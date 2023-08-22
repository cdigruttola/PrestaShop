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

include_once _PS_MODULE_DIR_.'anblog/libs/Helper.php';

class AnblogBlog extends ObjectModel
{
    /**
     * @var string Name
     */
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $content;
    public $description;
    public $video_code;
    public $image = '';
    public $thumb = '';
    public $link_rewrite;
    public $id_anblogcat;
    public $indexation;
    public $active;
    public $id_anblog_blog;
    public $date_add;
    public $date_upd;
    public $hits = 0;
    public $id_employee;
    public $tags;
    public $shops = array();
    public $categories = array();
    public $positions = array();
    /**
     * @var string
     */
    public $products = 'a:0:{}';
    /**
     * @see ObjectModel::$definition
     */
    //DONGND:: add author name
    public $author_name;
    
    public static $definition = array(
        'table' => 'anblog_blog',
        'primary' => 'id_anblog_blog',
        'multilang' => true,
        'fields' => array(
            'image' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'lang' => false),
            'id_employee' => array('type' => self::TYPE_INT),
            'indexation' => array('type' => self::TYPE_BOOL),
            'active' => array('type' => self::TYPE_BOOL),
            'thumb' => array('type' => self::TYPE_STRING, 'lang' => false,),
                'hits' => array('type' => self::TYPE_INT),
                'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'video_code' => array('type' => self::TYPE_HTML, 'validate' => 'isString', 'required' => false),
            'author_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            // Lang fields
            'meta_description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255
            ),
            'meta_keywords' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255
            ),
            'tags' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255
            ),
            'meta_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 128
            ),
            'link_rewrite' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128
            ),
            'content' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'size' => 3999999999999
            ),
            'description' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 3999999999999
            ),
            'products' => array('type' => self::TYPE_STRING),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'content',
        'objectsNodeName' => 'content_management_system',
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->imageObject = new AnblogImage($this);
		
        if ((int)$this->id) {
            $sql = 'SELECT id_shop FROM `'._DB_PREFIX_.'anblog_blog_shop` '
            .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            foreach (Db::getInstance()->executeS($sql) as $id_shop) {
                $this->shops[] = $id_shop['id_shop'];
            }
            $sql = 'SELECT id_anblogcat, position FROM `'._DB_PREFIX_.'anblog_blog_categories` '
            .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            foreach (Db::getInstance()->executeS($sql) as $id_category) {
                $this->categories[] = $id_category['id_anblogcat'];
                $this->positions[$id_category['id_anblogcat']] = $id_category['position'];
            }
        } else {
            $shops = Shop::getContextListShopID();
            if (count($shops)) {
                foreach ($shops as $shop_id) {
                    $this->shops[] = $shop_id;
                }
            }
        }
        $this->products = Tools::unSerialize($this->products);
        if (!$this->products || !is_array($this->products)) {
            $this->products = array();
        }
    }

    public static function findByRewrite($parrams)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;
        $id = 0;
        if (isset($parrams['link_rewrite']) && $parrams['link_rewrite']) {
            $sql = '
            SELECT bl.id_anblog_blog, bl.id_lang FROM '._DB_PREFIX_.'anblog_blog_lang bl 
            INNER JOIN '._DB_PREFIX_.'anblog_blog_shop bs 
                ON (bl.id_anblog_blog = bs.id_anblog_blog AND id_shop = '. (int) $id_shop.')
             WHERE  link_rewrite = "'.pSQL($parrams['link_rewrite']).'" AND bl.`id_lang` = '.$id_lang.' 
                ';

            if ($row = Db::getInstance()->getRow($sql)) {
                $id = $row['id_anblog_blog'];
            }
        }
        return new AnblogBlog($id, $id_lang);
    }

    public function add($autodate = true, $null_values = false)
    {
        $shops = array();
        if (!Shop::isFeatureActive()) {
            $shops[] = Shop::getContextShopID();
        } else {
            $shops = Tools::getValue('shops', array());
        }
        $categories = Tools::getValue('categories', array());
        $this->products = serialize($this->products);
        $res = parent::add($autodate, $null_values);
        /*if (isset($_FILES['image_link']) && isset($_FILES['image_link']['tmp_name']) && !empty($_FILES['image_link']['tmp_name'])) {
            $this->imageObject->uploadNew($this->id);
        }*/

        foreach ($shops as $id_shop) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'anblog_blog_shop` (`id_shop`, `id_anblog_blog`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')';
            $res &= Db::getInstance()->execute($sql);
        }
        foreach ($categories as $id_category) {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'anblog_blog_categories` (`id_anblogcat`, `id_anblog_blog`)
                VALUES('.(int)$id_category.', '.(int)$this->id.')';
            $res &= Db::getInstance()->execute($sql);
        }

        foreach ($categories as $category) {
            $this->cleanPositions((int)$category);
        }
        return $res;
    }

    public function update($null_values = false)
    {
        $shops = array();
        if (!Shop::isFeatureActive()) {
            $shops[] = Shop::getContextShopID();
        } else {
            $shops = Tools::getValue('shops', array());
        }
        $categories = Tools::getValue('categories', array());
        $this->products = serialize($this->products);
        if (parent::update($null_values)) {
            $res = true;
            $sql = 'DELETE FROM `'._DB_PREFIX_.'anblog_blog_shop` '
                .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            Db::getInstance()->execute($sql);
            foreach ($shops as $id_shop) {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'anblog_blog_shop` (`id_shop`, `id_anblog_blog`)
                VALUES('.(int)$id_shop.', '.(int)$this->id.')';
                $res &= Db::getInstance()->execute($sql);
            }
            $sql = 'DELETE FROM `'._DB_PREFIX_.'anblog_blog_categories` '
                .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            Db::getInstance()->execute($sql);
            foreach ($categories as $id_category) {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'anblog_blog_categories` (`id_anblogcat`, `id_anblog_blog`)
                VALUES('.(int)$id_category.', '.(int)$this->id.')';
                $res &= Db::getInstance()->execute($sql);
            }
            return $res & $this->cleanPositions($this->id_anblogcat);
        }
        return false;
    }

    public function updateField($id, $fields)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'anblog_blog` SET ';
        $last_key = current(array_keys($fields));
        foreach ($fields as $field => $value) {
            $sql .= $field." = '".$value."'";
            if ($field != $last_key) {
                // validate module
                $sql .= ',';
            }
        }

        $sql .= ' WHERE `id_anblog_blog`='.(int)$id;
        return Db::getInstance()->execute($sql);
    }

    public function delete()
    {
        if (parent::delete()) {
            // BLOG_SHOP
            $sql = 'DELETE FROM `'._DB_PREFIX_.'anblog_blog_shop` '
                    .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM `'._DB_PREFIX_.'anblog_blog_categories` '
                    .'WHERE `id_anblog_blog` IN ('.(int)$this->id.')';
            Db::getInstance()->execute($sql);
            
            //delete comment
            $result_comment = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT `id_anblog_comment` as id FROM `'._DB_PREFIX_.'anblog_comment` 
                WHERE `id_anblog_blog` = '.(int)$this->id
            );
            foreach ($result_comment as $value) {
                $comment = new AnblogComment($value['id']);
                $comment->delete();
            }
			
			$this->imageObject->delete();
        
            return $this->cleanPositions($this->id_anblogcat);
        }
        return false;
    }

    /**
     * @param Array   $condition ( default array )
     * @param Boolean $is_active ( default false )
     */
    public static function getListBlogs($id_category, $id_lang, $page_number, $nb_products, $order_by, $order_way, $condition = array(), $is_active = false, $id_shop = null)
    {
        // module validation
        if ($nb_products !== 'all'){
            !is_null($nb_products) ? true : $nb_products = 10;

            if ($nb_products < 1) {
                $nb_products = 10;
            }
        } 
        
        !is_null($page_number) ? true : $page_number = 0;

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

        $where = '';

        if ($id_category) {
            // validate module
            $where .= ' AND abbc.id_anblogcat='.(int)$id_category;
        }

        if ($id_shop) {
            // validate module
            $where .= ' AND s.id_shop='.(int)$id_shop;
        }


        if (isset($condition['type'])) {
            switch ($condition['type']) {
                case 'author':
                    if (isset($condition['id_employee'])) {
                        $where .= ' AND id_employee='.(int)$condition['id_employee'].' AND (author_name = "" OR author_name is null)';
                    } else {
                        $where .= ' AND author_name LIKE "%'.pSQL($condition['author_name']).'%"';
                    }
                    break;

                case 'tag':
                    $tmp = explode(',', $condition['tag']);

                    if (!empty($tmp) && count($tmp) > 1) {
                        $t = array();
                        foreach ($tmp as $tag) {
                            // validate module
                            $t[] = 'l.tags LIKE "%'.pSQL(trim($tag)).'%"';
                        }
                        $where .= ' AND ('.implode(' OR ', $t).') ';
                    } else {
                        // validate module
                        $where .= ' AND l.tags LIKE "%'.pSQL($condition['tag']).'%"';
                    }
                    break;
                case 'samecat':
                    $where .= ' AND c.id_anblog_blog!='.(int)$condition['id_anblog_blog'];
                    break;
            }
        }

        if ($is_active) {
            // validate module
            $where .= ' AND c.active=1';
        }

        $query = '
        SELECT c.*, l.*, l.meta_title as title, blc.link_rewrite as category_link_rewrite , blc.title as category_title
        FROM  '._DB_PREFIX_.'anblog_blog c
        LEFT JOIN '._DB_PREFIX_.'anblog_blog_lang l
         ON (c.id_anblog_blog = l.id_anblog_blog) and  l.id_lang='.(int)$id_lang
                .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_shop s
                 ON  (c.id_anblog_blog = s.id_anblog_blog) and s.id_shop='.(int)$id_shop
                .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_categories abbc ON  abbc.id_anblog_blog = c.id_anblog_blog '
                .' LEFT JOIN '._DB_PREFIX_.'anblogcat bc ON  bc.id_anblogcat = abbc.id_anblogcat '
                .' LEFT JOIN '._DB_PREFIX_.'anblogcat_lang blc
                 ON blc.id_anblogcat=bc.id_anblogcat and blc.id_lang='.(int)$id_lang
                .' '.Shop::addSqlAssociation('blog', 'c').'
        WHERE l.id_lang = '.(int)$id_lang.$where.'
        GROUP BY c.id_anblog_blog
         ';

        $query .= 'ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').pSQL($order_by).' '
                .pSQL($order_way).'';
                
        if ($nb_products !== 'all'){
            $query .= ' LIMIT '.(int)(($page_number - 1) * $nb_products).', '.(int)$nb_products.'';
        }

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return $data;
    }

    /**
     * @param Array $condition ( default array )
     * @param Boolean $is_active ( default false )
     */

    public static function countBlogs($id_category, $id_lang = false, $condition = array(), $is_active = false, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }

        if (!$id_lang){
            $id_lang = Context::getContext()->language->id;
        }

        $where = '';
        if ($id_category) {
            // validate module
            $where .= 'AND abc.id_anblogcat='.(int)$id_category;
        }
        if ($is_active) {
            // validate module
            $where .= ' AND c.active=1';
        }

        if (isset($condition['type'])) {
            switch ($condition['type']) {
                case 'author':
                    if (isset($condition['id_employee'])) {
                        $where .= ' AND id_employee='.(int)$condition['id_employee'].'
                         AND (author_name = "" OR author_name is null)';
                    } else {
                        $where .= ' AND author_name LIKE "%'.pSQL($condition['author_name']).'%"';
                    }
                    break;

                case 'tag':
                    $tmp = explode(',', $condition['tag']);

                    if (!empty($tmp) && count($tmp) > 1) {
                        $t = array();
                        foreach ($tmp as $tag) {
                            // validate module
                            $t[] = 'sl.tags LIKE "%'.pSQL(trim($tag)).'%"';
                        }
                        $where .= ' AND  '.implode(' OR ', $t).' ';
                    } else {
                        // validate module
                        $where .= ' AND sl.tags LIKE "%'.pSQL($condition['tag']).'%"';
                    }
                    break;
                case 'samecat':
                    $where .= ' AND c.id_anblog_blog!='.(int)$condition['id_anblog_blog'];
                    break;
            }
        }

        $query = '
		SELECT  *
		FROM  `'._DB_PREFIX_.'anblog_blog` c
            LEFT JOIN `' . _DB_PREFIX_ . 'anblog_blog_lang` sl 
            ON (c.`id_anblog_blog` = sl.`id_anblog_blog`
            AND sl.`id_lang` = ' . (int) $id_lang . '),
        `'._DB_PREFIX_.'anblog_blog_categories` abc
        ';

        $query .= 'WHERE c.`id_anblog_blog`=abc.`id_anblog_blog` '.$where.'  ';
        
        if ($id_shop) {
			$query .= ' AND c.`id_anblog_blog` IN (
				SELECT hs.`id_anblog_blog`
				FROM `' . _DB_PREFIX_ . 'anblog_blog_shop` hs
				WHERE hs.`id_shop`='.(int) $id_shop.')
			';
        }

		$query .= ' GROUP BY c.id_anblog_blog
        
        ';

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        return count($data);
    }

    // public static function countBlogs($id_category, $id_lang, $condition = array(), $is_active = false, $id_shop = null)
    // {
    //     if (!$id_shop) {
    //         $context = Context::getContext();
    //         $id_shop = $context->shop->id;
    //     }
    //     $where = '';
    //     if ($id_category) {
    //         // validate module
    //         $where .= ' AND c.id_anblogcat='.(int)$id_category;
    //     }
    //     if ($is_active) {
    //         // validate module
    //         $where .= ' AND c.active=1';
    //     }
    //     if ($id_shop) {
    //         // validate module
    //         $where .= ' AND s.id_shop='.(int)$id_shop;
    //     }
    //     if (isset($condition['type'])) {
    //         switch ($condition['type']) {
    //             case 'author':
    //                 if (isset($condition['id_employee'])) {
    //                     $where .= ' AND id_employee='.(int)$condition['id_employee'].'
    //                      AND (author_name = "" OR author_name is null)';
    //                 } else {
    //                     $where .= ' AND author_name LIKE "%'.pSQL($condition['author_name']).'%"';
    //                 }
    //                 break;

    //             case 'tag':
    //                 $tmp = explode(',', $condition['tag']);

    //                 if (!empty($tmp) && count($tmp) > 1) {
    //                     $t = array();
    //                     foreach ($tmp as $tag) {
    //                         // validate module
    //                         $t[] = 'l.tags LIKE "%'.pSQL(trim($tag)).'%"';
    //                     }
    //                     $where .= ' AND  '.implode(' OR ', $t).' ';
    //                 } else {
    //                     // validate module
    //                     $where .= ' AND l.tags LIKE "%'.pSQL($condition['tag']).'%"';
    //                 }
    //                 break;
    //             case 'samecat':
    //                 $where .= ' AND c.id_anblog_blog!='.(int)$condition['id_anblog_blog'];
    //                 break;
    //         }
    //     }
    //     $query = '
	// 	SELECT  c.id_anblog_blog
	// 	FROM  '._DB_PREFIX_.'anblog_blog c
	// 	LEFT JOIN '._DB_PREFIX_.'anblog_blog_lang l
	// 	 ON (c.id_anblog_blog = l.id_anblog_blog) and  l.id_lang='.(int)$id_lang
    //             .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_shop s ON  (c.id_anblog_blog = s.id_anblog_blog) '
    //             .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_categories abc ON  abc.id_anblog_blog = c.id_anblog_blog '
    //             .' LEFT JOIN '._DB_PREFIX_.'anblogcat bc ON  bc.id_anblogcat = abc.id_anblogcat '
    //             .' LEFT JOIN '._DB_PREFIX_.'anblogcat_lang blc
    //              ON blc.id_anblogcat=bc.id_anblogcat and blc.id_lang='.(int)$id_lang
    //             .'
	// 	WHERE l.id_lang = '.(int)$id_lang.$where.'
	// 	GROUP BY c.id_anblog_blog
	// 	 ';

    //     $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

    //     return count($data);
    // }

    public static function listblog($id_lang = null, $id_block = false, $active = true, $id_shop = null)
    {
        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }

        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
		SELECT c.id_anblog_blog, l.meta_title
		FROM  '._DB_PREFIX_.'blog c
		JOIN '._DB_PREFIX_.'blog_lang l ON (c.id_anblog_blog = l.id_anblog_blog)
                JOIN '._DB_PREFIX_.'anblog_blog_lang s ON (c.id_anblog_blog = s.id_anblog_blog)
		'.Shop::addSqlAssociation('blog', 'c').'
		'.(($id_block) ? 'JOIN '._DB_PREFIX_.'block_blog b ON (c.id_anblog_blog = b.id_anblog_blog)' : '').'
		WHERE s.id_shop = '.(int)$id_shop.' 
		AND l.id_lang = '.(int)$id_lang.
            (($id_block) ? ' AND b.id_block = '.(int)$id_block : '').
            ($active ? ' AND c.`active` = 1 ' : '').'
		GROUP BY c.id_anblog_blog
		ORDER BY c.`position`'
        );
    }

    public function updatePosition($way, $position)
    {
        $sql = 'SELECT abc.`id_anblog_blog`, abc.`position`, abc.`id_anblogcat`
			FROM `'._DB_PREFIX_.'blog` cp
            LEFT JOIN '._DB_PREFIX_.'anblog_blog_categories abc ON  abc.id_anblog_blog = cp.id_anblog_blog
			WHERE abc.`id_anblogcat` = '.(int)$this->id_anblogcat.'
			ORDER BY abc.`position` ASC';
        if (!$res = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($res as $blog) {
            if ((int)$blog['id_anblog_blog'] == (int)$this->id) {
                $moved_blog = $blog;
            }
        }

        if (!isset($moved_blog) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.'anblog_blog_categories`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way ? '> '.(int)$moved_blog['position'].' 
			AND `position` <= '.(int)$position : '< '.(int)$moved_blog['position'].' 
			AND `position` >= '.(int)$position).'
			AND `id_anblogcat`='.(int)$moved_blog['id_anblogcat']
        ) && Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.'anblog_blog_categories`
			SET `position` = '.(int)$position.'
			WHERE `id_anblog_blog` = '.(int)$moved_blog['id_anblog_blog'].'
			AND `id_anblogcat`='.(int)$moved_blog['id_anblogcat']
        ));
    }

    public static function cleanPositions($id_category)
    {
        $sql = '
		SELECT `id_anblog_blog`
		FROM `'._DB_PREFIX_.'anblog_blog_categories`
		WHERE `id_anblogcat` = '.(int)$id_category.'
		ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'anblog_blog_categories`
                    SET `position` = '.(int)$i.'
                    WHERE `id_anblogcat` = '.(int)$id_category.'
                    AND `id_anblog_blog` = '.(int)$result[$i]['id_anblog_blog'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_category)
    {
        $sql = '
		SELECT MAX(position) + 1
		FROM `'._DB_PREFIX_.'anblog_blog_categories`
		WHERE `id_anblogcat` = '.(int)$id_category;
        return (Db::getInstance()->getValue($sql));
    }

    public static function getblogPages($id_lang = null, $id_anblogcat = null, $active = true, $id_shop = null)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('blog', 'c');
        if ($id_lang) {
            $sql->innerJoin(
                'blog_lang',
                'l',
                'c.id_anblog_blog = l.id_anblog_blog AND l.id_lang = '.(int)$id_lang
            );
        }

        if ($id_shop) {
            $sql->innerJoin(
                'blog_shop',
                'cs',
                'c.id_anblog_blog = cs.id_anblog_blog AND cs.id_shop = '.(int)$id_shop
            );
        }

        if ($active) {
            $sql->where('c.active = 1');
        }

        if ($id_anblogcat) {
            $sql->innerJoin(
                'blog_category',
                'bc',
                'c.id_anblog_blog = bc.id_anblog_blog'
            );
            $sql->where('bc.id_anblogcat = '.(int)$id_anblogcat);
        }

        $sql->orderBy('position');

        return Db::getInstance()->executeS($sql);
    }

    public static function getUrlRewriteInformations($id_anblog_blog)
    {
        $sql = 'SELECT l.`id_lang`, c.`link_rewrite`
				FROM `'._DB_PREFIX_.'anblog_blog_lang` AS c
				LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
				WHERE c.`id_anblog_blog` = '.(int)$id_anblog_blog.'
				AND l.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * This function is build for module ApPageBuilder, most logic query Sqlis clone from function getListBlog.
     *
     * @param Int     $id_category ( default '' )
     * @param Int     $nb_blog     ( default 10 )
     * @param Array   $condition   ( default array )
     * @param Boolean $is_active   ( default false )
     */
    public static function getListBlogsForApPageBuilder($id_category, $id_lang, $nb_blog, $order_by, $order_way, $condition, $is_active, $id_shop = null)
    {
        // module validation
        !is_null($id_category) ? true : $id_category = '';
        !is_null($nb_blog) ? true : $nb_blog = 10;
        is_array($condition) ? true : $condition = array();
        !is_null($is_active) ? true : $is_active = false;

        if (!$id_shop) {
            $context = Context::getContext();
            $id_shop = $context->shop->id;
        }
        if (empty($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        if ($nb_blog < 1) {
            $nb_blog = 10;
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
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $where = '';
        if ($id_category) {
            $where .= ' AND abc.id_anblogcat IN('.$id_category.') ';
        }
        if ($id_shop) {
            $where .= ' AND s.id_shop='.(int)$id_shop;
        }
        if (isset($condition['type'])) {
            switch ($condition['type']) {
                case 'author':
                    $where .= ' AND id_employee='.(int)$condition['id_employee'];
                    break;
                case 'tag':
                    $tmp = explode(',', $condition['tag']);
                    if (!empty($tmp) && count($tmp) > 1) {
                        $t = array(); // validate module
                        foreach ($tmp as $tag) {
                            $t[] = 'l.tags LIKE "%'.pSQL(trim($tag)).'%"';
                        }
                        $where .= ' AND  '.implode(' OR ', $t).' ';
                    } else {
                        $where .= ' AND l.tags LIKE "%'.pSQL($condition['tag']).'%"';
                    }
                    break;
                case 'samecat':
                    $where .= ' AND c.id_anblog_blog!='.(int)$condition['id_anblog_blog'];
                    break;
            }
        }
        if ($is_active) {
            $where .= ' AND c.active=1';
        }
        $query = '
			SELECT c.*, l.*, l.meta_title as title, blc.link_rewrite as category_link_rewrite , blc.title as category_title
			FROM  '._DB_PREFIX_.'anblog_blog c
			LEFT JOIN '._DB_PREFIX_.'anblog_blog_lang l
			 ON (c.id_anblog_blog = l.id_anblog_blog) and  l.id_lang='.(int)$id_lang
                .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_shop s ON  (c.id_anblog_blog = s.id_anblog_blog) '
                .' LEFT JOIN '._DB_PREFIX_.'anblog_blog_categories abc ON  (c.id_anblog_blog = abc.id_anblog_blog) '
                .' LEFT JOIN '._DB_PREFIX_.'anblogcat bc ON  bc.id_anblogcat = abc.id_anblogcat '
                .' LEFT JOIN '._DB_PREFIX_.'anblogcat_lang blc
                 ON blc.id_anblogcat=bc.id_anblogcat and blc.id_lang='.(int)$id_lang
                .' '.Shop::addSqlAssociation('blog', 'c').'
			WHERE l.id_lang = '.(int)$id_lang.$where.'
			GROUP BY c.id_anblog_blog ';

        if ($order_way == 'random') {
            $query .= 'ORDER BY rand() LIMIT 0, '.(int)$nb_blog;
        } else {
            $query .= 'ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '')
                    .pSQL($order_by).' '.pSQL($order_way).' LIMIT 0, '.(int)$nb_blog;
        }

        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        return $data;
    }

    /**
     * @param null $id_lang
     * @return array
     */
    public function getProductsAutocompleteInfo($id_lang = null)
    {
        $id_lang = is_null($id_lang) ? Context::getContext()->language->id : $id_lang;

        $products = array();

        if (!empty($this->products)) {
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT p.`id_product`, p.`reference`, pl.name
                FROM `' . _DB_PREFIX_ . 'product` p
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' .
                (int)$id_lang . ')
                WHERE p.`id_product` IN (' . implode(',', array_map('intval', $this->products)) . ')'
            );

            foreach ($rows as $row) {
                $products[$row['id_product']] = trim($row['name']) . (!empty($row['reference']) ?
                        ' (ref: ' . $row['reference'] . ')' : '');
            }
        }

        return $products;
    }

    /**
     * @param null $array_product_id
     * @param null $id_lang
     * @return bool
     */
    public static function getProductsByArrayId($array_product_id = null, $id_lang = null)
    {
        if (empty($array_product_id)) {
            return false;
        }

        $context = Context::getContext();
        $id_lang = is_null($id_lang) ? $context->language->id : $id_lang;

        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`,
            pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, 
            pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, 
            m.`name` AS manufacturer_name,
            DATEDIFF(
                product_shop.`date_add`,
                DATE_SUB(
                    NOW(),
                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl'));
        $sql->leftJoin('image', 'i', 'i.`id_product` = p.`id_product`');
        $sql->join(Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1'));
        $sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('p.`id_product` IN (' . implode(',', array_map('intval', $array_product_id)) . ')');

        $sql->groupBy('product_shop.id_product');

        if (Combination::isFeatureActive()) {
            $sql->select('MAX(product_attribute_shop.id_product_attribute) id_product_attribute');
            $sql->leftOuterJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
            $sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on = 1'));
        }
        $sql->join(Product::sqlStock('p', Combination::isFeatureActive() ? 'product_attribute_shop' : 0));

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        return Product::getProductsProperties((int)$id_lang, $result);
    }
}
