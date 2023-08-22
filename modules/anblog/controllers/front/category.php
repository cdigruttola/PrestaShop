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

class AnblogcategoryModuleFrontController extends ModuleFrontController
{
    public $php_self;
    protected $template_path = '';

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->template_path = _PS_MODULE_DIR_.'anblog/views/templates/front/';
        $code = '';
        if (sizeof(Language::getLanguages(true, true)) > 1) {
            $code =$this->context->language->iso_code .  '/';
        }
        $this->context->smarty->assign(
            'anblog_main_page',
            $this->context->shop->getBaseURL(true) . $code . Configuration::get('link_rewrite', 'blog')
        );

		$this->category = Anblogcat::findByRewrite(array('link_rewrite' => Tools::getValue('rewrite')));

        if (!isset($this->category->id) || !$this->category->id){
            Tools::redirect('index.php?controller=404');
        }
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $config = AnblogConfig::getInstance();

        /* Load Css and JS File */
        AnblogHelper::loadMedia($this->context, $this);

    //    $this->php_self = 'category';

        if ($this->category->groups != null
            && $this->category->groups != ''
            && !in_array(Group::getCurrent()->id, explode(';', $this->category->groups))
        ) {
            Tools::redirect('index.php?controller=404');
        }

        parent::initContent();

        $helper = AnblogHelper::getInstance();


        $limit = (int)Configuration::get(anblog::PREFIX . 'listing_limit_items', 6);
        $n = $limit;
        $p = abs((int)(Tools::getValue('p', 1)));

