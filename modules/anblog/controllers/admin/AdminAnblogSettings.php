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

require_once _PS_MODULE_DIR_.'anblog/loader.php';
require_once _PS_MODULE_DIR_.'anblog/classes/comment.php';

class AdminAnblogSettingsController extends ModuleAdminController
{
    protected $_module = null;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

		$this->name = 'AdminAnblogSettingsController';

        parent::__construct();
    }

    public function initToolBarTitle()
    {
        $this->toolbar_title[] = $this->l('Settings');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
        if (file_exists(_PS_THEME_DIR_ . 'js/modules/anblog/views/assets/form.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/anblog/views/assets/admin/form.js');
        } else {
            $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/anblog/views/js/admin/form.js');
        }
    }

    protected function getSettingsForm()
    {
        $url_rss = Tools::htmlentitiesutf8('http://' . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__) . 'modules/anblog/rss.php';

        $onoff = array(
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
        );

        $languages = Language::getLanguages();

        $sitemapLinks = [];

        $sitemapLinks['siteMapAll'] = $this->context->link->getBaseLink(null, null, null) . 'module/anblog/sitemap.xml';

        foreach ($languages as $language){
            $sitemapLinks['siteMapLang'][$language['iso_code']] = $this->context->link->getModuleLink('anblog', 'sitemap', ['id_lang' => $language['id_lang']], true, $language['id_lang']) . '';
        }

        $this->context->smarty->assign('sitemapLinks', $sitemapLinks);

        $rssLink = $this->context->link->getBaseLink(null, null, null) . 'module/anblog/rss';

        $this->context->smarty->assign('rssLink', $rssLink);

        $form['0']['form']['legend'] = [
            'title' => $this->l('General'),
        ];

        $form['0']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Root Link Title'),
            'name' => anblog::PREFIX .'blog_link_title',
            'required' => true,
            'lang' => true,
            'default' => 'Blog',
        ];

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Category'),
            'name' => anblog::PREFIX . 'category_rewrite',
            'lang' => true,
            'default' => '',
            'form_group_class' => 'url_use_id_sub url_use_id-0',
            'desc' => 'Enter a hint word that is displayed in the URL of a category and makes the URL friendly',
            'hint' => $this->l('Example http://domain/blog/category/name/'),
        ];

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Post'),
            'name' => anblog::PREFIX . 'detail_rewrite',
            'required' => true,
            'lang' => true,
            'default' => 'post',
            'form_group_class' => 'url_use_id_sub url_use_id-0',
            'desc' => 'Enter a hint word that is displayed in the URL of a post and makes the URL friendly',
            'hint' => $this->l('Example http://domain/blog/post/name/'),
        ];

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Root'),
            'name' => anblog::PREFIX . 'link_rewrite',
            'required' => true,
            'desc' => $this->l('If necessary, change root of the blog'),
            'default' => 'blog',
        ];

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Meta Title'),
            'name' => anblog::PREFIX . 'meta_title',
            'lang' => true,
            'cols' => 40,
            'rows' => 10,
            'default' => 'Blog',
        ];

        $form['0']['form']['input'][] = [
            'type' => 'textarea',
            'label' => $this->l('Meta description'),
            'name' => anblog::PREFIX . 'meta_description',
            'lang' => true,
            'cols' => 40,
            'rows' => 10,
            'default' => '',
            'desk' => $this->l('Display meta descrition on frontpage blog') . 'note: note &lt;&gt;;=#{}'
        ];

        $form['0']['form']['input'][] = [
            'type' => 'tags',
            'label' => $this->l('Meta keywords'),
            'name' => anblog::PREFIX . 'meta_keywords',
            'default' => '',
            'hint' => $this->l('Invalid characters:') . ' &lt;&gt;;=#{}',
            'lang' => true,
            'desc' => array(
                $this->l('To add a keyword, enter the keyword and then press "Enter"')
            )
        ];

        $form['0']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Enable RSS'),
            'name' => anblog::PREFIX . 'indexation',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '',
            'values' => $onoff,
        ];

        if(Configuration::get(anblog::PREFIX . 'indexation')){

            $form['0']['form']['input'][] = [
                'type' => 'html',
                'label' => $this->l('RSS'),
                'name' => anblog::PREFIX . 'rss_link',
                'html_content' => $this->module->display(_PS_MODULE_DIR_.'anblog','/views/templates/admin/anblog_settings/helpers/rss.tpl')
            ];
        }

        $form['0']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('RSS Limit Items'),
            'name' => anblog::PREFIX . 'rss_limit_item',
            'default' => '20',
        ];

        $form['0']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Soft reset (Do not delete database tables)'),
            'name' => anblog::PREFIX . 'soft_reset',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '',
            'values' => $onoff,
        ];

        // $form['0']['form']['input'][] = [
        //     'type' => 'text',
        //     'label' => $this->l('RSS Title'),
        //     'name' => anblog::PREFIX . 'rss_title_item',
        //     'default' => 'RSS FEED',
        // ];


        //////

        $form['1']['form']['legend'] = [
            'title' => $this->l('Blog'),
        ];

        $form['1']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Category description'),
            'name' => anblog::PREFIX . 'listing_show_categoryinfo',
            'required' => false,
            'class' => 't',
            'desc' => $this->l('Display description of the category in the list of categories'),
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['1']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Items limit'),
            'name' => anblog::PREFIX . 'listing_limit_items',
            'required' => false,
            'class' => 't',
            'default' => '6',
        ];
        //////////////////////////////////////////// ПЕРЕОПРЕДЕЛИТЬ

        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Title'),
            'name' => anblog::PREFIX . 'listing_show_title',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Description'),
            'name' => anblog::PREFIX . 'listing_show_description',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('"Read more" button'),
            'name' => anblog::PREFIX . 'listing_show_readmore',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Image'),
            'name' => anblog::PREFIX . 'listing_show_image',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Author'),
            'name' => anblog::PREFIX . 'listing_show_author',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Category'),
            'name' => anblog::PREFIX . 'listing_show_category',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Date'),
            'name' => anblog::PREFIX . 'listing_show_created',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Views'),
            'name' => anblog::PREFIX . 'listing_show_hit',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => $onoff,
        ];
        $form['1']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Comments counter'),
            'name' => anblog::PREFIX . 'listing_show_counter',
            'required' => false,
            'class' => 't',
            'default' => '0',
            'values' => $onoff,
        ];

        $form['1']['form']['input'][] = [
            'type' => 'select',
            'label' => $this->l('Posts type'),
            'name' => anblog::PREFIX . 'item_posts_type',
            'id' => 'item_posts_type',
            'class' => 'item_posts_type',
            'options' => array('query' => array(
                array('id' => 'Type 1', 'name' => $this->l('type1')),
                array('id' => 'Type 2', 'name' => $this->l('type2')),
                array('id' => 'Type 3', 'name' => $this->l('type3')),
            ),
                'id' => 'id',
                'name' => 'name'),
                'default' => 'local'
        ];
        ///////

        $form['2']['form']['legend'] = [
            'title' => $this->l('Post'),
        ];

        $form['2']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Description'),
            'name' => anblog::PREFIX . 'item_show_description',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Image'),
            'name' => anblog::PREFIX . 'item_show_image',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '',
            'values' => $onoff,
        ];


        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Author'),
            'name' => anblog::PREFIX . 'item_show_author',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Category'),
            'name' => anblog::PREFIX . 'item_show_category',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Date'),
            'name' => anblog::PREFIX . 'item_show_created',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Views'),
            'name' => anblog::PREFIX . 'item_show_hit',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Comments counter'),
            'name' => anblog::PREFIX . 'item_show_counter',
            'required' => false,
            'class' => 't',
            'default' => '1',
            'values' => $onoff,
        ];

        $form['2']['form']['input'][] = [
            'type' => 'textarea',
            'label' => $this->l('Social Sharing CODE'),
            'name' => anblog::PREFIX . 'social_code',
            'required' => false,
            'default' => '',
            'desc' => 'If you want to replace default social sharing buttons, configure them on https://www.sharethis.com/ and paste their code into the field above'
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Comments list'),
            'name' => anblog::PREFIX . 'item_show_listcomment',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
            'desc' => $this->l('Show/Hide the comments list'),
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Comment form'),
            'name' => anblog::PREFIX . 'item_show_formcomment',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '1',
            'values' => $onoff,
            'desc' => $this->l('This option is compatible only with local comments engine'),
        ];

        $form['2']['form']['input'][] = [
            'type' => 'select',
            'label' => $this->l('Comments Engine'),
            'name' => anblog::PREFIX . 'item_comment_engine',
            'id' => 'item_comment_engine',
            'class' => 'engine_select',
            'options' => array('query' => array(
                array('id' => 'local', 'name' => $this->l('Local')),
                array('id' => 'facebook', 'name' => $this->l('Facebook')),
                array('id' => 'diquis', 'name' => $this->l('Disqus')),
            ),
                'id' => 'id',
                'name' => 'name'),
                'default' => 'local'
        ];

        $form['2']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Enable reCAPTCHA  '),
            'name' => anblog::PREFIX . 'google_captcha_status',
            'required' => false,
            'is_bool' => true,
            'class' => 't local comment_item',
            'default' => '1',
            'values' => $onoff,
            'desc' => html_entity_decode('&lt;a target=&#x22;_blank&#x22;  href=&quot;https://www.google.com/recaptcha/admin&quot;&gt;Register google reCAPTCHA &lt;/a&gt;')
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('reCAPTCHA site key'),
            'name' => anblog::PREFIX . 'google_captcha_site_key',
            'required' => false,
            'class' => 't local comment_item',
            'default' => '',
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('reCAPTCHA secret key'),
            'name' => anblog::PREFIX . 'google_captcha_secret_key',
            'required' => false,
            'default' => '',
            'class' => 't local comment_item',
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Comments limit'),
            'name' => anblog::PREFIX . 'item_limit_comments',
            'required' => false,
            'class' => 't local comment_item',
            'default' => '10',
            'desc' => $this->l('This option is compatible only with local comments engine'),
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Disqus Account'),
            'name' => anblog::PREFIX . 'item_diquis_account',
            'required' => false,
            'class' => 't diquis comment_item',
            'default' => 'demo4antheme',
            'desc' => html_entity_decode('Enter the name of your Disqus account (for example anvanto-com). You can copy the name from the address page in your account: for example, the URL is anvanto-com.disqus.com/admin, then copy the text before the first dot. If you have no Disqus account, &lt;a target=&quot;_blank&quot; href=&quot;https://disqus.com/admin/signup/&quot;&gt;sign up here&lt;/a&gt;')
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Facebook Application ID'),
            'name' => anblog::PREFIX . 'item_facebook_appid',
            'required' => false,
            'class' => 't facebook comment_item',
            'default' => '100858303516',
            'desc' => html_entity_decode('&#x3C;a target=&#x22;_blank&#x22; href=&#x22;http://developers.facebook.com/docs/reference/plugins/comments/&#x22;&#x3E;' . $this->l('Register a comment box') . '&#x3C;/a&#x3E;' .  ' then enter your site URL into the Comments Plugin Code Generator and then press the "Get code" button. Copy the appId from the code and paste it into the field above.')
        ];

        $form['2']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Facebook Width'),
            'name' => anblog::PREFIX . 'item_facebook_width',
            'required' => false,
            'class' => 't facebook comment_item',
            'default' => '600'
        ];

        /////////////////

        $form['3']['form']['legend'] = [
            'title' => $this->l('Left column'),
        ];

        $form['3']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['3']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Enable in blog'),
            'name' => anblog::PREFIX . 'show_in_blog',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => array(
                array(
                    'id' => 'show_in_blog_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'show_in_blog_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        ];

        $form['3']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Enable on post page'),
            'name' => anblog::PREFIX . 'show_in_post',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => array(
                array(
                    'id' => 'show_in_post_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'show_in_post_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        ];

        $form['3']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Recent posts limit'),
            'name' => anblog::PREFIX . 'limit_recent_blog',
            'default' => '5',
        ];

        $obj = new anblogcat();
        $obj->getTree();
        $menus = $obj->getDropdown(null, $obj->id_parent, false);
        array_shift($menus);

		$itemHome['-'] = ['id'=>'', 'title' => '-', 'selected' => ''];
		$menus = array_merge($itemHome, $menus);

        /////////////////////////

        $form['4']['form']['legend'] = [
            'title' => $this->l('Widget'),
        ];

        $form['4']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['4']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Display Home'),
            'name' => anblog::PREFIX . 'show_in_DisplayHome',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => array(
                array(
                    'id' => 'show_in_DisplayHome_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'show_in_DisplayHome_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        ];

        $form['4']['form']['input'][] = [
            'type' => 'select',
            'label' => $this->l('Category'),
            'name' => anblog::PREFIX . 'categories_DisplayHome_blog',
            'options' => array('query' => $menus,
                'id' => 'id',
                'name' => 'title'),
            'default' => '',
        ];

        $form['4']['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Display Home posts limit'),
            'name' => anblog::PREFIX . 'limit_DisplayHome_blog',
            'default' => '6',
        ];

        ///////////////

        $form['5']['form']['legend'] = [
            'title' => $this->l('Google sitemap'),
        ];

        $form['5']['form']['submit'] = [
            'name' => 'save',
            'title' => $this->l('Save'),
        ];

        $form['5']['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Enable Google sitemap'),
            'name' => anblog::PREFIX . 'enable_google_sitemap',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'default' => '0',
            'values' => array(
                array(
                    'id' => 'enable_google_sitemap_on',
                    'value' => 1,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id' => 'enable_google_sitemap_off',
                    'value' => 0,
                    'label' => $this->l('Disabled')
                )
            ),
        ];

        if(Configuration::get(anblog::PREFIX . 'enable_google_sitemap')){

            $form['5']['form']['input'][] = [
                'type' => 'html',
                'label' => $this->l('Sitemaps'),
                'name' => anblog::PREFIX . 'Sitemaps',
                'html_content' => $this->module->display(_PS_MODULE_DIR_.'anblog','/views/templates/admin/anblog_settings/helpers/sitemap.tpl')
            ];
        }


        ////////////////

        return $form;
    }

    public function renderView()
    {
        $languages = $this->context->controller->getLanguages();

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->name_controller = $this->name;
        $helper->submit_action = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminAnblogSettings', false);
        $helper->token = Tools::getAdminTokenLite('AdminAnblogSettings');
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->tpl_vars = [
            'uri' => $this->module->getPathUri(),
            'languages' => $languages,
            'id_language' => $this->context->language->id
        ];

        $form = $this->getSettingsForm();

        foreach($form as $subForm){
            foreach ($subForm['form']['input'] as $input){
                if (isset($input['lang']) && $input['lang']){
                    $value = [];
                    foreach ($languages  as $language){
                        $value[$language['id_lang']] = Configuration::get($input['name'], $language['id_lang']);
                    }
                    $helper->tpl_vars['fields_value'][$input['name']] = $value;
                } else {
                    $helper->tpl_vars['fields_value'][$input['name']] = Configuration::get($input['name']);
                }
            }
        }

        //return $this->module->topPromo() . $helper->generateForm([$form]);


        return  $this->getHomeLinkBlog() . $helper->generateForm($form);
	}

    public function getHomeLinkBlog()
    {
        $link = $this->context->link;

        $code = '';
        if (sizeof(Language::getLanguages(true, true)) > 1) {
            $code =$this->context->language->iso_code .  '/';
        }
        $preview = array(
            'title' => $this->l('Open the blog'),
            'icon' => 'icon-eye',
            'target' => '_blank',
            'class' => '',
        );
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $preview['href'] = $this->context->shop->getBaseURL(true) . $code . Configuration::get(anblog::PREFIX . 'link_rewrite', $this->context->language->id);
        } else {
            $helper = AnblogHelper::getInstance();
            $preview['href'] = $helper->getFontBlogLink();
        }

        $this->context->smarty->assign('anblogTopLink', $preview);

        return $this->module->display(_PS_MODULE_DIR_.$this->module->name, 'views/templates/admin/topSettings.tpl');
    }

    public function postProcess()
    {
        if (!empty($this->errors)) {
            $this->display = 'edit';
            return false;
        }

        $form = $this->getSettingsForm();

        $isSubmit = false;
        foreach($form as $subForm){
            if (Tools::isSubmit($subForm['form']['submit']['name'])){
                $isSubmit = true;
            }
        }

        if ($isSubmit) {

            $languages = Language::getLanguages(false);

            foreach($form as $subForm){

                foreach ($subForm['form']['input'] as $input){

                    $html = false;

                    if (isset($input['html']) && $input['html']){
                        $html = true;
                    }

                    if (isset($input['lang']) && $input['lang']){
                        $value = [];
                        foreach ($languages  as $language){
                            $value[$language['id_lang']] = Tools::getValue($input['name'].'_' . $language['id_lang']);
                        }

                        Configuration::updateValue($input['name'], $value, $html);
                    } else {
                        Configuration::updateValue($input['name'], Tools::getValue($input['name']), $html);
                    }
                }
            }

            $currentIndex = $this->context->link->getAdminLink('AdminAnblogSettings', false);
            $token = Tools::getAdminTokenLite('AdminAnblogSettings');

            Tools::redirectAdmin($currentIndex.'&token='.$token.'&conf=4');
        }
		return  true;
    }
}

?>
