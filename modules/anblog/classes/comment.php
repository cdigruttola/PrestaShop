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
    // module validation
    exit;
}

class AnblogComment extends ObjectModel
{
    /**
     * @var string Name
     */
    public $user;
    public $comment;
    public $active;
    public $id_anblog_blog;
    public $date_add;
    public $email;
    public $id_shop;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'anblog_comment',
        'primary' => 'id_anblog_comment',
        'fields' => array(
            'id_anblog_blog' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'user' => array('type' => self::TYPE_STRING, 'required' => false),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128, 'required' => true),
            'comment' => array('type' => self::TYPE_STRING, 'required' => true),
            'active' => array('type' => self::TYPE_BOOL),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => false)
        ),
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->id_shop = AnblogHelper::getIDShop();
        return parent::add($autodate, $null_values);
    }

    public static function countComments($id_anblog_blog = 0, $is_active = false, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }

        $query = ' SELECT count(id_anblog_comment) as total FROM '._DB_PREFIX_.'anblog_comment WHERE 1=1 ';

        if ($id_anblog_blog > 0) {
            // validate module
            $query .= ' AND id_anblog_blog='.(int)$id_anblog_blog;
        }
        if ($is_active) {
            // validate module
            $query .= ' AND active=1 ';
        }
        if ($id_shop) {
            $query .= ' AND id_shop='.(int)$id_shop;
        }

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        return $data[0]['total'];
    }

    public static function getComments($id_anblog_blog, $limit, $id_lang, $order = null, $by = null, $id_shop = null)
    {
        // validate module
        !is_null($limit) ? true : $limit = 10;
        unset($id_anblog_blog);
        unset($order);
        unset($by);
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }
        $query = ' SELECT c.*, b.meta_title FROM '._DB_PREFIX_.'anblog_comment c';
        $query .= ' LEFT JOIN '._DB_PREFIX_.'anblog_blog_lang b ON c.id_anblog_blog=b.id_anblog_blog';
        $query .= ' AND b.id_lang='.(int)$id_lang.' WHERE id_shop='.(int)$id_shop;
        $query .= ' LIMIT '.$limit;

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }

    public function getList($id_anblog_blog, $id_lang, $page_number = 0, $nb_products = 10, $order_by = null, $order_way = null, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }

        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_anblog_blog' || $order_by == 'date_add' || $order_by == 'date_upd' || $order_by == 'title') {
            $order_by_prefix = 'c';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $query = ' SELECT c.* FROM '._DB_PREFIX_.'anblog_comment c';
        $query .= ' WHERE 1=1 AND id_shop='.(int)$id_shop;

        $query .= ' AND active=1 AND id_anblog_blog='.(int)$id_anblog_blog;
        $query .= '  ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '')
            .pSQL($order_by).' '.pSQL($order_way).' LIMIT '.(($page_number - 1) * $nb_products).', '.(int)$nb_products;

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }
}
