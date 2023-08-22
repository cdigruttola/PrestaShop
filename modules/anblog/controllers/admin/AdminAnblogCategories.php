<?php
/**
 * 2020 Anvanto
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 *  @author Anvanto <anvantoco@gmail.com>
 *  @copyright  2020 Anvanto
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of Anvanto
 */

require_once _PS_MODULE_DIR_.'anblog/loader.php';

class AdminAnblogCategoriesController extends ModuleAdminController
{
    public $name = 'anblog';
    protected $fields_form = array();
    private $_html = '';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->id_anblogcat = true;
        $this->table = 'anblogcat';

        $this->className = 'anblogcat';
        $this->lang = true;
        $this->fields_options = array();

        parent::__construct();

        $this->toolbar_title = $this->l('Categories Management');
    }

    /**
     * Build List linked Icons Toolbar
     */
    public function initPageHeaderToolbar()
    {
        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/ui/themes/base/jquery.ui.tabs.css');
        if (file_exists(_PS_THEME_DIR_.'css/modules/anblog/views/assets/admin/form.css')) {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/anblog/views/assets/admin/form.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/anblog/views/css/admin/form.css');
        }

        if (empty($this->display)) {
            parent::initPageHeaderToolbar();
        }
    }

    /**
     *
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.sortable.min.js');
        if (file_exists(_PS_THEME_DIR_.'js/modules/anblog/views/assets/admin/jquery.nestable.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/views/assets/admin/jquery.nestable.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/views/js/admin/jquery.nestable.js');
        }
        if (file_exists(_PS_THEME_DIR_.'js/modules/anblog/views/assets/admin/form.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/views/assets/admin/form.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/views/js/admin/form.js');
        }

        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.cookie-plugin.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.tabs.min.js');
    }

    /**
     * get live Edit URL
     */
    public function getLiveEditUrl($live_edit_params)
    {
        $url = $this->context->shop->getBaseURL().Dispatcher::getInstance()->createUrl(
            'index',
            (int)$this->context->language->id,
            $live_edit_params
        );
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $url = str_replace('index.php', '', $url);
        }
        return $url;
    }

    /**
     * add toolbar icons
     */
    public function initToolbar()
    {
        $this->context->smarty->assign('toolbar_scroll', 1);
        $this->context->smarty->assign('show_toolbar', 1);
        $this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
        $this->context->smarty->assign('title', $this->toolbar_title);
    }

    public function postProcess()
    {
        if (Tools::getValue('doupdatepos') && Tools::isSubmit('updatePosition')) {
            $list = Tools::getValue('list');
            $root = 1;
            $child = array();
            foreach ($list as $id => $parent_id) {
                if ($parent_id <= 0) {
                    // validate module
                    $parent_id = $root;
                }
                $child[$parent_id][] = $id;
            }
            $res = true;
            foreach ($child as $id_parent => $menus) {
                $i = 0;
                foreach ($menus as $id_anblogcat) {
                    $sql = 'UPDATE `'._DB_PREFIX_.'anblogcat` SET `position` = '.(int)$i.', 
                            id_parent = '.(int)$id_parent.' WHERE `id_anblogcat` = '.(int)$id_anblogcat;
                    $res &= Db::getInstance()->execute($sql);
                    $i++;
                }
            }
            die($this->l('Positions has been updated'));
        }
        /* delete megamenu item */
        if (Tools::getValue('dodel')) {
            $obj = new Anblogcat((int)Tools::getValue('id_anblogcat'));
            $obj->delete();
            Tools::redirectAdmin(AdminController::$currentIndex.'&token='.Tools::getValue('token'));
        }
        if (Tools::isSubmit('save'.$this->name) && Tools::isSubmit('active')) {
            if ($id_anblogcat = Tools::getValue('id_anblogcat')) {
                // validate module
                $megamenu = new Anblogcat((int)$id_anblogcat);

                if (Tools::getIsset('groupBox')) {
                    $megamenu->groups = implode(';', Tools::getValue('groupBox'));
                } else {
                    $megamenu->groups;
                }
            } else {
                // validate module
                $megamenu = new Anblogcat();
                $megamenu->randkey = AnblogHelper::genKey();
            }
            $this->copyFromPost($megamenu, $this->table);

            $megamenu->id_shop = $this->context->shop->id;

            if ($megamenu->validateFields(false) && $megamenu->validateFieldsLang(false)) {
                $megamenu->save();
                if (isset($_FILES['image_link']) && isset($_FILES['image_link']['tmp_name']) && !empty($_FILES['image_link']['tmp_name'])) {
                    if ($megamenu->image != '') {
                        $old_image = $megamenu->image;
                    }

                    if (ImageManager::validateUpload($_FILES['image_link']) ||
                        !($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_link']['tmp_name'], $tmp_name) ||
                        !ImageManager::resize($tmp_name, _ANBLOG_BLOG_IMG_DIR_.'c/'.$_FILES['image_link']['name'])
                    ) {
                        return false;
                    }
                    unlink($tmp_name);
                    $megamenu->image = $_FILES['image_link']['name'];
                    if (isset($old_image) && file_exists($old_image)) {
                        unlink(_ANBLOG_BLOG_IMG_DIR_.'c/'.$old_image);
                    }

                    $megamenu->save();
                }
                if (Tools::getValue('id_anblogcat')) {
                    $message = 'Category has been updated successfully';
                } else {
                    $message = 'Category has been added successfully';
                }
                Tools::redirectAdmin(AdminController::$currentIndex.'&saveanblog&token='.Tools::getValue('token').'&id_anblogcat='.$megamenu->id.'&confirmations='.htmlentities($message));
            } else {
                // validate module
                $this->errors[] = $this->l('An error occurred while attempting to save.');
            }
        }
        if (Tools::getValue('confirmations')) {
            $this->confirmations[] = html_entity_decode(Tools::getValue('confirmations'));
        }
    }

    /**
     *
     *
     */
    public function renderList()
    {
        $this->initToolbar();
        if (!$this->loadObject(true)) {
            return false;
        }

        $obj = $this->object;
        $tree = $obj->getTree();
        $menus = $obj->getDropdown(null, $obj->id_parent);

        // FIX : PARENT IS NOT THIS CATEGORY
        $id_anblogcat = (int) (Tools::getValue('id_anblogcat'));
        foreach ($menus as $key => $menu) {
            if ($menu['id'] == $id_anblogcat) {
                unset($menus[$key]);
            }
        }

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        );

        $groups = Group::getGroups(Context::getContext()->language->id);
        $groupsdefault = array();
        foreach ($groups as $group) {
            $groupsdefault[$group['id_group']] = true;
        }

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Category Form'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Category ID'),
                    'name' => 'id_anblogcat',
                    'default' => 0,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'default' => '',
                    'name' => 'title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'class' => 'copyMeta2friendlyURL',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                    'default' => '',
                    'hint' => $this->l('Only letters and the minus (-) character are allowed')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Parent ID'),
                    'name' => 'id_parent',
                    'options' => array('query' => $menus,
                        'id' => 'id',
                        'name' => 'title'),
                    'default' => 'url',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Additional CSS class'),
                    'name' => 'menu_class',
                    'display_image' => true,
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu icon class'),
                    'name' => 'icon_class',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->l('Select any icon here ')
                        .html_entity_decode('&#x3C;a href=&#x22;http://fontawesome.io/&#x22; target=&#x22;_blank&#x22;&#x3E;http://fontawesome.io/&#x3C;/a&#x3E; and paste its title in the field above')
                ),
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Image name'),
                    'name' => 'image',
                    'default' => '',
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'image_link',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->l(''),
                    'thumb' => '',
                    'title' => $this->l('Icon Preview'),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'content_text',
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Customer Groups'),
                    'name' => 'type',
                    'required' => true,
                    'class' => 't',
                    'values' => $groups,
                    'default' => $groupsdefault,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-large btn-default pull-right'
            )
        );

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('SEO META'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'default' => '',
                    'name' => 'meta_title',
                    'id' => 'name', // for copyMeta2friendlyURL compatibility
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    'default' => ''
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'default' => '',
                    'hint' => $this->l('Invalid characters:') . ' &lt;&gt;;=#{}',
                    'desc' => array(
                        $this->l('To add a keyword, enter the keyword and then press "Enter"')
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-large btn-default pull-right'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getValue('token');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }

        $helper->currentIndex = AdminController::$currentIndex;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->l('Categories Management');
        $helper->submit_action = 'save'.$this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($obj),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );

        $action = AdminController::$currentIndex.'&save'.$this->name.'&token='.Tools::getValue('token');
        $addnew = AdminController::$currentIndex.'&token='.Tools::getValue('token');
        $helper->toolbar_btn = false;

        $this->context->smarty->assign(
            array(
                'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
                'anblog_del_img_txt'         => $this->l('Delete'),
                'anblog_del_img_mess'        => $this->l('Are you sure delete this?'),
                'form'   => $helper->generateForm($this->fields_form),
                'action' => $action,
                'addnew' => $addnew,
                'tree'   => $tree
            )
        );

        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'anblog/views/templates/admin/prerender/megamenu.tpl');

        return $this->_html.$output;
    }

    /**
     * Asign value for each input of Data form
     */
    public function getConfigFieldsValues($obj)
    {
        $languages = Language::getLanguages(false);
        $fields_values = array();

        $url = _PS_BASE_URL_;
        if (Tools::usingSecureMode()) {
            // validate module
            $url = _PS_BASE_URL_SSL_;
        }
        foreach ($this->fields_form as $k => $f) {
            foreach ($f['form']['input'] as $j => $input) {
                if (isset($obj->{trim($input['name'])})) {
                    if (isset($obj->{trim($input['name'])})) {
                        $data = $obj->{trim($input['name'])};
                    } else {//???wtf
                        if ($input['type'] == 'group') {
                            foreach ($input['default'] as $key => $value) {
                                var_dump($key);
                                $data['groupBox_' . $key] = $value;
                            }
                        }
                        $data = $input['default'];
                    }

                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            // validate module
                            $fields_values[$input['name']][$lang['id_lang']] = isset($data[$lang['id_lang']]) ? $data[$lang['id_lang']] : $input['default'];
                        }
                    } else {
                        // validate module
                        $fields_values[$input['name']] = $data;
                    }
                } else {
                    if ($input['name'] == 'image_link' && $obj->image != '') {
                        $thumb = $url._ANBLOG_BLOG_IMG_URI_.'/c/'.$obj->image;
                        $this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
                    }

                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            $v = Tools::getValue('title', Configuration::get($input['name'], $lang['id_lang']));
                            $fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
                        }
                    } else {
                        $v = Tools::getValue($input['name'], Configuration::get($input['name']));
                        $fields_values[$input['name']] = $v ? $v : $input['default'];
                    }

                    if ($input['name'] == $obj->type.'_type') {
                        // validate module
                        $fields_values[$input['name']] = $obj->item;
                    }
                    if ($input['type'] == 'group') {
                        foreach ($input['values'] as $group) {
                            $fields_values['groupBox_' . $group['id_group']] = in_array($group['id_group'], explode(';', $obj->groups));
                        }
                    }
                }
            }
        }


        return $fields_values;
    }
}
