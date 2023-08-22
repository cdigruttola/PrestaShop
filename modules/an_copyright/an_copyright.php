<?php
/**
 * 2021 Anvanto
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 *  @author Anvanto <anvantoco@gmail.com>
 *  @copyright  2021 Anvanto
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of Anvanto
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class an_copyright extends Module implements WidgetInterface
{

	const PREFIX = 'an_cprg_';

    public function __construct()
    {
        $this->name = 'an_copyright';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Anvanto';
        $this->need_instance = 0;

        $this->bootstrap = true;
        $this->module_key = '';

        parent::__construct();

        $this->displayName = $this->l('Copyright for theme');
        $this->description = $this->l('Copyright for theme');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->front_css_path = $this->local_path.'views/css/';
    }

    /**
     * @return bool
     */
    public function install()
    {
		$defaultContent = $this->display($this->name, 'views/templates/front/default_content.tpl');
		
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
			Configuration::updateValue(self::PREFIX.'link', '#',  $lang['id_lang']);
			Configuration::updateValue(self::PREFIX.'copyright', $defaultContent, $lang['id_lang'], true);
		}

        return parent::install()
            && $this->registerHook('displayCopyrightContainerLeft');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->deleteParams($this->getParamList());

        return parent::uninstall();
    }
	
    /**
     * @param $hookName
     * @param array $params
     * @return mixed|void
     */
    public function renderWidget($hookName, array $params)
    {
		$widget = $this->getWidgetVariables($hookName, $params);

		$this->smarty->assign('widget', $widget);

		return $this->fetch('module:an_copyright/views/templates/front/widget.tpl');
    }

    /**
     * @param $hookName
     * @param array $params
     * @return array
     */
    public function getWidgetVariables($hookName, array $params)
    {
        return [
			'link' => Configuration::get(self::PREFIX . 'link', $this->context->language->id),
			'copyright' =>  Configuration::get(self::PREFIX . 'copyright', $this->context->language->id)
		];
    }
	
    /**
     * @param $key
     * @param null $value
     * @param null $id_lang
     * @return bool|string
     */
    public static function getParam($key, $value = null, $id_lang = null)
    {
        return $value === null ? Configuration::get(
            self::PREFIX . $key,
            $id_lang
        ) : Configuration::updateValue(self::PREFIX . $key, $value);
    }

    public function getParamList()
    {
        return [
            'link',
            'copyright',
        ];
    }
	
    protected function deleteParams($keys)
    {
        foreach ($keys as $key) {
            $this->deleteParam($key);
        }
    }

    protected function deleteParam($key)
    {
        return Configuration::deleteByName(self::PREFIX.$key);
    }
	
   public function getContent()
   {
		$output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $output = $this->getSubmitOutput();
        }
		
        return $output . $this->displayForm();
    }
    
    protected function getSubmitOutput()
    {
		$languages = Language::getLanguages(false);
		
		$form = $this->getConfigForm();
		
		foreach ($form['input'] as $input){
			
			$html = false;
			if (isset($input['html']) && $input['html']){
				$html = true;
			}
			
			if (isset($input['lang']) && $input['lang']){
				$value = [];
				foreach ($languages as $lang) {
					$value[$lang['id_lang']] = Tools::getValue($input['name'].'_' . $lang['id_lang']);
				}
				Configuration::updateValue($input['name'], $value, $html);
			} else {
				Configuration::updateValue($input['name'], Tools::getValue($input['name']), $html);
			}
		}        
		
        return $this->displayConfirmation($this->l('Settings updated'));
    }


    public function displayForm()
    {

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form = [];

        $fields_form[0]['form'] = $this->getConfigForm();
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                [
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ],
        );
		
		$helper->languages = $this->context->controller->getLanguages();
		$helper->id_language = $this->context->language->id;
			
		
		$languages = Language::getLanguages(false);
		foreach ($fields_form[0]['form']['input'] as $input){
			
			if (isset($input['lang']) && $input['lang']){
				foreach ($languages as $lang) {
					$helper->fields_value[$input['name']][$lang['id_lang']] = Configuration::get($input['name'], $lang['id_lang']);
				}
			} else {
				$helper->fields_value[$input['name']] = Configuration::get($input['name']);
			}
		}


        return $helper->generateForm($fields_form);
    }
	
    protected function getConfigForm()
    {
		$form = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
           
				[
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Link'),
					'name' => self::PREFIX.'link',
				],	
				[
					'type' => 'textarea',
					'class' => 'autoload_rte',
					'html' => true,
					'lang' => true,
					'label' => $this->l('Custom copyright'),
					'name' => self::PREFIX.'copyright',
					'desc' => $this->l('Use [year] to display the year'),
				],			
							
                
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ]
        ];
		
		return $form;
		
	}

}