        if ($this->category->id_anblogcat && $this->category->active) {
            $this->template_path .= 'default/';
            $url = _PS_BASE_URL_;
            if (Tools::usingSecureMode()) {
                // validate module
                $url = _PS_BASE_URL_SSL_;
            }
            if ($this->category->image) {
                // validate module
                $this->category->image = $url._ANBLOG_BLOG_IMG_URI_.'/c/'.$this->category->image;
            }

            $leading_blogs = AnblogBlog::getListBlogs(
				$this->category->id_anblogcat,
				$this->context->language->id,
				$p,
				$limit,
				'date_add',
				'DESC',
				[],
				true);

            $count = AnblogBlog::countBlogs($this->category->id_anblogcat, $this->context->language->id, true);
            $authors = array();

            foreach ($leading_blogs as $key => $blog) {
                $blog = AnblogHelper::buildBlog($helper, $blog, 'anblog_listing_leading_img', $config);
                if ($blog['id_employee']) {
                    if (!isset($authors[$blog['id_employee']])) {
                        // validate module
                        $authors[$blog['id_employee']] = new Employee($blog['id_employee']);
                    }

                    if ($blog['author_name'] != '') {
                        $blog['author'] = $blog['author_name'];
                        $blog['author_link'] = $helper->getBlogAuthorLink($blog['author_name']);
                    } else {
                        $blog['author'] = $authors[$blog['id_employee']]->firstname.' '.$authors[$blog['id_employee']]->lastname;
                        $blog['author_link'] = $helper->getBlogAuthorLink($authors[$blog['id_employee']]->id);
                    }
                } else {
                    $blog['author'] = '';
                    $blog['author_link'] = '';
                }

                $leading_blogs[$key] = $blog;
            }

            $nb_blogs = $count;
            $range = 2; /* how many pages around page selected */
            if ($p > (($nb_blogs / $n) + 1)) {
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

            $params = array(
                'rewrite' => $this->category->link_rewrite,
                'id' => $this->category->id_anblogcat
            );

            /* breadcrumb */
            $r = $helper->getPaginationLink('module-anblog-category', 'category', $params, false, true);
            $all_cats = array();
            self::parentCategories($this->category, $all_cats);

            foreach ($all_cats as $key => $cat) {
                $params = array(
                    'rewrite' => $cat->link_rewrite,
                    'id' => $cat->id
                );
                $all_cats[$key]->category_link = $helper->getBlogCatLink($params);
            }
            $this->context->smarty->assign(
                array(
                    'getBlogLink'    => false,
                    'categories'     => $all_cats,
                    'blogLink'       => $helper->getFontBlogLink(),
                    'blogTitle'      => htmlentities(Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id, 'Blog'), ENT_NOQUOTES, 'UTF-8'),
                    'navigationPipe' => Configuration::get('PS_NAVIGATION_PIPE'),
                    'isNew'   => $this->module->new174,
                )
            );
            /* sub categories */
            $categories = $this->category->getChild($this->category->id_anblogcat, $this->context->language->id);

            $childrens = array();

            if ($categories) {
                foreach ($categories as $child) {
                    $params = array(
                        'rewrite' => $child['link_rewrite'],
                        'id' => $child['id_anblogcat']
                    );

                    $child['thumb'] = $url._ANBLOG_BLOG_IMG_URI_.'/c/'.$child['image'];

                    $child['category_link'] = $helper->getBlogCatLink($params);
                    $childrens[] = $child;
                }
            }

            $this->context->smarty->assign(
                array(
                    'leading_blogs' => $leading_blogs,
                    'listing_column' => Configuration::get(anblog::PREFIX . 'listing_column', 3),
                    'module_tpl' => $this->template_path,
                    'config' => $config,
                    'range' => $range,
                    'category' => $this->category,
                    'start' => $start,
                    'childrens' => $childrens,
                    'stop' => $stop,
                    'pages_nb' => $pages_nb,
                    'nb_items' => $count,
                    'p' => (int)$p,
                    'n' => (int)$n,
                    'meta_title' => Tools::ucfirst($this->category->title).' - '.Configuration::get('PS_SHOP_NAME'),
                    'meta_keywords' => $this->category->meta_keywords,
                    'meta_description' => $this->category->meta_description,
                    'requestPage' => $r['requestUrl'],
                    'requestNb' => $r,
                    'isNew'   => $this->module->new174
                )
            );
        } else {
            $this->context->smarty->assign(
                array(
                    'getBlogLink'    => true,
                    'blogLink'       => $helper->getFontBlogLink(),
                    'blogTitle'      => htmlentities(Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id, 'Blog'), ENT_NOQUOTES, 'UTF-8'),
                    'navigationPipe' => Configuration::get('PS_NAVIGATION_PIPE')
                )
            );
            $this->context->smarty->assign(
                array(
                    'active' => '0',
                    'leading_blogs' => array(),
                    'controller' => 'category',
                    'isNew'   => $this->module->new174,
                    'category' => $this->category
                )
            );
        }

        $this->context->smarty->assign(
            array(
            'post_type' => Tools::getIsset('post_type') ? Tools::getValue('post_type') : AnblogConfig::getInstance()->get('item_posts_type'),
            'show_in_blog' => Tools::getIsset('show_in_blog') ? Tools::getValue('show_in_blog') : AnblogConfig::getInstance()->get('show_in_blog'),
            'show_in_post' => Tools::getIsset('show_in_post') ? Tools::getValue('show_in_post') : AnblogConfig::getInstance()->get('show_in_post'),
            )
        );
        $this->setTemplate('module:anblog/views/templates/front/blog.tpl');
    }

    public static function parentCategories($current, &$return)
    {
        if ($current->id_parent) {
            $obj = new Anblogcat($current->id_parent, Context::getContext()->language->id);
            self::parentCategories($obj, $return);
        }
        $return[] = $current;
    }

    //DONGND:: add meta
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

		if ($this->category->meta_title != '' ){
			$page['meta']['title'] = $this->category->meta_title;
		} else {
			$page['meta']['title'] = Tools::ucfirst($this->category->title).' - '.Configuration::get('PS_SHOP_NAME');
		}

        $page['meta']['keywords'] = $this->category->meta_keywords;
        $page['meta']['description'] = $this->category->meta_description;

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
            'title' => Configuration::get(anblog::PREFIX . 'blog_link_title', $this->context->language->id, $this->l('Blog', 'category')),
            'url' => $link,
        );

        $category_link = $helper->getBlogCatLink([
			'rewrite' => $this->category->link_rewrite,
            'id' => $this->category->id_anblogcat
        ]);

        $breadcrumb['links'][] = array(
            'title' => $this->category->title,
            'url' => $category_link,
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
