<?php
/**
 * 2020 Anvanto
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author Anvanto (anvantoco@gmail.com)
 *  @copyright  2020 anvanto.com

 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_5($object, $install = false)
{

    $ret = (bool)Db::getInstance()->execute('
      ALTER TABLE `' . _DB_PREFIX_ . 'anblogcat_lang`
        ADD (`meta_title` text);
    ');

    $ret &= (bool)Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'anblog_blog_categories` (
            `id_anblog_blog` int(11) NOT NULL DEFAULT \'0\',
            `id_anblogcat` int(11) NOT NULL DEFAULT \'0\',
            `position` int(11) NOT NULL DEFAULT \'0\',
            PRIMARY KEY (`id_anblog_blog`,`id_anblogcat`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
    ');
    return $ret;
}
