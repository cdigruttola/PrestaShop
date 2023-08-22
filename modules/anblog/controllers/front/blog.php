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
require_once _PS_MODULE_DIR_.'anblog/ReCaptcha/ReCaptcha/ReCaptcha.php';
require_once _PS_MODULE_DIR_.'anblog/ReCaptcha/ReCaptcha/Response.php';

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

class AnblogblogModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->template_path = _PS_MODULE_DIR_.'anblog/views/templates/front/';
        $code = '';

        $this->translations = Module::getInstanceByName('anblog')->translateFrontBlog();
        if (sizeof(Language::getLanguages(true, true)) > 1) {
            $code =$this->context->language->iso_code .  '/';
        }
        $this->context->smarty->assign(
            'anblog_main_page',
            $this->context->shop->getBaseURL(true) . $code . Configuration::get('ab_bl_link_rewrite', 'blog')
        );
        $this->context->smarty->assign(
            'blogTitle',
            Configuration::get('an_bl_blog_link_title', $this->context->language->id)
        );

        if (Configuration::get('PS_REWRITING_SETTINGS')) {
			$this->blog = AnblogBlog::findByRewrite(array('link_rewrite'=>Tools::getValue('rewrite')));

        } else {
			$this->blog = new AnblogBlog(Tools::getValue('id'), $this->context->language->id);
        }

        if (!isset($this->blog->id) || !$this->blog->id){
            Tools::redirect('index.php?controller=404');
        }
    }


    /**
     * @param object &$object Object
     * @param string $table   Object table
     *                        @ DONE
     */
    protected function copyFromPost(&$object, $table, $post = array())
    {
        /* Classical fields */
        foreach ($post as $key => $value) {
            if (property_exists($object, $key) && $key != 'id_'.$table) {

            //if (array_key_exists($key, $object) && $key != 'id_'.$table) {
                /* Do not take care of password field if empty */
                if ($key == 'passwd' && Tools::getValue('id_'.$table) && empty($value)) {
                    continue;
                }
                if ($key == 'passwd' && !empty($value)) {
                    /* Automatically encrypt password in MD5 */
                    $value = Tools::encrypt($value);
                }
                $object->{$key} = $value;
            }
        }


        /* Multilingual fields */
        $rules = call_user_func(array(get_class($object), 'getValidationRules'), get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    $field_name = $field.'_'.(int)($language['id_lang']);
                    $value = Tools::getValue($field_name);
                    if (isset($value)) {
                        // validate module
                        $object->{$field}[(int)($language['id_lang'])] = $value;
                    }
                }
            }
        }
    }

    /**
     * Save user comment
     */
    protected function comment()
    {
        if (Tools::getValue('token') != Tools::getToken(false)){
            Tools::redirect('index.php?controller=404');
        }

        $post = array();
        $post['user'] = Tools::getValue('user');
        $post['email'] = Tools::getValue('email');
        $post['comment'] = Tools::getValue('comment');
        $post['captcha'] = Tools::getValue('g-recaptcha-response', true);
        $post['id_anblog_blog'] = Tools::getValue('id_anblog_blog');
        $post['submitcomment'] = Tools::getValue('submitcomment');
        if (!empty($post)) {
            $comment = new AnblogComment();
            $config = new AnblogConfig();
            $result = true;
            $error = new stdClass();
            $error->error = true;
            if (AnblogConfig::getInstance()->params['google_captcha_status']) {
                $recaptcha = new ReReCaptcha(Configuration::get(anblog::PREFIX . 'google_captcha_secret_key'));
                $response = $recaptcha->verify(Tools::getValue('g-recaptcha-response'));
                $result = $response->isSuccess();
            }
            $this->copyFromPost($comment, 'comment', $post);
            if ($result) {
                if ($comment->validateFields(false) && $comment->validateFieldsLang(false)) {
                    $comment->save();
                    $error->message = $this->translations['thanks'];
                    $error->error = false;
                } else {
                    // validate module
                    $error->message = $this->translations['error'];
                }
            } else {
                // validate module
                $error->message = $this->translations['recapcha'];
            }

            die(json_encode($error));
        }
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        $this->php_self = 'blog';


        $config = AnblogConfig::getInstance();

        /* Load Css and JS File */
        AnblogHelper::loadMedia($this->context, $this);

        parent::initContent();

        if (Tools::isSubmit('submitcomment')) {
            // validate module
            $this->comment();
        }

        $helper = AnblogHelper::getInstance();

        if (!$this->blog->id_anblog_blog) {
            $this->context->smarty->assign(
                array(
                    'getBlogLink'    => true,
                    'blogLink'       => $helper->getFontBlogLink(),
                    'blogTitle'      => htmlentities(Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id, 'Blog'), ENT_NOQUOTES, 'UTF-8'),
                    'navigationPipe' => Configuration::get('PS_NAVIGATION_PIPE')
                )
            );
            $vars = array(
                'error' => true,
            );
            $this->context->smarty->assign($vars);

            $this->context->smarty->assign(
                array(
                    'post_type' => Tools::getIsset('post_type') ? Tools::getValue('post_type') : AnblogConfig::getInstance()->get('item_posts_type'),
                    'show_in_post' => Tools::getIsset('show_in_post') ? Tools::getValue('show_in_post') : AnblogConfig::getInstance()->get('show_in_post'),
                    'show_in_blog' => Tools::getIsset('show_in_blog') ? Tools::getValue('show_in_blog') : AnblogConfig::getInstance()->get('show_in_blog'),
                )
            );
            return $this->setTemplate('module:anblog/views/templates/front/single_post.tpl');
        }

        $category = new anblogcat($this->blog->categories[0], $this->context->language->id);
        if ($category->groups != null
            && $category->groups != ''
            && !in_array(Group::getCurrent()->id, explode(';', $category->groups))
        ) {
            Tools::redirect('index.php?controller=404');
        }
        $this->template_path .= 'default/';
        $module_tpl = $this->template_path;

        $this->blog->preview_url = '';
        if ($this->blog->image) {
            $this->blog->image_url = $this->blog->imageObject->mainurl;

            if (array_key_exists('anblog_thumb', $this->blog->imageObject->thumbsurls)) {
                $this->blog->preview_url = $this->blog->imageObject->thumbsurls['anblog_thumb'];
                $this->blog->thumb_url = $this->blog->imageObject->thumbsurls['anblog_thumb'];
            }
        }

        $blog_link = $helper->getBlogLink(get_object_vars($this->blog));
		if ($category->id_anblogcat){
			$params = array(
				'rewrite' => $category->link_rewrite,
				'id' => $category->id_anblogcat
			);

			$this->blog->category_link = $helper->getBlogCatLink($params);
			$this->blog->category_title = $category->title;
		} else {
			$this->blog->category_link = '';
			$this->blog->category_title = '';
		}

        //DONGND:: author name
        if ($this->blog->author_name != '') {
            $this->blog->author = $this->blog->author_name;
            $this->blog->author_link = $helper->getBlogAuthorLink($this->blog->author_name);
        } else {
            $employee = new Employee($this->blog->id_employee);
            $this->blog->author = $employee->firstname.' '.$employee->lastname;
            $this->blog->author_link = $helper->getBlogAuthorLink($employee->id);
        }

        $tags = array();
        if ($this->blog->tags && $tmp = explode(',', $this->blog->tags)) {
            foreach ($tmp as $tag) {
                $tags[] = array(
                    'tag' => $tag,
                    'link' => $helper->getBlogTagLink($tag)
                );
            }
        }

        $this->blog->hits = $this->blog->hits + 1;
        $this->blog->updateField($this->blog->id, array('hits' => $this->blog->hits));

        /* breadscrumb */
        $params = array(
            'rewrite' => $category->link_rewrite,
            'id' => $category->id_anblogcat
        );

        $category->category_link = $helper->getBlogCatLink($params);
        $this->context->smarty->assign(
            array(
                'getBlogLink'    => false,
                'categories'     => array($category),
                'blogLink'       => $helper->getFontBlogLink(),
                'blogTitle'      => htmlentities(Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id, 'Blog'), ENT_NOQUOTES, 'UTF-8'),
                'navigationPipe' => Configuration::get('PS_NAVIGATION_PIPE')
            )
        );
        $limit = 5;

        $samecats = AnblogBlog::getListBlogs(
            $category->id_anblogcat,
            $this->context->language->id,
            0,
            $limit,
            'date_add',
            'DESC',
            array('type' => 'samecat', 'id_anblog_blog' => $this->blog->id_anblog_blog),
            true
        );
        foreach ($samecats as $key => $sblog) {
            $sblog['link'] = $helper->getBlogLink($sblog);
            $samecats[$key] = $sblog;
        }

        $tagrelated = array();

        if ($this->blog->tags) {
            $tagrelated = AnblogBlog::getListBlogs(
                $category->id_anblogcat,
                $this->context->language->id,
                0,
                $limit,
                'date_add',
                'DESC',
                array('type' => 'tag', 'tag' => $this->blog->tags),
                true
            );
            foreach ($tagrelated as $key => $tblog) {
                $tblog['link'] = $helper->getBlogLink($tblog);
                $tagrelated[$key] = $tblog;
            }
        }

        /* Comments */
        $evars = array();
        if (Configuration::get(anblog::PREFIX . 'item_comment_engine', 'local') == 'local') {
            $count_comment = 0;
            if (Configuration::get(anblog::PREFIX . 'comment_engine', 'local') == 'local') {
                // validate module
                $count_comment = AnblogComment::countComments($this->blog->id_anblog_blog, true);
            }

            $blog_link = $helper->getBlogLink(get_object_vars($this->blog)) . '?token=' . Tools::getToken(false);
            $limit = (int)Configuration::get(anblog::PREFIX . 'item_limit_comments', 10);
            $n = $limit;
            $p = abs((int)(Tools::getValue('p', 1)));

            $comment = new AnblogComment();
            $comments = $comment->getList($this->blog->id_anblog_blog, $this->context->language->id, $p, $limit);

            $nb_blogs = $count_comment;

            if ($nb_blogs < 1){
                $nb_blogs = 1;
            }
            if ($n < 1){
                $n = 1;
            }
            $range = 2; /* how many pages around page selected */
            if ($nb_blogs > 0 && $p > (($nb_blogs / $n) + 1)) {
                Tools::redirect(preg_replace('/[&?]p=\d+/', '', $_SERVER['REQUEST_URI']));
            }
            $pages_nb = ceil($nb_blogs / (int)($n));
            $start = (int)($p - $range);
            if ($start < 1) {
                $start = 1;
            }
            $stop = (int)($p + $range);
            if ($stop > $pages_nb) {
                $stop = (int)($pages_nb);
            }

            $evars = array('pages_nb' => $pages_nb,
                'nb_items' => $count_comment,
                'p' => (int)$p,
                'n' => (int)$n,
                'requestPage' => $blog_link,
                'requestNb' => $blog_link,
                'start' => $start,
                'comments' => $comments,
                'range' => $range,
                'blog_count_comment' => $count_comment,
                'stop' => $stop);
        }
        if ((bool)Module::isEnabled('smartshortcode')
            && context::getcontext()->controller->controller_type == 'front'
        ) {
            $smartshortcode = Module::getInstanceByName('smartshortcode');
            $this->blog->content = $smartshortcode->parse($this->blog->content);
        }

        if (!empty($this->blog->products) && count($this->blog->products) > 0) {
            $products = AnblogBlog::getProductsByArrayId($this->blog->products, (int)$this->context->language->id);
            if ($products) {
                $present_products = array();
                $assembler = new ProductAssembler($this->context);

                $presenterFactory = new ProductPresenterFactory($this->context);
                $presentationSettings = $presenterFactory->getPresentationSettings();
                $presenter = new ProductListingPresenter(
                    new ImageRetriever($this->context->link),
                    $this->context->link,
                    new PriceFormatter(),
                    new ProductColorsRetriever(),
                    $this->context->getTranslator()
                );

                foreach ($products as $rawProduct) {
                    $present_products[] = $presenter->present(
                        $presentationSettings,
                        $assembler->assembleProduct($rawProduct),
                        $this->context->language
                    );
                }

                $this->blog->products = $present_products;
            }
        }

        $vars = array(
            'tags' => $tags,
            'meta_title' => Tools::ucfirst($this->blog->meta_title).' - '.Configuration::get('PS_SHOP_NAME'),
            'meta_keywords' => $this->blog->meta_keywords,
            'meta_description' => $this->blog->meta_description,
            'blog' => $this->blog,
            'samecats' => $samecats,
            'tagrelated' => $tagrelated,
            'config' => $config,
            'id_anblog_blog' => $this->blog->id_anblog_blog,
            'is_active' => $this->blog->active,
            'productrelated' => array(),
            'module_tpl' => $module_tpl,
            'blog_link' => $blog_link,
        );

        $vars = array_merge($vars, $evars);

        $this->context->smarty->assign($vars);

        $this->context->smarty->assign(
            array(
                'post_type' => Tools::getIsset('post_type') ? Tools::getValue('post_type') : AnblogConfig::getInstance()->get('item_posts_type'),
                'show_in_post' => Tools::getIsset('show_in_post') ? Tools::getValue('show_in_post') : AnblogConfig::getInstance()->get('show_in_post'),
                'show_in_blog' => Tools::getIsset('show_in_blog') ? Tools::getValue('show_in_blog') : AnblogConfig::getInstance()->get('show_in_blog'),
            )
        );
        $this->setTemplate('module:anblog/views/templates/front/single_post.tpl');
    }

    // DONGND:: add meta
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['meta']['title'] = Tools::ucfirst($this->blog->meta_title).' - '.Configuration::get('PS_SHOP_NAME');
        $page['meta']['keywords'] = $this->blog->meta_keywords;
        $page['meta']['description'] = $this->blog->meta_description;
        return $page;
    }

    //DONGND:: add breadcrumb
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $helper = AnblogHelper::getInstance();
        $link = $helper->getFontBlogLink();
        $config = AnblogConfig::getInstance();
        $breadcrumb['links'][] = array(
            'title' => Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id,
                $this->translations['blog']
            ),
            'url' => $link,
        );

        $category = new anblogcat($this->blog->categories[0], $this->context->language->id);

		if ($category->id_anblogcat){
			$params = array(
				'rewrite' => $category->link_rewrite,
				'id' => $category->id_anblogcat
			);

			$breadcrumb['links'][] = array(
				'title' => $category->title,
				'url' => $helper->getBlogCatLink($params),
			);
		}

        $breadcrumb['links'][] = array(
            'title' => Tools::ucfirst($this->blog->meta_title),
            'url' => $helper->getBlogLink(get_object_vars($this->blog)),
        );

        return $breadcrumb;
    }

    //DONGND:: get layout
    public function getLayout()
    {
        $entity = 'module-anblog-'.$this->php_self;

        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);

        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                    'default_layout' => $layout,
                    'entity' => $entity,
                    'locale' => $this->context->language->locale,
                    'controller' => $this,
                )
        )
        ) {
            return $overridden_layout;
        }

        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }

        return $layout;
    }
}
