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

class anblogrssModuleFrontController extends ModuleFrontController
{
    public function initContent()
    { 
            // Get data
    $authors = array();
    $config = AnblogConfig::getInstance();
    $enbrss = Configuration::get(anblog::PREFIX . 'indexation', 0);
    if ($enbrss != 1) {
        exit;
    }
    $config->setVar('blockanblogs_height', Configuration::get('BANBLOGS_HEIGHT'));
    $config->setVar('blockanblogs_width', Configuration::get('BANBLOGS_WIDTH'));
    $config->setVar('blockanblogs_limit', Configuration::get('BANBLOGS_NBR'));
    $limit = Configuration::get(anblog::PREFIX . 'rss_limit_item', 4);
    $helper = AnblogHelper::getInstance();
    $blogs = AnblogBlog::getListBlogs(
        null,
        Context::getContext()->language->id,
        0,
        $limit,
        'id_anblog_blog',
        'DESC',
        array(),
        true
    );
    foreach ($blogs as $key => $blog) {
        $blog = AnblogHelper::buildBlog($helper, $blog, 'anblog_listing_leading_img', $config);
        if ($blog['id_employee']) {
            if (!isset($authors[$blog['id_employee']])) {
                // validate module
                $authors[$blog['id_employee']] = new Employee($blog['id_employee']);
            }

            $blog['author'] = $authors[$blog['id_employee']]->firstname.' '.$authors[$blog['id_employee']]->lastname;
            $blog['author_link'] = $helper->getBlogAuthorLink($authors[$blog['id_employee']]->id);
        } else {
            $blog['author'] = '';
            $blog['author_link'] = '';
        }

        $blogs[$key] = $blog;
    }
    // Send feed
    header('Content-Type:text/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    ?>
    <rss version="2.0">
        <channel>
            <title><![CDATA[<?php echo Configuration::get('PS_SHOP_NAME') ?>]]></title>
            <link><?php echo _PS_BASE_URL_.__PS_BASE_URI__; ?></link>
            <webMaster><?php echo Configuration::get('PS_SHOP_EMAIL') ?></webMaster>
            <generator>PrestaShop</generator>
            <language><?php echo Context::getContext()->language->iso_code; ?></language>
            <image>
            <title><![CDATA[<?php echo Configuration::get('PS_SHOP_NAME') ?>]]></title>
            <url><?php echo _PS_BASE_URL_.__PS_BASE_URI__.'img/logo.jpg'; ?></url>
            <link><?php echo _PS_BASE_URL_.__PS_BASE_URI__; ?></link>
            </image>
            <?php
            foreach ($blogs as $blog) {
                echo "\t\t<item>\n";
                echo "\t\t\t<title><![CDATA[".$blog['title']."]]></title>\n";
                echo "\t\t\t<description>";
                $cdata = true;
                if (!empty($blog['image'])) {
                    echo "<![CDATA[<img src='".$blog['preview_url']."' title='".
                        str_replace(
                            '&',
                            '',
                            $blog['title']
                        )
                        ."' alt='thumb' class='img-fluid'/>";
                    $cdata = false;
                }
                if ($cdata) {
                    echo '<![CDATA[';
                }
                echo $blog['description']."]]></description>\n";

                echo "\t\t\t<link><![CDATA[".$blog['link']."]]></link>\n";
                    echo "\t\t</item>\n";
                }
                ?>
            </channel>
        </rss>
        <?php
        
        //////

        die;
    } 
}