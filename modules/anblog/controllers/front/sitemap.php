<?php
/**
 * 2022 Anvanto
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 *  @author Anvanto <anvantoco@gmail.com>
 *  @copyright  2022 Anvanto
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of Anvanto
 */

include_once _PS_MODULE_DIR_.'anblog/loader.php';

class anblogsitemapModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if(!(Configuration::get(anblog::PREFIX . 'enable_google_sitemap'))){
            Tools::redirect('index.php?controller=404');
        }

        if (Tools::isSubmit('id_lang')){
            $this->getSitemapAction();
        } else {
            $this->getMainSitemapAction();
        }
    }

    public function getMainSitemapAction()
    {
        $context = Context::getContext()->language;
        $languages = Language::getLanguages();

        $sitemapLinks = [];

        foreach ($languages as $language){
            $sitemapLinks[$language['iso_code']] = $this->context->link->getModuleLink('anblog', 'sitemap', ['id_lang' => $language['id_lang']], true, $language['id_lang']) . '';
        }

        header('Content-Type:text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        ?>
            <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <?php
                foreach ($sitemapLinks as $link) {
                    echo "\t\t<sitemap>\n";
                    echo "\t\t\t<loc><![CDATA[".$link."]]></loc>\n";
                    echo "\t\t</sitemap>\n";
                }
            ?>
            </sitemapindex>
        <?php

        die;
    }

    public function getSitemapAction()
    {
        $posts = $this->getPosts();
        $categories = $this->getCategories();

        $a = date('Y-m-d');

        header('Content-Type:text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        ?>
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
            <url>
            <link><?php echo $this->context->link->getModuleLink('anblog', 'sitemap', [], true); ?></link>
            <priority>1.0</priority>
            <changefreq>daily</changefreq>
            </url>
            <?php
                foreach ($posts as $post) {
                    echo "<url>";
                        echo "<loc><![CDATA[".$post['link']."]]></loc>";
                        echo "<priority>0.9</priority>";
                        echo "<lastmod>".$a."</lastmod>";
                        echo "<changefreq>daily</changefreq>";
                        if($post['preview_url'] !== ''){
                            echo "<image:image>";
                                echo "<image:loc>";
                                echo "<![CDATA[".$post['preview_url']."]]>";
                                echo "</image:loc>";
                                echo "<image:caption>";
                                echo "<![CDATA[".$post['title']."]]>";
                                echo "</image:caption>";
                                echo "<image:title>";
                                echo "<![CDATA[".$post['title']."]]>";
                                echo "</image:title>";
                            echo "</image:image>";
                        }
                    echo "</url>";
                }
                foreach ($categories as $category) {
                    echo "<url>";
                        echo "<loc><![CDATA[".$category['category_link']."]]></loc>";
                        echo "<priority>0.8</priority>";
                        echo "<lastmod>".$a."</lastmod>";
                        echo "<changefreq>daily</changefreq>";

                        if($category['thumb'] !== ''){
                            echo "<image:image>";
                                echo "<image:loc>";
                                echo "<![CDATA[".$category['thumb']."]]>";
                                echo "</image:loc>";
                                echo "<image:caption>";
                                echo "<![CDATA[".$category['title']."]]>";
                                echo "</image:caption>";
                                echo "<image:title>";
                                echo "<![CDATA[".$category['title']."]]>";
                                echo "</image:title>";
                            echo "</image:image>";
                        }
                    echo "</url>";
                }

            ?>
            </urlset>
        <?php
        die;
    }

    public function getPosts()
    {
        $helper = AnblogHelper::getInstance();
        $config = AnblogConfig::getInstance();

        $blogs = AnblogBlog::getListBlogs(
            null,
            Context::getContext()->language->id,
            0,
            'all',
            'id_anblog_blog',
            'DESC',
            array(),
            true
        );

        foreach ($blogs as $key => $blog) {

            $blog = AnblogHelper::buildBlog($helper, $blog, 'anblog_listing_leading_img', $config);
            $blogs[$key] = $blog;
        }

        return $blogs;
    }

    public function getCategories()
    {
        $categories = Anblogcat::getCategories();
        $helper = AnblogHelper::getInstance();

        foreach ($categories as $key => $category) {


            $category['thumb'] = _PS_BASE_URL_ ._ANBLOG_BLOG_IMG_URI_.'c/'.$category['image'];
            $category['category_link'] = $helper->getBlogCatLink(['rewrite' => $category['link_rewrite'], 'id' => $category['id_anblogcat']]);
            $categories[$key] = $category;
        }
        return $categories;
    }


}
