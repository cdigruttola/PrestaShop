<?php
/**
 * @package   gm_omniprice
 * @author    Dariusz Tryba (contact@greenmousestudio.com)
 * @copyright Copyright (c) Green Mouse Studio (http://www.greenmousestudio.com)
 * @license   http://greenmousestudio.com/paid-license.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Gm_OmniPrice extends Module
{
    protected $ignoredGroups = [];
    protected $batchSize = 100;
    protected $ignoreCountries = false;
    protected $ignoreCombinations = false;
    protected $reindexOnSave = false;
    protected $showIfNotEnoughHistoricalData = false;
    protected $textColor = '';
    protected $priceColor = '';
    protected $backgroundColor = '';
    protected $showRealDiscount = false;
    protected $indexInactive = false;
    protected $activeMap = null;
    protected $daysBack = 30;
    protected $defaultShopId;
    protected $defaultCountryId;
    protected $defaultGroupId;
    protected $defaultCurrencyId;
    protected $today;
    protected $yesterday;
    protected $groupNames = [];

    public function __construct()
    {
        $this->name = 'gm_omniprice';
        $this->prefix = strtoupper($this->name);
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'GreenMouseStudio.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('OmniPrice - Omnibus Directive price compliancy');
        $this->description = $this->l('Displays lowest price before current promotion for discounted products');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->getConfiguration();
    }

    public function getConfiguration()
    {
        $this->ignoredGroups = explode(',', Configuration::get($this->prefix.'_GROUPS'));
        $this->daysBack = Configuration::get($this->prefix.'_DAYS');
        $this->batchSize = Configuration::get($this->prefix.'_BATCH');
        $this->ignoreCountries = Configuration::get($this->prefix.'_IGNORE_COUNTRIES');
        $this->ignoreCombinations = Configuration::get($this->prefix.'_IGNORE_COMBINATIONS');
        $this->reindexOnSave = Configuration::get($this->prefix.'_REINDEX');
        $this->textColor = Configuration::get($this->prefix.'_TEXT_COLOR');
        $this->priceColor = Configuration::get($this->prefix.'_PRICE_COLOR');
        $this->backgroundColor = Configuration::get($this->prefix.'_BG_COLOR');
        $this->showIfNotEnoughHistoricalData = Configuration::get($this->prefix.'_SHOW_IF_NO_HISTORY');
        $this->showRealDiscount = Configuration::get($this->prefix.'_SHOW_REAL_DISCOUNT');
        $this->indexInactive = Configuration::get($this->prefix.'_INDEX_INACTIVE');

        $this->defaultShopId = Configuration::get('PS_SHOP_DEFAULT');
        $this->defaultCountryId = Configuration::get('PS_COUNTRY_DEFAULT');
        $this->defaultGroupId = Configuration::get('PS_CUSTOMER_GROUP');
        $this->defaultCurrencyId = Configuration::get('PS_CURRENCY_DEFAULT');
        $this->today = date('Y-m-d');
        $this->yesterday = date('Y-m-d', strtotime("-1 days"));
    }

    public function install()
    {
        if (parent::install() && $this->installDb() &&
            $this->registerHook('displayProductPriceBlock') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('actionObjectSpecificPriceAddAfter') &&
            $this->registerHook('actionObjectSpecificPriceUpdateAfter') &&
            $this->registerHook('actionObjectSpecificPriceDeleteAfter')
        ) {
            Configuration::updateValue($this->prefix.'_DAYS', 30);
            Configuration::updateValue($this->prefix.'_BATCH', 100);
            Configuration::updateValue($this->prefix.'_IGNORE_COUNTRIES', true);
            Configuration::updateValue($this->prefix.'_REINDEX', true);
            Configuration::updateValue($this->prefix.'_SHOW_IF_NO_HISTORY', false);
            Configuration::updateValue($this->prefix.'_SHOW_REAL_DISCOUNT', false);
            Configuration::updateValue($this->prefix.'_INDEX_INACTIVE', false);
            Configuration::updateValue($this->prefix.'_TEXT_COLOR', '#666666');
            Configuration::updateValue($this->prefix.'_PRICE_COLOR', '#666666');
            Configuration::updateValue($this->prefix.'_BG_COLOR', '#FFFFFF');
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->registerHook('displayHeader');
            } else {
                $this->registerHook('actionFrontControllerSetMedia');
            }
            $this->autoConfig();
            return true;
        }
        return false;
    }

    public function autoConfig()
    {
        $combinationsHaveDiscountsOrImpacts = $this->getCombinationsDiscountsInfo() || $this->getCombinationsPriceImpactsInfo();
        Configuration::updateValue($this->prefix.'_IGNORE_COMBINATIONS', !$combinationsHaveDiscountsOrImpacts);
        if (!defined('_TB_VERSION_')) { //TB has a nasty bug here
            $groupsToSafelyIgnore = $this->findGroupsToSafelyIgnore();
            Configuration::updateValue($this->prefix.'_GROUPS', implode(',', $groupsToSafelyIgnore));
        }
    }

    public function installDb()
    {
        return Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gm_omniprice_history` (
            `date` DATE NOT NULL,
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
			`id_product_attribute` INT(10) UNSIGNED NOT NULL,
			`id_currency` INT(10) UNSIGNED NOT NULL,
			`id_country` INT(10) UNSIGNED NOT NULL,
			`id_group` INT(10) UNSIGNED NOT NULL,
			`price_tex` DECIMAL(20,6),
			`price_tin` DECIMAL(20,6),
            `is_specific_price` TINYINT(1),
			INDEX (`date`, `id_shop`, `id_product`)
		) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;') &&
            Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gm_omniprice_cache` (
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
			`id_product_attribute` INT(10) UNSIGNED NOT NULL,
			`id_currency` INT(10) UNSIGNED NOT NULL,
			`id_country` INT(10) UNSIGNED NOT NULL,
			`id_group` INT(10) UNSIGNED NOT NULL,
			`price_tex` DECIMAL(20,6),
			`price_tin` DECIMAL(20,6),
            `date` DATE NOT NULL,
			INDEX (`id_shop`, `id_product`, `id_product_attribute`, `id_currency`, `id_country`, `id_group`, `date`)
		) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;') &&
            Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'gm_omniprice_index` (
            `date` DATE NOT NULL,
			`id_shop` INT(10) UNSIGNED NOT NULL,
			`id_product` INT(10) UNSIGNED NOT NULL,
                        INDEX (`date`, `id_shop`)
		) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;');
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        if (!$this->uninstallDB() ||
            !Configuration::deleteByName($this->prefix.'_GROUPS') ||
            !Configuration::deleteByName($this->prefix.'_DAYS') ||
            !Configuration::deleteByName($this->prefix.'_BATCH') ||
            !Configuration::deleteByName($this->prefix.'_BG_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_TEXT_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_PRICE_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_IGNORE_COUNTRIES') ||
            !Configuration::deleteByName($this->prefix.'_IGNORE_COMBINATIONS') ||
            !Configuration::deleteByName($this->prefix.'_TEXT_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_PRICE_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_BG_COLOR') ||
            !Configuration::deleteByName($this->prefix.'_SHOW_IF_NO_HISTORY') ||
            !Configuration::deleteByName($this->prefix.'_SHOW_REAL_DISCOUNT') ||
            !Configuration::deleteByName($this->prefix.'_INDEX_INACTIVE') ||
            !Configuration::deleteByName($this->prefix.'_REINDEX')
        ) {
            return false;
        }
        return true;
    }

    protected function uninstallDb()
    {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'gm_omniprice_history`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'gm_omniprice_cache`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'gm_omniprice_index`');
        return $res;
    }

    public function getContent()
    {
        $content = '';
        $content .= $this->postProcess();
        $content .= $this->displayForm();
        $content .= $this->displayInfo();
        $content .= $this->displayInformationPanel();
        return $content;
    }

    protected function postProcess()
    {
        $output = '';
        if (Tools::isSubmit('submit'.$this->name)) {
            $this->ignoredGroups = Tools::getValue('groupBox');
            $groupsString = implode(',', $this->ignoredGroups);
            Configuration::updateValue($this->prefix.'_GROUPS', $groupsString);

            $this->daysBack = Tools::getValue($this->prefix.'_DAYS');
            Configuration::updateValue($this->prefix.'_DAYS', $this->daysBack);

            $this->batchSize = Tools::getValue($this->prefix.'_BATCH');
            Configuration::updateValue($this->prefix.'_BATCH', $this->batchSize);

            $this->ignoreCountries = Tools::getValue($this->prefix.'_IGNORE_COUNTRIES');
            Configuration::updateValue($this->prefix.'_IGNORE_COUNTRIES', $this->ignoreCountries);

            $this->ignoreCombinations = Tools::getValue($this->prefix.'_IGNORE_COMBINATIONS');
            Configuration::updateValue($this->prefix.'_IGNORE_COMBINATIONS', $this->ignoreCombinations);

            $this->reindexOnSave = Tools::getValue($this->prefix.'_REINDEX');
            Configuration::updateValue($this->prefix.'_REINDEX', $this->reindexOnSave);

            $this->textColor = Tools::getValue($this->prefix.'_TEXT_COLOR');
            Configuration::updateValue($this->prefix.'_TEXT_COLOR', $this->textColor);

            $this->priceColor = Tools::getValue($this->prefix.'_PRICE_COLOR');
            Configuration::updateValue($this->prefix.'_PRICE_COLOR', $this->priceColor);

            $this->backgroundColor = Tools::getValue($this->prefix.'_BG_COLOR');
            Configuration::updateValue($this->prefix.'_BG_COLOR', $this->backgroundColor);

            $this->showIfNotEnoughHistoricalData = Tools::getValue($this->prefix.'_SHOW_IF_NO_HISTORY');
            Configuration::updateValue($this->prefix.'_SHOW_IF_NO_HISTORY', $this->showIfNotEnoughHistoricalData);

            $this->showRealDiscount = Tools::getValue($this->prefix.'_SHOW_REAL_DISCOUNT');
            Configuration::updateValue($this->prefix.'_SHOW_REAL_DISCOUNT', $this->showRealDiscount);

            $this->indexInactive = Tools::getValue($this->prefix.'_INDEX_INACTIVE');
            Configuration::updateValue($this->prefix.'_INDEX_INACTIVE', $this->indexInactive);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output;
    }

    public function displayForm()
    {
        $helper = new HelperForm();
        $groups = Group::getGroups($this->context->language->id);
        $inputs = array(
            array(
                'type' => 'text',
                'label' => $this->l('Period'),
                'desc' => $this->l('Number of days before promotion start to analyze'),
                'name' => $this->prefix.'_DAYS',
                'class' => 'fixed-width-md',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Ignore countries'),
                'name' => $this->prefix.'_IGNORE_COUNTRIES',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('Analyze prices only for the default country, customers from other countries will see prices of the default country'),
                'desc' => $this->l('Analyze prices only for the default country, customers from other countries will see prices of the default country')
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Ignore combinations'),
                'name' => $this->prefix.'_IGNORE_COMBINATIONS',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('Analyze prices only for the default combination, recommended if combinations don\'t have price impacts'),
                'desc' => $this->l('Analyze prices only for the default combination, recommended if combinations don\'t have price impacts')
            ),
            array(
                'type' => 'group',
                'label' => $this->l('Ignored groups'),
                'name' => 'groupBox',
                'values' => $groups,
                'hint' => $this->l('Ignore selected groups, customers from ignored groups will see prices for the default group (Customer), recommended if no group discounts in use'),
                'desc' => $this->l('Ignore selected groups, customers from ignored groups will see prices for the default group (Customer), recommended if no group discounts in use')
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Batch size'),
                'desc' => $this->l('Number of products to process in a single CRON task run'),
                'name' => $this->prefix.'_BATCH',
                'class' => 'fixed-width-md',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Reindex on product save'),
                'name' => $this->prefix.'_REINDEX',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('Reindex product on save'),
                'desc' => $this->l('Reindex product on save')
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background color'),
                'name' => $this->prefix.'_BG_COLOR',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Text color'),
                'name' => $this->prefix.'_TEXT_COLOR',
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Price color'),
                'name' => $this->prefix.'_PRICE_COLOR',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show label even if not enough history'),
                'name' => $this->prefix.'_SHOW_IF_NO_HISTORY',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('For discounted products, if previous price is unknown, shows the current discounted price as the lowest one'),
                'desc' => $this->l('For discounted products, if previous price is unknown, shows the current discounted price as the lowest one')
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Index inactive products'),
                'name' => $this->prefix.'_INDEX_INACTIVE',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('Store price history even if the product is not active'),
                'desc' => $this->l('Store price history even if the product is not active'),
            ),
        );
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $inputs[] = array(
                'type' => 'switch',
                'label' => $this->l('Show real discount from the previous price'),
                'name' => $this->prefix.'_SHOW_REAL_DISCOUNT',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
                'hint' => $this->l('Display price change percentage after the lowest previous price'),
                'desc' => $this->l('Display price change percentage after the lowest previous price')
            );
        }
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach ($groups as $group) {
            $helper->fields_value['groupBox_'.$group['id_group']] = in_array($group['id_group'], $this->ignoredGroups);
        }
        $helper->fields_value[$this->prefix.'_DAYS'] = $this->daysBack;
        $helper->fields_value[$this->prefix.'_BATCH'] = $this->batchSize;
        $helper->fields_value[$this->prefix.'_IGNORE_COUNTRIES'] = $this->ignoreCountries;
        $helper->fields_value[$this->prefix.'_IGNORE_COMBINATIONS'] = $this->ignoreCombinations;
        $helper->fields_value[$this->prefix.'_REINDEX'] = $this->reindexOnSave;
        $helper->fields_value[$this->prefix.'_TEXT_COLOR'] = $this->textColor;
        $helper->fields_value[$this->prefix.'_PRICE_COLOR'] = $this->priceColor;
        $helper->fields_value[$this->prefix.'_BG_COLOR'] = $this->backgroundColor;
        $helper->fields_value[$this->prefix.'_SHOW_REAL_DISCOUNT'] = $this->showRealDiscount;
        $helper->fields_value[$this->prefix.'_INDEX_INACTIVE'] = $this->indexInactive;
        $helper->fields_value[$this->prefix.'_SHOW_IF_NO_HISTORY'] = $this->showIfNotEnoughHistoricalData;

        return $helper->generateForm(array($fieldsForm));
    }

    public function savePrices($verbose = false, $productId = null)
    {
        $this->clearIndex($this->yesterday);
        $output = '';
        $usetax = true;
        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }
        $basicPrices = [];
        $stateId = 0;
        $zipcode = '';

        $output .= $this->today.'<br/>';
        $output .= $this->l('Batch size').': '.$this->batchSize.'<br/>';
        $output .= $this->l('Default country ID:').' '.$this->defaultCountryId.'<br/>';
        $output .= $this->l('Default group ID:').' '.$this->defaultGroupId.'<br/>';

        $shopIds = $this->getShopsIds();
        $useReduction = true;
        if (Tools::isSubmit('init')) {
            $useReduction = false;
        }
        $specificPriceOutput = null;
        foreach ($shopIds as $shopId) {
            $currencyIds = $this->getCurrencyIds($shopId);
            $countryIds = $this->getCountryIds($shopId);
            $groupIds = $this->getGroupIds($shopId);
            $attributesMap = $this->getProductAttributeMap($shopId);
            if (!$productId) {
                $productIds = $this->getProductIds($shopId);
            } else {
                if (!$this->indexInactive && !$this->productIsActive($productId, $shopId)) {
                    continue;
                }
                $productIds = [$productId];
            }
            $output .= '<h4>'.$this->l('Shop ID:').' '.$shopId.'</h4>';
            if (count($productIds) < 1) {
                $output .= '<p>'.$this->l('All products indexed').'</p>';
                continue;
            } else {
                $output .= '<p>'.$this->l('Not finished yet, please run me again').'</p>';
            }
            $output .= '<table border="1"><tr>'
                .'<th></th>'
                .'<th>'.$this->l('Product ID').'</th>'
                .'<th>'.$this->l('Attribute ID').'</th>'
                .'<th>'.$this->l('Country ID').'</th>'
                .'<th>'.$this->l('Currency ID').'</th>'
                .'<th>'.$this->l('Group ID').'</th>'
                .'<th>'.$this->l('Price').'</th>'
                .'<th>'.$this->l('Previous price').'</th>'
                .'<th>'.$this->l('Is discounted').'</th>'
                .'<th>'.$this->l('Action').'</th>'
                .'<th>'.$this->l('Lowest price').'</th>'
                .'</tr>';
            $counter = 0;
            foreach ($currencyIds as $currencyId) {
                foreach ($countryIds as $countryId) {
                    foreach ($groupIds as $groupId) {
                        $discountedIds = $this->getDiscountedProductIds($shopId, $currencyId, $countryId, $groupId);
                        foreach ($productIds as $productId) {
                            $attributeId = 0;
                            $basicKey = $shopId.'-'.$productId.'-'.$attributeId.'-'.$currencyId.'-'.$countryId.'-'.$groupId;
                            $priceTin = Product::priceCalculation(
                                    $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                    $usetax, 6, //decimals
                                    false, //only_reduc
                                    $useReduction, //use_reduc
                                    true, //with_ecotax
                                    $specificPriceOutput, true //use_group_reduction
                            );
                            $priceTin = sprintf("%.6f", $priceTin);
                            $basicPrices[$basicKey] = $priceTin;
                            $priceTex = $priceTin;
                            if ($usetax) {
                                $priceTex = Product::priceCalculation(
                                        $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                        false, //no tax
                                        6, //decimals
                                        false, //only_reduc
                                        $useReduction, //use_reduc
                                        true, //with_ecotax
                                        $specificPriceOutput, true //use_group_reduction
                                );
                            }
                            $previously = $this->getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                            if ($previously) {
                                $previousPrice = (float) $previously['price_tin'];
                                $previousDiscount = (bool) $previously['is_specific_price'];
                            } else {
                                $previousPrice = 0;
                                $previousDiscount = false;
                            }

                            $onDiscount = $this->checkIfProductIsDiscounted($discountedIds, $productId, $attributeId);
                            $output .= '<tr>'
                                .'<td>'.++$counter.'</td>'
                                .'<td>'.$productId.'</td>'
                                .'<td>'.$attributeId.'</td>'
                                .'<td>'.$countryId.'</td>'
                                .'<td>'.$currencyId.'</td>'
                                .'<td>'.$groupId.'</td>'
                                .'<td>'.$priceTin.' ('.$priceTex.') </td>'
                                .'<td>'.$previousPrice.'</td>'
                                .'<td>'.($onDiscount ? $this->l('Yes') : $this->l('No')).'</td>';
                            $priceIsCorrect = ($priceTin > 0);
                            $priceChanged = (abs($previousPrice - $priceTin) > 0.01);
                            $discountChanged = ($previousDiscount != $onDiscount);
                            if (Tools::isSubmit('cache')) {
                                $discountChanged = true;
                            }
                            if ($priceIsCorrect && ($priceChanged || $discountChanged)) {
                                $output .= '<td>'.$this->l('Save').'</td>';
                                $this->savePrice($this->today, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex,
                                    $priceTin, $onDiscount);
                                //calculate lowest price and add it to the cache
                                if ($onDiscount) {
                                    $lowestPrices = $this->getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                    if ($lowestPrices) {
                                        $output .= '<td>'.$lowestPrices['price_tin'].' ('.$lowestPrices['price_tex'].')</td>';
                                        $this->saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                            $lowestPrices['price_tex'], $lowestPrices['price_tin'], $lowestPrices['date']);
                                    } else {
                                        $output .= '<td>'.$this->l('Unknown').'<</td>';
                                    }
                                } else {
                                    $output .= '<td>'.$this->l('Not applicable').'</td>';
                                }
                            } else {
                                $output .= '<td>'.$this->l('No change').'</td>';
                                $output .= '<td>'.$this->l('No change').'</td>';
                            }
                            if (!$onDiscount) {
                                $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                            }
                            $output .= '</tr>';
                            //attributes
                            if (array_key_exists($productId, $attributesMap)) {
                                foreach ($attributesMap[$productId] as $attributeId) {
                                    $priceTin = Product::priceCalculation(
                                            $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId, 1, //quantity
                                            $usetax, 6, //decimals
                                            false, //only_reduc
                                            $useReduction, //use_reduc
                                            true, //with_ecotax
                                            $specificPriceOutput, true //use_group_reduction
                                    );
                                    $priceTin = sprintf("%.6f", $priceTin);
                                    $priceTex = $priceTin;
                                    if ($usetax) {
                                        $priceTex = Product::priceCalculation(
                                                $shopId, $productId, $attributeId, $countryId, $stateId, $zipcode, $currencyId, $groupId,
                                                1, //quantity
                                                false, //no tax
                                                6, //decimals
                                                false, //only_reduc
                                                $useReduction, //use_reduc
                                                true, //with_ecotax
                                                $specificPriceOutput, true //use_group_reduction
                                        );
                                    }
                                    if (abs($priceTin - $basicPrices[$basicKey]) > 0.01) {
                                        $previously = $this->getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                        if ($previously) {
                                            $previousPrice = (float) $previously['price_tin'];
                                            $previousDiscount = (bool) $previously['is_specific_price'];
                                        } else {
                                            $previousPrice = 0;
                                            $previousDiscount = false;
                                        }
                                        $onDiscount = $this->checkIfProductIsDiscounted($discountedIds, $productId, $attributeId);
                                        $output .= '<tr>'
                                            .'<td>'.++$counter.'</td>'
                                            .'<td>'.$productId.'</td>'
                                            .'<td>'.$attributeId.'</td>'
                                            .'<td>'.$countryId.'</td>'
                                            .'<td>'.$currencyId.'</td>'
                                            .'<td>'.$groupId.'</td>'
                                            .'<td>'.$priceTin.' ('.$priceTex.') </td>'
                                            .'<td>'.$previousPrice.'</td>'
                                            .'<td>'.($onDiscount ? $this->l('Yes') : $this->l('No')).'</td>';
                                        $priceIsCorrect = ($priceTin > 0);
                                        $priceChanged = (abs($previousPrice - $priceTin) > 0.01);
                                        $discountChanged = ($previousDiscount != $onDiscount);
                                        if (Tools::isSubmit('cache')) {
                                            $discountChanged = true;
                                        }
                                        if ($priceIsCorrect && ($priceChanged || $discountChanged)) {
                                            $output .= '<td>'.$this->l('Save').'</td>';
                                            $this->savePrice($this->today, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                                $priceTex, $priceTin, $onDiscount);
                                            if ($onDiscount) {
                                                $lowestPrices = $this->getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId,
                                                    $attributeId);
                                                if ($lowestPrices) {
                                                    $output .= '<td>'.$lowestPrices['price_tin'].' ('.$lowestPrices['price_tex'].')</td>';
                                                    $this->saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId,
                                                        $lowestPrices['price_tex'], $lowestPrices['price_tin'], $lowestPrices['date']);
                                                } else {
                                                    $output .= '<td>'.$this->l('Unknown').'</td>';
                                                }
                                            } else {
                                                $output .= '<td>'.$this->l('Not applicable').'</td>';
                                            }
                                        } else {
                                            $output .= '<td>'.$this->l('No change').'</td>';
                                            $output .= '<td>'.$this->l('No change').'</td>';
                                        }
                                        if (!$onDiscount) {
                                            $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                        }
                                    } else {
                                        //skip analyzing attribute if price is the same as basic
                                        //delete if the attribute is not on discount
                                        $onDiscount = $this->checkIfProductIsDiscounted($discountedIds, $productId, $attributeId);
                                        if (!$onDiscount) {
                                            $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
                                        }
                                    }
                                }
                            }
                            $this->addProductToIndex($shopId, $productId, $this->today);
                        }
                    }
                }
            }
            $output .= '</table>';
        }
        if ($verbose) {
            echo $output;
        }
        return true;
    }

    public function getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $daysOffset = 0)
    {
        $lowestPriceTin = INF;
        $lowestPriceTex = INF;
        $lowestDate = '0000-00-00';
        for ($d = 1; $d <= $this->daysBack; $d++) {
            $days = $d + $daysOffset;
            $date = date('Y-m-d', strtotime("-$days days"));
            $row = Db::getInstance()->getRow('SELECT `price_tin`, `price_tex` '
                .' FROM `'._DB_PREFIX_.'gm_omniprice_history` '
                .' WHERE `id_shop` = '.$shopId
                .' AND `id_product` = '.$productId
                .' AND `id_product_attribute` = '.$attributeId
                .' AND `id_currency` = '.$currencyId
                .' AND `id_group` = '.$groupId
                .' AND `id_country` = '.$countryId
                .' AND `date` <= \''.$date.'\''
                .' ORDER BY `date` DESC'
            );
            if ($attributeId != 0 && $row == false) {
                $attributeId = 0;
                $row = Db::getInstance()->getRow('SELECT `price_tin`, `price_tex` '
                    .' FROM `'._DB_PREFIX_.'gm_omniprice_history` '
                    .' WHERE `id_shop` = '.$shopId
                    .' AND `id_product` = '.$productId
                    .' AND `id_product_attribute` = '.$attributeId
                    .' AND `id_currency` = '.$currencyId
                    .' AND `id_group` = '.$groupId
                    .' AND `id_country` = '.$countryId
                    .' AND `date` <= \''.$date.'\''
                    .' ORDER BY `date` DESC'
                );
            }
            if ($row) {
                $priceTin = $row['price_tin'];
                if ($priceTin < $lowestPriceTin) {
                    $lowestPriceTin = $priceTin;
                }
                $priceTex = $row['price_tex'];
                if ($priceTex < $lowestPriceTex) {
                    $lowestPriceTex = $priceTex;
                    $lowestDate = $date;
                }
            } else {
                break;
            }
        }
        if ($lowestPriceTex < INF) {
            return [
                'price_tin' => $lowestPriceTin,
                'price_tex' => $lowestPriceTex,
                'date' => $lowestDate
            ];
        } else {
            return false;
        }
    }

    public function checkIfProductIsDiscounted($discountedIds, $productId, $attributeId)
    {
        if (Tools::isSubmit('init')) {
            return false;
        }
        foreach ($discountedIds as $item) {
            if (($item['id_product'] == $productId) && ($item['id_product_attribute'] == $attributeId)) {
                return true;
            }
            if (($item['id_product'] == $productId) && ($item['id_product_attribute'] == 0)) {
                return true;
            }
        }
        return false;
    }

    public function clearIndex($date)
    {
        return Db::getInstance()->delete('gm_omniprice_index', '`date` <= \''.$date.'\'');
    }

    public function addProductToIndex($shopId, $productId, $date)
    {
        Db::getInstance()->insert('gm_omniprice_index',
            [
                'date' => $date,
                'id_shop' => $shopId,
                'id_product' => $productId
        ]);
    }

    public function getPreviousPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId)
    {
        return Db::getInstance()->getRow('SELECT `price_tin`, `is_specific_price` FROM `'._DB_PREFIX_.'gm_omniprice_history`'
                .' WHERE `id_shop` = '.$shopId.' AND `id_product` = '.$productId
                .' AND `id_currency` = '.$currencyId.' AND `id_country` = '.$countryId
                .' AND `id_group` = '.$groupId.' AND `id_product_attribute` = '.$attributeId
                .' AND `date` < \''.$this->today.'\''
                .' ORDER BY `date` DESC');
    }

    public function saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex, $priceTin, $date)
    {
        $this->deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
        return Db::getInstance()->insert('gm_omniprice_cache',
                [
                    'id_shop' => $shopId,
                    'id_product' => $productId,
                    'id_currency' => $currencyId,
                    'id_country' => $countryId,
                    'id_group' => $groupId,
                    'id_product_attribute' => $attributeId,
                    'price_tex' => $priceTex,
                    'price_tin' => $priceTin,
                    'date' => $date
        ]);
    }

    public function deleteLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId)
    {
        return Db::getInstance()->delete('gm_omniprice_cache',
                '`id_shop` = '.$shopId
                .' AND `id_product` = '.$productId
                .' AND `id_currency` = '.$currencyId
                .' AND `id_country` = '.$countryId
                .' AND `id_group` = '.$groupId
                .' AND `id_product_attribute` = '.$attributeId
        );
    }

    public function savePrice($date, $shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $priceTex, $priceTin, $onDiscount = false)
    {
        if (Tools::isSubmit('cache')) {
            $this->deleteTodaysPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId);
        }
        Db::getInstance()->insert('gm_omniprice_history',
            [
                'date' => $date,
                'id_shop' => $shopId,
                'id_product' => $productId,
                'id_currency' => $currencyId,
                'id_country' => $countryId,
                'id_group' => $groupId,
                'id_product_attribute' => $attributeId,
                'price_tex' => $priceTex,
                'price_tin' => $priceTin,
                'is_specific_price' => $onDiscount
        ]);
    }

    public function deleteTodaysPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId)
    {
        return Db::getInstance()->delete('gm_omniprice_history',
                '`id_shop` = '.$shopId
                .' AND `id_product` = '.$productId
                .' AND `id_currency` = '.$currencyId
                .' AND `id_country` = '.$countryId
                .' AND `id_group` = '.$groupId
                .' AND `id_product_attribute` = '.$attributeId
                .' AND `date` = \''.$this->today.'\''
        );
    }

    public function getGroupIds($shopId)
    {
        $ids = [$this->defaultGroupId];
        if (!Group::isFeatureActive()) {
            return $ids;
        }
        $query = 'SELECT `gs`.`id_group`
                            FROM `'._DB_PREFIX_.'group_shop` `gs`
                            WHERE `gs`.`id_shop` = '.$shopId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                if (($row['id_group'] != $this->defaultGroupId) && !in_array($row['id_group'], $this->ignoredGroups)) {
                    $ids[] = (int) $row['id_group'];
                }
            }
        }
        return $ids;
    }

    public function getCountryIds($shopId)
    {
        $ids = [$this->defaultCountryId];
        if (Shop::isFeatureActive()) {
            $ids = [Configuration::get('PS_COUNTRY_DEFAULT', null, null, $shopId)];
        }
        if (!$this->ignoreCountries) {
            $query = 'SELECT `cs`.`id_country`
                            FROM `'._DB_PREFIX_.'country_shop` `cs`
                            LEFT JOIN `'._DB_PREFIX_.'country` `c` ON (`cs`.`id_country` = `c`.`id_country`)
                            WHERE `cs`.`id_shop` = '.$shopId
                .' AND `c`.`active` = 1';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    if (!in_array($row['id_country'], $ids)) {
                        $ids[] = (int) $row['id_country'];
                    }
                }
            }
        }
        return $ids;
    }

    public function getCurrencyIds($shopId)
    {
        $ids = [];
        $query = 'SELECT `cs`.`id_currency`
                            FROM `'._DB_PREFIX_.'currency` c
                            LEFT JOIN `'._DB_PREFIX_.'currency_shop` cs ON (cs.`id_currency` = c.`id_currency`)
                            WHERE cs.`id_shop` = '.(int) $shopId
            .' AND c.`active` = 1';
        $currencies = Db::getInstance()->executeS($query);
        foreach ($currencies as $currency) {
            $ids[] = (int) $currency['id_currency'];
        }
        return $ids;
    }

    public function getProductIds($shopId)
    {
        $productIds = [];
        $query = 'SELECT `ps`.`id_product` '
            .' FROM `'._DB_PREFIX_.'product_shop` `ps`'
            .' WHERE `ps`.`id_product` NOT IN '
            .' (SELECT `id_product` FROM `'._DB_PREFIX_.'gm_omniprice_index`'
            .'  WHERE `id_shop` = '.$shopId.' AND `date` = \''.$this->today.'\')'
            .($this->indexInactive ? ' ' : ' AND `ps`.`active` = 1 ')
            .' AND `ps`.`id_shop` = '.$shopId.' '
            . ' ORDER BY `ps`.`id_product` ASC LIMIT '.$this->batchSize;

        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $productIds[] = (int) $row['id_product'];
            }
        }
        return $productIds;
    }

    public function getProductAttributeMap($shopId)
    {
        $map = [];
        if (!$this->ignoreCombinations) {
            $query = 'SELECT `id_product`, `id_product_attribute` '
                .' FROM `'._DB_PREFIX_.'product_attribute_shop` '
                .' WHERE `id_shop` = '.$shopId;
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $map[(int) $row['id_product']][] = (int) $row['id_product_attribute'];
                }
            }
        }
        return $map;
    }

    public function getShopsIds()
    {
        $list = [];
        $sql = 'SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`
                WHERE `active` = 1 AND `deleted` = 0';
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($res) {
            foreach ($res as $row) {
                $list[] = (int) $row['id_shop'];
            }
        }
        return $list;
    }

    public function hookDisplayProductPriceBlock($hookParams)
    {
        if (($hookParams['type'] == 'after_price') &&
            ((isset($hookParams['product']->id)) || (isset($hookParams['product']['id_product'])) || Tools::isSubmit('id_product') )) {
            if (isset($hookParams['product']->id)) {
                $productId = (int) $hookParams['product']->id;
            } else if (isset($hookParams['product']['id_product'])) {
                $productId = (int) $hookParams['product']['id_product'];
            } else {
                $productId = (int) Tools::getValue('id_product');
            }
            $showRealDiscount = $this->showRealDiscount;
            if (Tools::isSubmit('omnipricetest')) {
                $lowestCachedPrice = [
                    'formatted' => Tools::getValue('omnipricetest'),
                    'raw' => 1
                ];
                $showRealDiscount = false;
            } else {
                $params = $this->getCurrentParams($productId);
                if (Tools::isSubmit('omnidebug')) {
                    var_export($params);
                }
                $lowestCachedPrice = $this->getLowestCachedPrice($params);
            }
            $realDiscount = '';
            if ($showRealDiscount && isset($hookParams['product']['price_amount'])) {
                $currentPrice = $hookParams['product']['price_amount'];
                $previousPrice = $lowestCachedPrice['raw'];
                $realDiscount = $this->calculateRealDisount($currentPrice, $previousPrice);
            }
            if (!$lowestCachedPrice) {
                //may have a promotion for an individual combination
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') && $this->hasAttributePrices($productId)) {
                    $lowestCachedPrice = [
                        'formatted' => '---',
                        'raw' => '0'
                    ];
                }
            }
            if ($lowestCachedPrice) {
                $this->context->smarty->assign(
                    [
                        'gm_omniprice_lowest' => $lowestCachedPrice['formatted'],
                        'gm_omniprice_days' => $this->daysBack,
                        'gm_omniprice_color' => $this->textColor,
                        'gm_omniprice_price_color' => $this->priceColor,
                        'gm_omniprice_background' => $this->backgroundColor,
                        'gm_omniprice_show_real_discount' => $showRealDiscount,
                        'gm_omniprice_real_discount' => $realDiscount
                    ]
                );
                return $this->display(__FILE__, 'price.tpl');
            }
        }
    }

    public function calculateRealDisount($currentPrice, $previousPrice)
    {
        if (!$currentPrice || !$previousPrice) {
            return false;
        }
        $realDiscount = '0%';
        if ($currentPrice < $previousPrice) {
            $discount = round((1 - $currentPrice / $previousPrice) * 100);
            $realDiscount = '-'.$discount.'%';
        }
        if ($currentPrice > $previousPrice) {
            $discount = round(($currentPrice / $previousPrice - 1) * 100);
            $realDiscount = '+'.$discount.'%';
        }
        return $realDiscount;
    }

    public function getLowestCachedPrice($params)
    {
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
        } else {
            $field = '`price_tin`';
        }
        $price = Db::getInstance()->getValue('SELECT  '.$field
            .' FROM `'._DB_PREFIX_.'gm_omniprice_cache`'
            .' WHERE `id_shop` = '.$params['id_shop']
            .' AND `id_product` = '.$params['id_product']
            .' AND `id_currency` = '.$params['id_currency']
            .' AND `id_country` = '.$params['id_country']
            .' AND `id_group` = '.$params['id_group']
            .' AND `id_product_attribute` = '.$params['id_product_attribute']
        );
        if ($price) {
            return [
                'formatted' => $this->getFormattedPrice($price),
                'raw' => $price
            ];
        } else if ($params['id_product_attribute'] != 0) {
            $price = Db::getInstance()->getValue('SELECT  '.$field
                .' FROM `'._DB_PREFIX_.'gm_omniprice_cache`'
                .' WHERE `id_shop` = '.$params['id_shop']
                .' AND `id_product` = '.$params['id_product']
                .' AND `id_currency` = '.$params['id_currency']
                .' AND `id_country` = '.$params['id_country']
                .' AND `id_group` = '.$params['id_group']
                .' AND `id_product_attribute` = 0'
            );
            if ($price) {
                return [
                    'formatted' => $this->getFormattedPrice($price),
                    'raw' => $price
                ];
            }
        }
        if (!$price) {
            if ($this->showIfNotEnoughHistoricalData) {
                return $this->getLatestHistoricalPrice($params);
            }
        }
        return false;
    }

    protected function getLatestHistoricalPrice($params)
    {
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
            $arrayField = 'price_tex';
        } else {
            $field = '`price_tin`';
            $arrayField = 'price_tin';
        }
        $prices = Db::getInstance()->executeS('SELECT  '.$field.', `is_specific_price`'
            .' FROM `'._DB_PREFIX_.'gm_omniprice_history`'
            .' WHERE `id_shop` = '.$params['id_shop']
            .' AND `id_product` = '.$params['id_product']
            .' AND `id_currency` = '.$params['id_currency']
            .' AND `id_country` = '.$params['id_country']
            .' AND `id_group` = '.$params['id_group']
            .' AND `id_product_attribute` = '.$params['id_product_attribute']
        );
        if ((count($prices) == 1) && ($prices[0]['is_specific_price'])) {
            return
                [
                    'formatted' => $this->getFormattedPrice($prices[0][$arrayField]),
                    'raw' => $prices[0][$arrayField]
            ];
        } else if ($params['id_product_attribute'] != 0) {
            $prices = Db::getInstance()->executeS('SELECT  '.$field.', `is_specific_price`'
                .' FROM `'._DB_PREFIX_.'gm_omniprice_history`'
                .' WHERE `id_shop` = '.$params['id_shop']
                .' AND `id_product` = '.$params['id_product']
                .' AND `id_currency` = '.$params['id_currency']
                .' AND `id_country` = '.$params['id_country']
                .' AND `id_group` = '.$params['id_group']
                .' AND `id_product_attribute` = 0'
            );
            if ((count($prices) == 1) && ($prices[0]['is_specific_price'])) {
                return
                    [
                        'formatted' => $this->getFormattedPrice($prices[0][$arrayField]),
                        'raw' => $prices[0][$arrayField]
                ];
            }
        }
        return false;
    }

    public function getLowestCachedPricesForCombinations($params)
    {
        $prices = [];
        $displayMethod = Group::getPriceDisplayMethod($params['id_group']);
        if ($displayMethod) {
            $field = '`price_tex`';
        } else {
            $field = '`price_tin`';
        }
        $result = Db::getInstance()->executeS('SELECT  '.$field.' AS `price`, `id_product_attribute` '
            .' FROM `'._DB_PREFIX_.'gm_omniprice_cache`'
            .' WHERE `id_shop` = '.$params['id_shop']
            .' AND `id_product` = '.$params['id_product']
            .' AND `id_currency` = '.$params['id_currency']
            .' AND `id_country` = '.$params['id_country']
            .' AND `id_group` = '.$params['id_group']
        );
        if ($result) {
            foreach ($result as $row) {
                $prices[$row['id_product_attribute']] = $this->getFormattedPrice($row['price']);
            }
        } else {
            if ($this->showIfNotEnoughHistoricalData) {
                $result = Db::getInstance()->executeS('SELECT  '.$field.' AS `price`, `id_product_attribute` '
                    .' FROM `'._DB_PREFIX_.'gm_omniprice_history`'
                    .' WHERE `id_shop` = '.$params['id_shop']
                    .' AND `id_product` = '.$params['id_product']
                    .' AND `id_currency` = '.$params['id_currency']
                    .' AND `id_country` = '.$params['id_country']
                    .' AND `id_group` = '.$params['id_group']
                    .' AND `is_specific_price` = 1 '
                );
                if ($result) {
                    foreach ($result as $row) {
                        $prices[$row['id_product_attribute']] = $this->getFormattedPrice($row['price']);
                    }
                }
            }
        }
        return $prices;
    }

    public function getFormattedPrice($price)
    {
        $context = Context::getContext();
        if (isset($context->currentLocale)) {
            return $context->currentLocale->formatPrice($price, $context->currency->iso_code);
        } else {
            return Tools::displayPrice($price);
        }
    }

    public function hasAttributePrices($productId)
    {
        $res = Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'gm_omniprice_cache` '
            .' WHERE `id_product` = '.$productId.' AND `id_product_attribute` > 0');
        if ($res == $productId) {
            return true;
        }
        if ($this->showIfNotEnoughHistoricalData) {
            $res = Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'gm_omniprice_history` '
                .' WHERE `id_product` = '.$productId.' AND `id_product_attribute` > 0 AND `is_specific_price` = 1');
            if ($res == $productId) {
                return true;
            }
        }
        return false;
    }

    public function getCurrentParams($productId)
    {
        $params = [];
        $params['id_shop'] = (int) $this->context->shop->id;
        $params['id_currency'] = (int) $this->context->currency->id;
        $params['id_product'] = (int) $productId;
        if ($this->ignoreCombinations) {
            $params['id_product_attribute'] = 0;
        } else {
            $params['id_product_attribute'] = $this->getIdProductAttribute($params['id_product']);
        }
        if ($this->ignoreCountries) {
            $params['id_country'] = $this->defaultCountryId;
            if (Shop::isFeatureActive()) {
                $params['id_country'] = Configuration::get('PS_COUNTRY_DEFAULT', null, null, $params['id_shop']);
            }
        } else {
            $params['id_country'] = $this->context->country->id;
        }
        $currentGroup = $this->context->customer->id_default_group;
        if (in_array($currentGroup, $this->ignoredGroups)) {
            $params['id_group'] = $this->defaultGroupId;
        } else {
            $params['id_group'] = $currentGroup;
        }
        return $params;
    }

    public function getDiscountedProductIds($shopId, $currencyId, $countryId, $groupId)
    {
        if ($this->globalRuleExists($shopId, $currencyId, $countryId, $groupId)) {
            return $this->getAllProductIdsFromShop($shopId);
        }
        $beginning = null;
        $ending = null;
        if (Tools::version_compare(_PS_VERSION_, '1.6.1.10', '<=')) {
            $now = date('Y-m-d H:i:00');
            $beginning = $now;
            $ending = $now;
        }
        $ids = SpecificPrice::getProductIdByDate($shopId, $currencyId, $countryId, $groupId, $beginning, $ending, 0, true);
        return $ids;
    }

    protected function getAllProductIdsFromShop($shopId)
    {
        $ids = [];
        $query = 'SELECT `id_product` FROM `'._DB_PREFIX_.'product_shop` WHERE `id_shop` = '.$shopId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $ids[] = [
                    'id_product' => (int) $row['id_product'],
                    'id_product_attribute' => 0
                ];
            }
        }
        return $ids;
    }

    public function globalRuleExists($shopId, $currencyId, $countryId, $groupId)
    {
        $query = 'SELECT `id_specific_price` FROM `'._DB_PREFIX_.'specific_price` '
            .' WHERE (`id_shop` = 0 OR `id_shop` = '.$shopId.') '
            .' AND (`id_currency` = 0 OR `id_currency` = '.$currencyId.') '
            .' AND (`id_country` = 0 OR `id_country` = '.$countryId.') '
            .' AND (`id_group` = 0 OR `id_group` = '.$groupId.') '
            .' AND (`from` <= NOW() OR `from` = \'0000-00-00 00:00:00\') '
            .' AND (`to` >= NOW() OR `to` = \'0000-00-00 00:00:00\' ) '
            .' AND `id_product` = 0 '
            .' AND `id_product_attribute` = 0 '
            .' AND `from_quantity` > 0 ';
        $result = (int) Db::getInstance()->getValue($query);
        return ($result > 0);
    }

    public function getIdProductAttribute($productId)
    {
        $idProductAttribute = $this->getIdProductAttributeByGroup($productId);
        if (null === $idProductAttribute) {
            $idProductAttribute = (int) Tools::getValue('id_product_attribute');
        }
        if (!$idProductAttribute) {
            $idProductAttribute = $this->getDefaultAttributeIdForProduct($productId);
        }
        return $idProductAttribute;
    }

    protected function getDefaultAttributeIdForProduct($productId)
    {
        $shopId = $this->context->shop->id;
        return (int) Db::getInstance()->getValue('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute_shop` '
                .' WHERE `default_on` = 1 AND `id_shop` = '.$shopId.' AND `id_product` = '.$productId);
    }

    protected function getIdProductAttributeByGroup($productId)
    {
        $groups = Tools::getValue('group');
        if (empty($groups)) {
            return null;
        }
        return (int) Product::getIdProductAttributeByIdAttributes(
                $productId, $groups, true
        );
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        $this->context->controller->registerStylesheet(
            'module-gm_omniprice-style', 'modules/'.$this->name.'/views/css/gm_omniprice.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
    }

    protected function displayInfo()
    {
        $token = Tools::getAdminToken($this->name);
        $output = '<div class="panel">'
            .'<div class="panel-heading"><i class="icon-link"></i> '
            .$this->l('Gathering price history')
            .'</div>';
        $output .= '<input type="text" size="90" value="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/cron.php?token='.$token.'"/>';
        $output .= '</div>';
        $output .= '<div class="panel">'
            .'<div class="panel-heading"><i class="icon-link"></i> '
            .$this->l('Cleaning old price history')
            .'</div>';
        $output .= '<input type="text" size="90" value="'.Tools::getHttpHost(true).__PS_BASE_URI__.'modules/'.$this->name.'/cleanup.php?token='.$token.'"/>';
        $output .= '</div>';
        return $output;
    }

    protected function displayInformationPanel()
    {
        $output = '<div class="panel">'
            .'<div class="panel-heading"><i class="icon-info"></i> '
            .$this->l('Information')
            .'</div>';
        if (!defined('_TB_VERSION_')) { //TB has a nasty bug here
            $output .= '<p>'.$this->l('Groups with no customers:').' '.implode(', ', $this->findEmptyGroups()).'</p>';
        }
        $output .= '<p>'.$this->l('Groups with group reductions:').' '.implode(', ', $this->findGroupsWithGroupReduction()).'</p>';
        $output .= '<p>'.$this->l('Groups with specific prices:').' '.implode(', ', $this->findGroupsWithSpecificPrices()).'</p>';
        $output .= '<p>'.$this->l('Groups with specific price rules:').' '.implode(', ', $this->findGroupsWithSpecifiPriceRules()).'</p>';
        $output .= '<p>'.$this->l('Products have combinations with price impacts:').' '.($this->getCombinationsPriceImpactsInfo() ? $this->l('Yes') : $this->l('No')).'</p>';
        $output .= '<p>'.$this->l('Individual combinations have discounts:').' '.($this->getCombinationsDiscountsInfo() ? $this->l('Yes') : $this->l('No')).'</p>';
        $output .= '<p>'.$this->l('Number of active countries:').' '.$this->countActiveCountries().'</p>';
        $output .= '<p>'.$this->l('Number of active currencies:').' '.$this->countActiveCurrencies().'</p>';
        $output .= '<p>'.$this->l('Number of prices stored in history:').' '.$this->countStoredPrices().'</p>';
        $output .= '</div>';
        return $output;
    }

    protected function countActiveCurrencies()
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(`id_currency`) FROM `'._DB_PREFIX_.'currency` WHERE `active` = 1');
    }

    protected function countActiveCountries()
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(`id_country`) FROM `'._DB_PREFIX_.'country` WHERE `active` = 1');
    }

    protected function countStoredPrices()
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(`id_product`) FROM `'._DB_PREFIX_.'gm_omniprice_history`');
    }

    protected function getCombinationsPriceImpactsInfo()
    {
        $query = 'SELECT `id_product` FROM `'._DB_PREFIX_.'product_attribute` WHERE `price` != 0';
        $res = Db::getInstance()->getValue($query);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    protected function getCombinationsDiscountsInfo()
    {
        $query = 'SELECT `id_product` FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product_attribute` > 0';
        $res = Db::getInstance()->getValue($query);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    protected function getGroupNames()
    {
        $langId = $this->context->language->id;
        $query = 'SELECT `id_group`, `name` FROM `'._DB_PREFIX_.'group_lang` WHERE `id_lang` = '.$langId;
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $this->groupNames[(int) $row['id_group']] = $row['name'];
            }
        }
    }

    protected function findGroupsToSafelyIgnore()
    {
        $groups = $this->findEmptyGroups(true);
        $guestGroupId = Configuration::get('PS_GUEST_GROUP');
        $unidentifiedGroupId = Configuration::get('PS_UNIDENTIFIED_GROUP');
        $groups[] = $guestGroupId;
        $groups[] = $unidentifiedGroupId;
        $groups = array_unique($groups);
        sort($groups);
        $ignoredGroups = [];
        foreach ($groups as $groupId) {
            if ($groupId != $this->defaultGroupId) {
                $ignoredGroups[] = $groupId;
            }
        }
        return $ignoredGroups;
    }

    protected function findEmptyGroups($returnIds = false)
    {
        $emptyGroups = [];
        $emptyIds = [];
        $res = Group::getGroups($this->context->language->id);
        foreach ($res as $row) {
            $group = new Group((int) $row['id_group']);
            $customerCount = $group->getCustomers(true);
            if ($customerCount < 1) {
                $emptyGroups[] = $row['name'];
                $emptyIds[] = $row['id_group'];
            }
        }
        if ($returnIds) {
            return $emptyIds;
        }
        if (!count($emptyGroups)) {
            return [$this->l('None')];
        }
        return $emptyGroups;
    }

    protected function findGroupsWithSpecifiPriceRules()
    {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `'._DB_PREFIX_.'specific_price_rule` WHERE `id_group` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function findGroupsWithSpecificPrices()
    {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `'._DB_PREFIX_.'specific_price` WHERE `id_group` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function findGroupsWithGroupReduction()
    {
        $groupIds = [];
        $query = 'SELECT `id_group` FROM `'._DB_PREFIX_.'group` WHERE `reduction` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $query = 'SELECT `id_group` FROM `'._DB_PREFIX_.'group_reduction` WHERE `reduction` > 0';
        $res = Db::getInstance()->executes($query);
        if ($res) {
            foreach ($res as $row) {
                $groupIds[] = (int) $row['id_group'];
            }
        }
        $groupIds = array_unique($groupIds);
        sort($groupIds);
        return $this->getGroupNamesForIds($groupIds);
    }

    protected function getGroupNamesForIds($groupIds)
    {
        if (!count($groupIds)) {
            return [$this->l('None')];
        }
        $names = [];
        $this->getGroupNames();
        foreach ($groupIds as $groupId) {
            $names[] = $this->groupNames[$groupId];
        }
        return $names;
    }

    public function hookActionProductUpdate($params)
    {
        $productId = $params['id_product'];
        $this->reindexProduct($productId);
    }

    public function hookActionObjectSpecificPriceAddAfter($params)
    {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function hookActionObjectSpecificPriceUpdateAfter($params)
    {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function hookActionObjectSpecificPriceDeleteAfter($params)
    {
        $sp = $params['object'];
        if ($sp->id_product) {
            $this->reindexProduct($sp->id_product);
        }
    }

    public function reindexProduct($productId)
    {
        $this->removeProductFromTodaysIndex($productId);
        $this->removeProductFromTodaysHistory($productId);
        if ($this->reindexOnSave) {
            $this->savePrices(false, $productId);
        }
    }

    public function resetIndex()
    {
        Db::getInstance()->delete('gm_omniprice_index');
        Db::getInstance()->delete('gm_omniprice_history', '`date` = \''.$this->today.'\'');
    }

    public function removeProductFromTodaysIndex($productId)
    {
        Db::getInstance()->delete('gm_omniprice_index', '`id_product` = '.$productId.' AND `date` = \''.$this->today.'\'');
    }

    public function removeProductFromTodaysHistory($productId)
    {
        Db::getInstance()->delete('gm_omniprice_history', '`id_product` = '.$productId.' AND `date` = \''.$this->today.'\'');
    }

    public function hookDisplayHeader($params)
    {
        if (Tools::isSubmit('id_product')) {
            $this->context->controller->addCSS($this->_path.'views/css/gm_omniprice.css', 'all');
            if (!$this->ignoreCombinations) {
                $params = $this->getCurrentParams((int) Tools::getValue('id_product'));
                $prices = $this->getLowestCachedPricesForCombinations($params);
                if (count($prices) > 0) {
                    $this->context->controller->addJS($this->_path.'views/js/gm_omniprice.js');
                    Media::addJsDef(['gm_omniprice_attr_prices' => $prices]);
                }
            }
        }
    }

    public function cleanUp($verbose = false)
    {
        $output = '';
        //general cleanup
        if (Tools::issubmit('zero')) {
            Db::getInstance()->delete('gm_omniprice_history', '`price_tin` < 0.001');
            Db::getInstance()->delete('gm_omniprice_cache', '`price_tin` < 0.001');
        }
        Db::getInstance()->delete('gm_omniprice_history', '`id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'product`)');
        Db::getInstance()->delete('gm_omniprice_cache', '`id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'product`)');
        Db::getInstance()->delete('gm_omniprice_history',
            '`id_product_attribute` > 0 AND `id_product_attribute` NOT IN (SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute`)');
        Db::getInstance()->delete('gm_omniprice_cache',
            '`id_product_attribute` > 0 AND `id_product_attribute` NOT IN (SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`)');
        Db::getInstance()->delete('gm_omniprice_cache', '`id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_currency` NOT IN (SELECT `id_currency` FROM `'._DB_PREFIX_.'currency`)');
        Db::getInstance()->delete('gm_omniprice_cache', '`id_currency` NOT IN (SELECT `id_currency` FROM `'._DB_PREFIX_.'currency`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_group` NOT IN (SELECT `id_group` FROM `'._DB_PREFIX_.'group`)');
        Db::getInstance()->delete('gm_omniprice_cache', '`id_group` NOT IN (SELECT `id_group` FROM `'._DB_PREFIX_.'group`)');
        Db::getInstance()->delete('gm_omniprice_history', '`id_country` NOT IN (SELECT `id_country` FROM `'._DB_PREFIX_.'country` WHERE `active` = 1)');
        Db::getInstance()->delete('gm_omniprice_cache', '`id_country` NOT IN (SELECT `id_country` FROM `'._DB_PREFIX_.'country` WHERE `active` = 1)');
        if ($this->ignoreCountries) {
            if (Shop::isFeatureActive()) {
                //for the future
            } else {
                Db::getInstance()->delete('gm_omniprice_history', '`id_country` != '.$this->defaultCountryId);
                Db::getInstance()->delete('gm_omniprice_cache', '`id_country` != '.$this->defaultCountryId);
            }
        }
        foreach ($this->ignoredGroups as $ignoredGroupId) {
            if ($ignoredGroupId && ((int) $ignoredGroupId !== (int) $this->defaultGroupId)) {
                Db::getInstance()->delete('gm_omniprice_history', '`id_group` = '.$ignoredGroupId);
                Db::getInstance()->delete('gm_omniprice_cache', '`id_group` = '.$ignoredGroupId);
            }
        }
        $date = date("Y-m-d", strtotime("-".$this->daysBack." days"));
        $output .= $this->l('Period').': '.$this->daysBack.' ('.$date.')<br/>';
        $shopIds = $this->getShopsIds();
        foreach ($shopIds as $shopId) {
            $currencyIds = $this->getCurrencyIds($shopId);
            $countryIds = $this->getCountryIds($shopId);
            $groupIds = $this->getGroupIds($shopId);
            foreach ($currencyIds as $currencyId) {
                foreach ($countryIds as $countryId) {
                    foreach ($groupIds as $groupId) {
                        $query = 'SELECT `date`, `id_product`, `id_product_attribute` FROM `'._DB_PREFIX_.'gm_omniprice_history` '
                            .' WHERE `id_shop` = '.$shopId.' AND `id_currency` = '.$currencyId.
                            ' AND `id_country` = '.$countryId.' AND `id_group` = '.$groupId.' ORDER BY `date` ASC';
                        $res = Db::getInstance()->executeS($query);
                        $datesMap = [];
                        if ($res) {
                            foreach ($res as $row) {
                                $day = $row['date'];
                                $productId = $row['id_product'];
                                $attributeId = $row['id_product_attribute'];
                                if ($day < $date) {
                                    $datesMap[$productId][$attributeId][] = $day;
                                }
                            }
                            //$output .= var_export($datesMap, true);
                            foreach ($datesMap as $productId => $dateItem) {
                                foreach ($dateItem as $attributeId => $dates) {
                                    $output .= "Product ID {$productId}, attribute ID: {$attributeId}<br/>";
                                    $datesCount = count($dates);
                                    if ($datesCount > 1) {
                                        for ($i = 0; $i < $datesCount - 1; $i++) {
                                            $output .= ' '.$dates[$i].' '.$this->l('this price may be deleted').'<br/>';
                                            $where = '`id_shop` = '.$shopId.' AND `id_currency` = '.$currencyId.
                                                ' AND `id_country` = '.$countryId.' AND `id_group` = '.$groupId;
                                            $where .= ' AND `id_product` = '.$productId.' AND `id_product_attribute` = '.$attributeId;
                                            $where .= ' AND `date` = \''.$dates[$i].'\'';
                                            Db::getInstance()->delete('gm_omniprice_history', $where);
                                        }
                                    }
                                    $output .= ' '.$dates[$datesCount - 1].' '.$this->l('this price is still needed').'<br/>';
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($verbose) {
            echo '<pre>';
            echo $output;
        }
    }

    public function hookDisplayAdminProductsExtra(array $params)
    {
        $data = [];
        if (isset($params['id_product'])) {
            $productId = (int) $params['id_product'];
        } else {
            $productId = (int) Tools::getValue('id_product');
        }
        $shopId = (int) $this->context->shop->id;
        $currencyId = (int) $this->defaultCurrencyId;
        if (Shop::isFeatureActive()) {
            $currencyId = Configuration::get('PS_CURRENCY_DEFAULT', null, null, $shopId);
        }
        $countryId = (int) $this->defaultCountryId;
        if (Shop::isFeatureActive()) {
            $countryId = Configuration::get('PS_COUNTRY_DEFAULT', null, null, $shopId);
        }
        $groupId = (int) $this->defaultGroupId;
        $attributeId = 0;

        $query = 'SELECT `date`, `price_tin`, `is_specific_price` '
            .' FROM `'._DB_PREFIX_.'gm_omniprice_history`'
            .' WHERE `id_shop` = '.$shopId
            .' AND `id_product` = '.$productId
            .' AND `id_product_attribute` = '.$attributeId
            .' AND `id_currency` = '.$currencyId
            .' AND `id_country` = '.$countryId
            .' AND `id_group` = '.$groupId
            .' ORDER BY `date` DESC';

        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $data[$row['date']] = [
                    'date' => $row['date'],
                    'price_tin' => $row['price_tin'],
                    'is_specific_price' => $row['is_specific_price'],
                    'type' => ($row['is_specific_price'] ? $this->l('Reduced price') : $this->l('Regular price'))
                ];
            }
        }

        $query = 'SELECT `date`, `price_tin` '
            .' FROM `'._DB_PREFIX_.'gm_omniprice_cache`'
            .' WHERE `id_shop` = '.$shopId
            .' AND `id_product` = '.$productId
            .' AND `id_product_attribute` = '.$attributeId
            .' AND `id_currency` = '.$currencyId
            .' AND `id_country` = '.$countryId
            .' AND `id_group` = '.$groupId
            .' ORDER BY `date` DESC';

        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                if (!array_key_exists($row['date'], $data)) {
                    $data[$row['date']] = [
                        'date' => $row['date'],
                        'price_tin' => $row['price_tin'],
                        'is_specific_price' => '',
                        'type' => $this->l('Lowest previous price')
                    ];
                }
            }
        }
        krsort($data);
        $indexed = (int) Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'gm_omniprice_index` WHERE `id_product` = '.$productId.
                ' AND `date` = \''.$this->today.'\'');
        $this->context->smarty->assign(array(
            'historyData' => $data,
            'indexedToday' => $indexed
        ));
        $debug = '';
        if (Tools::isSubmit('omnidebug')) {
            $res = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'gm_omniprice_history` WHERE `id_product` = '.$productId.' ORDER BY `date` DESC');
            if ($res) {
                $debug = $this->displayTable($res, array_keys($res[0]));
            }
        }
        return $this->display(__FILE__, 'tab.tpl').$debug;
    }

    public function displayTable($data, $columns)
    {
        $output = '<table class="table table-hover" border="1">';
        $output .= '<thead>';
        $output .= '<tr>';
        foreach ($columns as $columnHeader) {
            $output .= '<th>'.$columnHeader.'</th>';
        }
        $output .= '</thead>';
        $output .= '</tr>';
        foreach ($data as $row) {
            $output .= '<tr>';
            foreach ($columns as $key) {
                $output .= '<td>'.$row[$key].'</td>';
            }
            $output .= '</tr>';
        }
        $output .= '</table>';
        return $output;
    }

    public function hookActionGetProductPropertiesAfter($params)
    {
        return;
        $product = &$params['product'];
        $sp = $product['specific_prices'];
        //var_export($product);
        $sp['reduction'] = (string) (rand(0, 10) / 100);
        $product['specific_prices'] = $sp;
    }

    public function fillMissingCache($verbose = false)
    {
        $output = '';
        $query = 'SELECT * FROM `'._DB_PREFIX_.'gm_omniprice_history` '
            .' WHERE `is_specific_price` = 1 AND `id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `active` = 1)'
            .' ORDER BY `date` DESC ';
        $res = Db::getInstance()->executeS($query);
        if ($res) {
            foreach ($res as $row) {
                $lowestPrice = $this->getLowestCachedPrice($row);
                if ($lowestPrice === false) {
                    $shopId = (int) $row['id_shop'];
                    $productId = (int) $row['id_product'];
                    $groupId = (int) $row['id_group'];
                    $currencyId = (int) $row['id_currency'];
                    $countryId = (int) $row['id_country'];
                    $attributeId = (int) $row['id_product_attribute'];
                    $output .= var_export($row, true).'<br/>';
                    $output .= ' - no lowest price!<br/>';
                    $lastChangeDate = $row['date'];
                    $output .= ' Look for the lowest price before '.$lastChangeDate.'<br/>';
                    $now = time();
                    $your_date = strtotime($row['date']);
                    $datediff = $now - $your_date;
                    $daysOffset = floor($datediff / (60 * 60 * 24));
                    $output .= ' days offset: '.$daysOffset.'<br/>';
                    $lowestPrices = $this->getLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $daysOffset);
                    if ($lowestPrices) {
                        $output .= ' found price: '.$lowestPrices['price_tin'].' ('.$lowestPrices['price_tex'].')<br/>';
                        $this->saveLowestPrice($shopId, $productId, $currencyId, $countryId, $groupId, $attributeId, $lowestPrices['price_tex'],
                            $lowestPrices['price_tin'], $lowestPrices['date']);
                    }
                }
            }
        }
        if ($verbose) {
            echo $output;
        }
    }

    public function getActiveMap()
    {
        if ($this->activeMap == null) {
            $query = 'SELECT `id_shop`, `id_product`, `active` FROM `'._DB_PREFIX_.'product_shop`';
            $res = Db::getInstance()->executeS($query);
            if ($res) {
                foreach ($res as $row) {
                    $this->activeMap[(int) $row['id_shop']][(int) $row['id_product']] = (int) $row['active'];
                }
            }
        }
        return $this->activeMap;
    }

    public function productIsActive($productId, $shopId)
    {
        $activeMap = $this->getActiveMap();
        return ($activeMap[$shopId][$productId] == 1);
    }
}
