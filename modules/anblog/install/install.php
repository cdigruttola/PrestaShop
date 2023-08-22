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
    // module validation
    exit;
}
$path = dirname(_PS_ADMIN_DIR_);

require_once $path.'/config/config.inc.php';
require_once $path.'/init.php';

$res = (bool)Db::getInstance()->execute('
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblogcat` (
	  `id_anblogcat` int(11) NOT NULL AUTO_INCREMENT,
      `image` varchar(255) NOT NULL,
      `id_parent` int(11) NOT NULL,
      `item` varchar(255) DEFAULT NULL,
      `level_depth` smallint(6) NOT NULL,
      `active` tinyint(1) NOT NULL,
      `show_title` tinyint(1) NOT NULL,
      `position` int(11) NOT NULL,
      `submenu_content` text NOT NULL,
      `privacy` smallint(6) DEFAULT NULL,
      `position_type` varchar(25) DEFAULT NULL,
      `menu_class` varchar(25) DEFAULT NULL,
      `content` text,
      `icon_class` varchar(255) DEFAULT NULL,
      `level` int(11) NOT NULL,
      `left` int(11) NOT NULL,
      `right` int(11) NOT NULL,
      `date_add` datetime DEFAULT NULL,
      `date_upd` datetime DEFAULT NULL,
      `template` varchar(200) NOT NULL,
      `randkey` varchar(255) DEFAULT NULL,
      `groups` text,
  	   PRIMARY KEY (`id_anblogcat`)
	) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblogcat_lang` (
	  `id_anblogcat` int(11) NOT NULL,
      `id_lang` int(11) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `meta_title` varchar(255) DEFAULT NULL,
      `content_text` text,
      `description` varchar(200) NOT NULL,
      `meta_keywords` varchar(255) NOT NULL,
      `meta_description` varchar(255) NOT NULL,
      `link_rewrite` varchar(250) NOT NULL,
      PRIMARY KEY (`id_anblogcat`,`id_lang`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblogcat_shop` (
	    `id_anblogcat` int(11) NOT NULL DEFAULT \'0\',
  		`id_shop` int(11) NOT NULL DEFAULT \'0\',
  		PRIMARY KEY (`id_anblogcat`,`id_shop`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_comment` (
  `id_anblog_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(11) NOT NULL DEFAULT \'0\',
  `id_anblog_blog` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT \'0\',
  `date_add` datetime DEFAULT NULL,
  `user` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id_anblog_comment`,`id_shop`),
  KEY `FK_blog_comment` (`id_anblog_blog`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');


$res &= (bool)Db::getInstance()->execute('
CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_blog` (
  `id_anblog_blog` int(11) NOT NULL AUTO_INCREMENT,
  `id_anblogcat` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `date_upd` datetime NOT NULL,
  `video_code` text DEFAULT NULL,
  `params` text DEFAULT NULL,
  `products` text DEFAULT NULL,
  `featured` tinyint(1) NOT NULL,
  `indexation` int(11) NOT NULL,
  `id_employee` int(11) NOT NULL,
  `product_ids` varchar(255) DEFAULT NULL,
  `author_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_anblog_blog`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_blog_lang` (
  `id_anblog_blog` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `meta_title` varchar(250) NOT NULL,
  `link_rewrite` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  PRIMARY KEY (`id_anblog_blog`,`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_hooks` (
  `id` varchar(255) NOT NULL,
  `id_hook` int(10) NOT NULL,
  `post_count` int(10) NOT NULL,
  `status` int(1) NOT NULL,
  `id_shop` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_blog_shop` (
	    `id_anblog_blog` int(11) NOT NULL DEFAULT \'0\',
  		`id_shop` int(11) NOT NULL DEFAULT \'0\',
  		PRIMARY KEY (`id_anblog_blog`,`id_shop`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');

$res &= (bool)Db::getInstance()->execute('
	CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_blog_categories` (
	    `id_anblog_blog` int(11) NOT NULL DEFAULT \'0\',
  		`id_anblogcat` int(11) NOT NULL DEFAULT \'0\',
        `position` int(11) NOT NULL DEFAULT \'0\',
  		PRIMARY KEY (`id_anblog_blog`,`id_anblogcat`)
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
');


$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_anblogcat FROM `'._DB_PREFIX_.'anblogcat`');

if (count($rows) <= 0) {
    $res &= (bool)Db::getInstance()->execute(
        '
		INSERT INTO `'._DB_PREFIX_.'anblogcat`(`image`,`id_parent`) VALUES(\'\', 0 )
	'
    );
    $languages = Language::getLanguages(false);
    foreach ($languages as $lang) {
        $res &= (bool)Db::getInstance()->execute(
            '
			INSERT INTO `'._DB_PREFIX_.'anblogcat_lang`(`id_anblogcat`,`id_lang`,`title`) VALUES(1, '.(int)$lang['id_lang'].', \'Root\')
		'
        );
    }

    $context = Context::getContext();
    $res &= (bool)Db::getInstance()->execute(
        '
		INSERT INTO `'._DB_PREFIX_.'anblogcat_shop`(`id_anblogcat`,`id_shop`) VALUES( 1, '.(int)($context->shop->id).' )
	'
    );
}

$rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_anblog_blog FROM `'._DB_PREFIX_.'anblog_blog`');
if (count($rows) <= 0 && file_exists(_PS_MODULE_DIR_.'anblog/install/sample.php')) {
    // validate module
    include_once _PS_MODULE_DIR_.'anblog/install/sample.php';
} else {
    // validate module
    include_once _PS_MODULE_DIR_.'anblog/install/config.php';
}
/* END REQUIRED */
