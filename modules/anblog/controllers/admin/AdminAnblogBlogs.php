<?php
/**
* 2022 Anvanto
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Anvanto <anvantoco@gmail.com>
*  @copyright 2022 Anvanto
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require_once _PS_MODULE_DIR_.'anblog/loader.php';

class AdminAnblogBlogsController extends ModuleAdminController
{

    protected $max_image_size;
    protected $position_identifier = 'id_anblog_blog';

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'anblog_blog';
        //$this->list_id = 'id_anblog_blog';        // must be set same value $this->table to delete multi rows
        $this->identifier = 'id_anblog_blog';
        $this->className = 'AnblogBlog';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array('text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash')
        );
        $this->fields_list = array(
            'id_anblog_blog' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
		//	'image' => array('title' => $this->l('Image')),
            'meta_title' => array('title' => $this->l('Title'), 'filter_key' => 'b!meta_title'),
            'author_name' => array('title' => $this->l('Author'), 'filter_key' => 'a!author_name'),
                'title' => array('title' => $this->l('Category'), 'filter_key' => 'cl!title'),
            'hits' => array(
                'title' => $this->l('Views'),
                'filter_key' => 'a!hits'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'type' => 'bool',
                'orderby' => true
            ),
            'date_add' => array(
                'title' => $this->l('Date Create'),
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),

            'date_upd' => array(
                'title' => $this->l('Date Update'),
                'type' => 'datetime',
                'filter_key' => 'a!date_upd'
            )
        );
        $this->max_image_size = Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->_select .= ' cl.title ';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'anblog_blog_categories abc ON a.id_anblog_blog = abc.id_anblog_blog
                          LEFT JOIN '._DB_PREFIX_.'anblogcat c ON abc.id_anblogcat = c.id_anblogcat
                          LEFT JOIN '._DB_PREFIX_.'anblogcat_lang cl ON cl.id_anblogcat=c.id_anblogcat
                          AND cl.id_lang=b.id_lang 
                ';
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'anblog_blog_shop` sh 
            ON (sh.`id_anblog_blog` = b.`id_anblog_blog` AND sh.id_shop = '.(int)Context::getContext()->shop->id.') ';
        }
        $this->_where = '';
        $this->_group = ' GROUP BY (a.id_anblog_blog) ';
        $this->_orderBy = 'a.id_anblog_blog';
        $this->_orderWay = 'DESC';
    }
/* 	
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
		
		foreach ($this->_list as &$list) {
			$list['image'] = 'wdwdwd';
		}
		
    }
	 */
	

    public function initPageHeaderToolbar()
    {
        $link = $this->context->link;
        if (Tools::getValue('id_anblog_blog')) {
            $helper = AnblogHelper::getInstance();
            $blog_obj = new AnblogBlog(Tools::getValue('id_anblog_blog'), $this->context->language->id);
            $this->page_header_toolbar_btn['view-blog-preview'] = array(
                'href' => $helper->getBlogLink(get_object_vars($blog_obj)),
                'desc' => $this->l('View Post'),
                'icon' => 'icon-preview anblog-comment-link-icon icon-3x process-icon-preview',
            'target' => '_blank',
            );
            
            $this->page_header_toolbar_btn['view-blog-comment'] = array(
                'href' => $link->getAdminLink('AdminAnblogComments').'&id_anblog_blog='.Tools::getValue('id_anblog_blog'),
                'desc' => $this->l('Manage Comments'),
                'icon' => 'icon-comment anblog-comment-link-icon icon-3x process-icon-comment',
            'target' => '_blank',
            );
        }

        return parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        if (!$this->loadObject(true)) {
            if (Validate::isLoadedObject($this->object)) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $id_anblogcat = (int)(Tools::getValue('id_anblogcat'));
        $obj = new anblogcat($id_anblogcat);
        $obj->getTree();
        $menus = $obj->getDropdown(null, $obj->id_parent, false);
        array_shift($menus);

        $url = _PS_BASE_URL_;
        if (Tools::usingSecureMode()) {
            // validate module
            $url = _PS_BASE_URL_SSL_;
        }
        if ($this->object->image) {
            $thumb = $url._ANBLOG_BLOG_IMG_URI_.'b/'.$this->object->image;
        } else {
            $thumb = '';
        }
        
        //DONGND:: add default author name is name of current admin
        $default_author_name = '';
        
        if (isset($this->context->employee->firstname) && isset($this->context->employee->lastname)) {
            $default_author_name = $this->context->employee->firstname.' '.$this->context->employee->lastname;
        }
        
        if ($this->object->id == '') {
            $this->object->author_name = $default_author_name;
        }

        $this->multiple_fieldsets = true;

        $input = array(
            array(
                'type' => 'select',
                'label' => $this->l('Category'),
                'name' => 'categories[]',
                'options' => array('query' => $menus,
                    'id' => 'id',
                    'name' => 'title'),
                'default' => $id_anblogcat,
                'multiple' => true,
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Meta title'),
                'name' => 'meta_title',
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
                'hint' => $this->l('Only letters and the minus (-) character are allowed')
            ),
            array(
                'type' => 'tags',
                'label' => $this->l('Tags'),
                'name' => 'tags',
                'lang' => true,
                'hint' => array(
                    $this->l('Invalid characters:').' &lt;&gt;;=#{}',
                    $this->l('To add "tags" click in the field, write something, and then press "Enter."')
                )
            ),
            array(
                'type' => 'hidden',
                'label' => $this->l('Image Name'),
                'name' => 'image',
            ),
            array(
                'type' => 'file',
                'label' => $this->l('Image'),
                'name' => 'image_link',
                'display_image' => true,
                'default' => '',
                'desc' => $this->l('Max file size is: ').($this->max_image_size/1024/1024). 'MB',
                'thumb' => $thumb,
                'class' => 'anblog_image_upload',
            ),
            array(
                'type' => 'hidden',
                'label' => $this->l('Thumb Name'),
                'name' => 'thumb',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Blog description'),
                'name' => 'description',
                'autoload_rte' => true,
                'lang' => true,
                'rows' => 5,
                'cols' => 30,
                'hint' => $this->l('Invalid characters:').' <>;=#{}'
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Blog Content'),
                'name' => 'content',
                'autoload_rte' => true,
                'lang' => true,
                'rows' => 5,
                'cols' => 40,
                'hint' => $this->l('Invalid characters:').' <>;=#{}'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Author'),
                'name' => 'author_name',
                'desc' => $this->l('Author is displayed in the front-end')
            ),
            array(
                'type' => 'date_anblog',
                'label' => $this->l('Date'),
                'name' => 'date_add',
                'default' => date('Y-m-d'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Indexation (by search engines):'),
                'name' => 'indexation',
                'required' => false,
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'indexation_on',
                        'value' => 1,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id' => 'indexation_off',
                        'value' => 0,
                        'label' => $this->l('Disabled')
                    )
                ),
            ),
            array(
                'type' => 'product_autocomplete',
                'label' => $this->l('Select the Products'),
                'name' => 'products',
                'ajax_path' => self::$currentIndex . '&ajax=1&action=ProductsList&token=' . Tools::getAdminTokenLite('AdminAnblogBlogs'),
                'hint' => $this->l('Begin typing the First Letters of the Product Name, then select the Product from the Drop-down List.'),
                'form_group_class' => 'content_type_product',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Displayed:'),
                'name' => 'active',
                'required' => false,
                'is_bool' => true,
                'values' => array(
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
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $shopsQuery = array();
            foreach (Shop::getShops(false) as $shop) {
                $shopsQuery[] = array('name' => $shop['id_shop'], 'title' => $shop['domain'] . $shop['uri']);
            }
            $input[] = array(
                'type'  => 'select',
                'name' => 'shops[]',
                'label' => $this->l('Shops:'),
                'id' => 'shops',
                'multiple' => true,
                'required' => true,
                'options'  => array(
                    'query' => $shopsQuery,
                    'id' => 'name',
                    'name' => 'title',
                ),
            );
        }

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Blog Form'),
                'icon' => 'icon-folder-close'
            ),
            'input' => $input,
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ),
        'buttons' => array(
                'save_and_preview' => array(
                    'name' => 'saveandstay',
                    'type' => 'submit',
                    'title' => $this->l('Save and stay'),
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save-and-stay'
                )
            )
        );

        $this->fields_form[1]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('SEO'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                // custom template
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'cols' => 40,
                    'rows' => 10,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' => $this->l('Invalid characters:') . ' &lt;&gt;;=#{}',
                    'desc' => array(
                        $this->l('To add a keyword, enter the keyword and then press "Enter"')
                    )
                ),
            )
        );

        if (!is_null($this->object->id)) {
            $this->fields_value['categories[]'] = $this->object->categories;
        }
        $this->fields_value['products'] = $this->object->getProductsAutocompleteInfo($this->context->language->id);
        $this->tpl_form_vars = array(
            'active' => $this->object->active,
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        );
        $this->context->smarty->assign(
            array(
            'PS_ALLOW_ACCENTED_CHARS_URL' => (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'anblog_del_img_txt'         => $this->l('Delete'),
            'anblog_del_img_mess'        => $this->l('Are you sure delete this?'),
            )
        );
        $html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'anblog/views/templates/admin/prerender/additionaljs.tpl');
        return $html . parent::renderForm();
    }

    /**
     * Get product list
     */
    protected function ajaxProcessProductsList()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die();
        }
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')
            GROUP BY p.`id_product`';

        $items = Db::getInstance()->executeS($sql);

        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' . $item['reference'] . ')' : '') . '|' . (int)$item['id_product'] . "\n";
            }
        } else {
            json_encode(new stdClass());
        }
    }

    public function renderList()
    {
		$this->context->controller->errors = $this->module->checkIssetImageTypes();
        
		$this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new')
        );

        return parent::renderList();
    }

    public function getFieldsValue($obj)
    {
        parent::getFieldsValue($obj);
        $this->fields_value['shops[]'] = $obj->shops;
        return $this->fields_value;
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function postProcess()
    {
        if (Tools::isSubmit('viewblog') && ($id_anblog_blog = (int)Tools::getValue('id_anblog_blog'))
            && ($blog = new AnblogBlog($id_anblog_blog, $this->context->language->id))
            && Validate::isLoadedObject($blog)
        ) {
            $this->redirect_after = $this->getPreviewUrl($blog);
        }

        if (Tools::isSubmit('submitAddanblog_blog') || Tools::isSubmit('submitAddanblog_blogAndPreview') || Tools::isSubmit('saveandstay')) {
            if (Shop::isFeatureActive() && !Tools::getIsset('shops')) {
                $this->errors[] = $this->l('Please, select at least one shop');
                $this->display = 'edit';
            }
            if (!Tools::getIsset('categories')) {
                $this->errors[] = $this->l('Please, select at least one category');
                $this->display = 'edit';
            }

            parent::validateRules();
            if (count($this->errors)) {
                return false;
            }
            if (!$id_anblog_blog = (int)Tools::getValue('id_anblog_blog')) {
                $blog = new AnblogBlog();
                $this->copyFromPost($blog, 'blog');
				
                if(!Tools::getIsset('products')) {
                    $blog->products = [];
                } else {
                    $blog->products = Tools::getValue('products');
                }

                $blog->id_employee = $this->context->employee->id;

                if (!$blog->add(false)) {
                    $this->errors[] = $this->l('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                } else {// TODO move to blog model
                    if (isset($_FILES['image_link']) && isset($_FILES['image_link']['tmp_name']) && !empty($_FILES['image_link']['tmp_name'])) {
                        $imgName = $blog->imageObject->uploadNew();
                        if (!$imgName) {
                            $this->errors[] = $this->l('An error occurred while image processing.');

                            if (property_exists($blog->imageObject, 'error')) {
                                $this->errors[] = $blog->imageObject->error;
                            }

                            $this->display = 'edit';
                            return false;
                        }
						
						if(!Tools::getIsset('products')) {
							$blog->products = [];
						} else {
							$blog->products = Tools::getValue('products');
						}
						
                        $blog->image = $imgName;
                        $blog->update();
                    }
                }
            } else {
                $blog = new AnblogBlog($id_anblog_blog);
                $this->copyFromPost($blog, 'blog');

                if(!Tools::getIsset('products')) {
                    $blog->products = [];
                } else {
                    unset($blog->products);
                    $blog->products = Tools::getValue('products');
                }

                if (!Tools::getValue('image')) {
                    $blog->imageObject->delete();
                }

				if (isset($_FILES['image_link']) && isset($_FILES['image_link']['tmp_name']) && !empty($_FILES['image_link']['tmp_name'])) {
					
					$blog->imageObject->delete();
				
					$imgName = $blog->imageObject->uploadNew();
					if (!$imgName) {
						$this->errors[] = $this->l('An error occurred while image processing.');

						if (property_exists($blog->imageObject, 'error')) {
							$this->errors[] = $blog->imageObject->error;
						}

						$this->display = 'edit';
						return false;
					}
					$blog->image = $imgName;
					//TODO ADD THUMB $blog->thumb = $anblogImg->thumb;
				}

				if (!$blog->update()) {
                    $this->errors[] = $this->l('An error occurred while creating an object.').' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
                }

				
            }

            if (Tools::isSubmit('submitAddblogAndPreview')) {
                // validate module
                $this->redirect_after = $this->previewUrl($blog);
            } elseif (Tools::isSubmit('saveandstay')) {
                // validate module
                Tools::redirectAdmin(self::$currentIndex.'&'.$this->identifier.'='.$blog->id.'&conf=4&update'.$this->table.'&token='.Tools::getValue('token'));
            } else {
                // validate module
                Tools::redirectAdmin(self::$currentIndex.'&id_anblogcat='.$blog->id_anblogcat.'&conf=4&token='.Tools::getValue('token'));
            }
        } else {
            return parent::postProcess(true);
        }
    }
	
	
	
	

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        $this->addJqueryPlugin('autocomplete');
        if (file_exists(_PS_THEME_DIR_.'js/modules/anblog/views/assets/admin/form.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/assets/admin/form.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/anblog/views/js/admin/form.js');
        }

        if (file_exists(_PS_THEME_DIR_.'css/modules/anblog/views/assets/admin/form.css')) {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/anblog/views/assets/admin/form.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/anblog/views/css/admin/form.css');
        }
    }

    public function ajaxProcessUpdateblogPositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_anblog_blog = (int)Tools::getValue('id_anblog_blog');
            $id_category = (int)Tools::getValue('id_anblog_blog_categories');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('blog');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && $pos[2] == $id_anblog_blog)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $blog = new blog($id_anblog_blog);
            if (Validate::isLoadedObject($blog)) {
                if (isset($position) && $blog->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update blog position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This blog can not be loaded"}');
            }
        }
    }

    public function ajaxProcessUpdateblogCategoriesPositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_anblog_blog_category_to_move = (int)Tools::getValue('id_anblog_blog_categories_to_move');
            $id_anblog_blog_category_parent = (int)Tools::getValue('id_anblog_blog_categories_parent');
            $way = (int)Tools::getValue('way');
            $positions = Tools::getValue('blog_category');
            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_anblog_blog_category_parent && $pos[2] == $id_anblog_blog_category_to_move)) {
                        $position = $key;
                        break;
                    }
                }
            }
            $blog_category = new blogCategory($id_anblog_blog_category_to_move);
            if (Validate::isLoadedObject($blog_category)) {
                if (isset($position) && $blog_category->updatePosition($way, $position)) {
                    die(true);
                } else {
                    die('{"hasError" : true, "errors" : "Can not update blog categories position"}');
                }
            } else {
                die('{"hasError" : true, "errors" : "This blog category can not be loaded"}');
            }
        }
    }

    public function ajaxProcessPublishblog()
    {
        if ($this->tabAccess['edit'] === '1') {
            if ($id_anblog_blog = (int)Tools::getValue('id_anblog_blog')) {
                $bo_blog_url = dirname($_SERVER['PHP_SELF']).
                    '/index.php?tab=AdminblogContent&id_anblog_blog='.
                    (int)$id_anblog_blog.'&updateblog&token='.$this->token;

                if (Tools::getValue('redirect')) {
                    die($bo_blog_url);
                }

                $blog = new blog((int)(Tools::getValue('id_anblog_blog')));
                if (!Validate::isLoadedObject($blog)) {
                    die('error: invalid id');
                }

                $blog->active = 1;
                if ($blog->save()) {
                    die($bo_blog_url);
                } else {
                    die('error: saving');
                }
            } else {
                die('error: parameters');
            }
        }
    }
}
