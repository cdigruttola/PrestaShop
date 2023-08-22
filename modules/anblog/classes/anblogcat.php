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

if (!defined('_PS_VERSION_')) {
    // module validation
    exit;
}

class Anblogcat extends ObjectModel
{
    public $id;
    public $id_anblogcat;
    public $image;
    public $icon_class;
    public $id_parent = 1;
    public $is_group = 0;
    public $width;
    public $submenu_width;
    public $colum_width;
    public $submenu_colum_width;
    public $item;
    public $colums = 1;
    public $type;
    public $is_content = 0;
    public $show_title = 1;
    public $type_submenu;
    public $level_depth;
    public $active = 1;
    public $position;
    public $show_sub;
    public $url;
    public $target;
    public $privacy;
    public $position_type;
    public $menu_class;
    public $content;
    public $submenu_content;
    public $level;
    public $left;
    public $right;
    public $date_add;
    public $date_upd;
    // Lang
    public $title;
    public $description;
    public $content_text;
    public $submenu_content_text;
    public $submenu_catids;
    public $is_cattree = 1;
    public $template = '';
    public $meta_title;
    public $meta_keywords;
    public $meta_description;
    public $link_rewrite;
    private $anModule = null;
    public $id_shop = '';
    public $select_data = array();
    public $randkey;
    public $groups;

    public function setModule($module)
    {
        $this->anModule = $module;
    }
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'anblogcat',
        'primary' => 'id_anblogcat',
        'multilang' => true,
        'fields' => array(
            'image' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCatalogName'
            ),
            'id_parent' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ),
            'level_depth' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ),
            'show_title' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true
            ),
            'position' => array(
                'type' => self::TYPE_INT
            ),
            'privacy' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'size' => 6
            ),
            'menu_class' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCatalogName',
                'size' => 25
            ),
            'icon_class' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isCatalogName',
                'size' => 125
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            // Lang fields
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true, 'size' => 255
            ),
            'content_text' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'required' => false
            ),
            'meta_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => false
            ),            
			'meta_description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => false
            ),
            'meta_keywords' => array('type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 255,
                'required' => false
            ),
            'link_rewrite' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isLinkRewrite',
                'required' => true,
                'size' => 128
            ),
            'randkey' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'size' => 255
            ),
            'groups' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'size' => 255
            ),
        ),
    );

    public static function findByRewrite($parrams)
    {
        $id_lang = (int)Context::getContext()->language->id;
        $id_shop = (int)Context::getContext()->shop->id;
        $id = 0;
        if (isset($parrams['link_rewrite']) && $parrams['link_rewrite']) {
            $sql = 'SELECT cl.id_anblogcat FROM '._DB_PREFIX_.'anblogcat_lang cl INNER JOIN '._DB_PREFIX_.'anblogcat_shop cs on cl.id_anblogcat=cs.id_anblogcat AND id_shop='.(int) $id_shop.' INNER JOIN '._DB_PREFIX_.'anblogcat cc on cl.id_anblogcat=cc.id_anblogcat AND cl.id_anblogcat != cc.id_parent AND link_rewrite = "'.pSQL($parrams["link_rewrite"]).'"';
            if ($row = Db::getInstance()->getRow($sql)) {
                $id = $row['id_anblogcat'];
            }
        }
        return new Anblogcat($id, $id_lang);
    }

    public static function getCategories()
    {
        $context = Context::getContext();

        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'anblogcat` sw LEFT JOIN `' . _DB_PREFIX_ . 'anblogcat_lang` sl 
			ON (sw.`id_anblogcat` = sl.`id_anblogcat`
            AND sl.`id_lang` = ' . (int) $context->language->id . ')
            WHERE sw.`active`=1';

        //echo $sql; die;
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

       

        return $categories;
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->position = self::getLastPosition((int)$this->id_parent);
        $this->level_depth = $this->calcLevelDepth();
        $res = parent::add($autodate, $null_values);
        $sql = 'INSERT INTO `'._DB_PREFIX_.'anblogcat_shop` (`id_shop`, `id_anblogcat`)
			VALUES('.(int)AnblogHelper::getIDShop().', '.(int)$this->id.')';
        $res &= Db::getInstance()->execute($sql);
        $this->cleanPositions($this->id_parent);
        return $res;
    }

    public function update($null_values = false)
    {
        $this->level_depth = $this->calcLevelDepth();
        return parent::update($null_values);
    }

    protected function recursiveDelete(&$to_delete, $id_anblogcat)
    {
        if (!is_array($to_delete) || !$id_anblogcat) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->executeS(
            '
		SELECT `id_anblogcat`
		FROM `'._DB_PREFIX_.'anblogcat`
		WHERE `id_parent` = '.(int)$id_anblogcat
        );
        foreach ($result as $row) {
            $to_delete[] = (int)$row['id_anblogcat'];
            $this->recursiveDelete($to_delete, (int)$row['id_anblogcat']);
        }
    }

    public function delete()
    {
        if ($this->id == 1) {
            return false;
        }
        $this->clearCache();

        // Get children categories
        $to_delete = array((int)$this->id);
        $this->recursiveDelete($to_delete, (int)$this->id);
        $to_delete = array_unique($to_delete);

        // Delete CMS Category and its child from database
        foreach ($to_delete as &$value) {
            $value = pSQL($value);
        }
        $list = count($to_delete) > 1 ? implode(',', $to_delete) : (int)$this->id;
        $result_blog = Db::getInstance(_PS_USE_SQL_SLAVE_)->
            executeS(
                'SELECT `id_anblog_blog` as id FROM `'._DB_PREFIX_.'anblog_blog`
                 WHERE `id_anblogcat` IN ('.$list.')'
            );
        foreach ($result_blog as $value) {
            $blog = new AnblogBlog($value['id']);
            $blog->delete();
        }


        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'anblogcat` WHERE `id_anblogcat` IN ('.$list.')'
        );
        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'anblogcat_shop` WHERE `id_anblogcat` IN ('.$list.')'
        );
        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'anblogcat_lang` WHERE `id_anblogcat` IN ('.$list.')'
        );
        anblogcat::cleanPositions($this->id_parent);
        return true;
    }

    public static function countCats()
    {
        $row = Db::getInstance()->
        executeS(
            'SELECT COUNT(id_anblogcat) as total FROM `'._DB_PREFIX_.'anblogcat` WHERE  id_anblogcat!=1 AND 1=1'
        );
        return $row[0]['total'];
    }

    public function deleteSelection($menus)
    {
        $return = 1;
        foreach ($menus as $id_anblogcat) {
            $obj_menu = new Anblogcat($id_anblogcat);
            $return &= $obj_menu->delete();
        }
        return $return;
    }

    public function calcLevelDepth()
    {
        $parentanblogcat = new Anblogcat($this->id_parent);
        if (!$parentanblogcat) {
            die('parent Menu does not exist');
        }
        return $parentanblogcat->level_depth + 1;
    }

    public function updatePosition($way, $position)
    {
        $sql = 'SELECT cp.`id_anblogcat`, cp.`position`, cp.`id_parent`
			FROM `'._DB_PREFIX_.'anblogcat` cp
			WHERE cp.`id_parent` = '.(int)$this->id_parent.'
			ORDER BY cp.`position` ASC';
        $res = Db::getInstance()->executeS($sql);
        if (!$res) {
            return false;
        }

        foreach ($res as $menu) {
            if ((int)$menu['id_anblogcat'] == (int)$this->id) {
                $moved_menu = $menu;
            }
        }

        if (!isset($moved_menu) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.'anblogcat`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way ? '> '.(int)$moved_menu['position'].' AND 
			`position` <= '.(int)$position : '< '.(int)$moved_menu['position'].' AND `position` >= '.(int)$position).'
			AND `id_parent`='.(int)$moved_menu['id_parent']
        ) && Db::getInstance()->execute(
            '
			UPDATE `'._DB_PREFIX_.'anblogcat`
			SET `position` = '.(int)$position.'
			WHERE `id_parent` = '.(int)$moved_menu['id_parent'].'
			AND `id_anblogcat`='.(int)$moved_menu['id_anblogcat']
        ));
    }

    public static function cleanPositions($id_parent)
    {
        $result = Db::getInstance()->executeS(
            '
		SELECT `id_anblogcat`
		FROM `'._DB_PREFIX_.'anblogcat`
		WHERE `id_parent` = '.(int)$id_parent.'
		ORDER BY `position`'
        );
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            $sql = '
			UPDATE `'._DB_PREFIX_.'anblogcat`
			SET `position` = '.(int)$i.'
			WHERE `id_parent` = '.(int)$id_parent.'
			AND `id_anblogcat` = '.(int)$result[$i]['id_anblogcat'];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition($id_parent)
    {
        return (
            Db::getInstance()->
                getValue(
                    'SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'anblogcat` WHERE `id_parent` = '.(int)$id_parent
                )
         );
    }

    public function getInfo($id_anblogcat, $id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT m.*, md.title, md.description, md.content_text
				FROM '._DB_PREFIX_.'megamenu m
				LEFT JOIN '._DB_PREFIX_.'anblogcat_lang md 
				ON m.id_anblogcat = md.id_anblogcat AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'anblogcat_shop bs
                 ON m.id_anblogcat = bs.id_anblogcat AND bs.id_shop = '.(int)($id_shop);
        $sql .= ' WHERE m.id_anblogcat='.(int)$id_anblogcat;

        return Db::getInstance()->executeS($sql);
    }

    public function getChild($id_anblogcat = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.*, md.*
				FROM '._DB_PREFIX_.'anblogcat m
				LEFT JOIN '._DB_PREFIX_.'anblogcat_lang md
				 ON m.id_anblogcat = md.id_anblogcat AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'anblogcat_shop bs
                 ON m.id_anblogcat = bs.id_anblogcat AND bs.id_shop = '.(int)($id_shop);
        if ($active) {
            $sql .= ' WHERE m.`active`=1 ';
        }

        if ($id_anblogcat != null) {
            // validate module
            $sql .= ' WHERE id_parent='.(int)$id_anblogcat;
        }
        $sql .= ' ORDER BY `position` ';
        return Db::getInstance()->executeS($sql);
    }

    public function getAllChild($id_anblogcat = null, $id_lang = null, $id_shop = null, $active = false)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = ' SELECT m.id_anblogcat AS id_category, m.id_anblogcat AS id_anblogcat, m.level_depth AS level_depth,
                md.title AS title,  m.id_parent, md.title AS name, m.randkey AS randkey, md.link_rewrite, m.icon_class, m.menu_class
				FROM '._DB_PREFIX_.'anblogcat m
				LEFT JOIN '._DB_PREFIX_.'anblogcat_lang md
				 ON m.id_anblogcat = md.id_anblogcat AND md.id_lang = '.(int)$id_lang
                .' JOIN '._DB_PREFIX_.'anblogcat_shop bs
                 ON m.id_anblogcat = bs.id_anblogcat AND bs.id_shop = '.(int)($id_shop);
        if ($active) {
            $sql .= ' WHERE m.`active`=1 ';
        }

        if ($id_anblogcat != null) {
            // validate module
            $sql .= ' WHERE id_parent='.(int)$id_anblogcat;
        }
        $sql .= ' ORDER BY `position` ';
        return Db::getInstance()->executeS($sql);
    }

    public function hasChild($id)
    {
        return isset($this->children[$id]);
    }

    public function getNodes($id)
    {
        if (empty($this->children[$id])) {
            return false;
        }
        return $this->children[$id];
    }

    public function getTree($id = null)
    {
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            // validate module
            $this->children[$child['id_parent']][] = $child;
        }
        $data = $this->getNodes(1);
        $tree = $this->genTree($data);
        Context::getContext()->smarty->assign(
            array(
            'tree' => $tree,
            'selected' => Tools::getValue('id_anblogcat')
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'anblog/views/templates/admin/prerender/tree.tpl');
    }

    public function getDropdown($id, $selected = 1)
    {
        $this->children = array();
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            // validate module
            $this->children[$child['id_parent']][] = $child;
        }
        $output = array(array('id' => '1', 'title' => 'Root', 'selected' => ''));
        $output = $this->genOption(1, 1, $selected, $output);

        return $output;
    }

    /**
     * @param int  $level  (default 0 )
     * @param type $output ( default array )
     * @param type $output
     */
    public function genOption($parent, $level, $selected, $output)
    {
        // module validation
        !is_null($level) ? $level : $level = 0;
        is_array($output) ? true : $output = array();

        if ($this->hasChild($parent)) {
            $data = $this->getNodes($parent);
            foreach ($data as $menu) {
                $output[] = array(
                    'id' => $menu['id_anblogcat'],
                    'title' => str_repeat('-', $level).' '.$menu['title'].' (ID:'.$menu['id_anblogcat'].')',
                    'selected' => $selected
                );
                if ($menu['id_anblogcat'] != $parent) {
                    $output = $this->genOption($menu['id_anblogcat'], $level + 1, $selected, $output);
                }
            }
        }
        return $output;
    }

    public function genTree(&$data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $id = isset($item['id_anblogcat']) ? $item['id_anblogcat'] : $item['id_category'];
                $children = $this->getAllChild($id);
                if (!empty($children)) {
                    $data[$key]['children'] = $children;
                    $this->genTree($data[$key]['children']);
                }
            }
            return $data;
        }
        return false;
    }


    public function getTreeForApPageBuilder($select = array(), $id = null)
    {
        $childs = $this->getChild($id);
        foreach ($childs as $child) {
            $this->children[$child['id_parent']][] = $child;
        }
        $data = $this->getNodes(1);
        $tree = $this->genTree($data);
        Context::getContext()->smarty->assign(
            array(
            'tree' => $tree,
            'select' => $select
            )
        );

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'anblog/views/templates/admin/prerender/treeForApPageBuilder.tpl');
    }

    public function getFrontEndTree($id, $helper)
    {
        $childs = $this->getChild(null);

        foreach ($childs as $child) {
            // validate module
            $this->children[$child['id_parent']][] = $child;
        }

        $data = $this->getNodes($id);
        $tree = $this->genFrontEndTree($data, $helper);
        Context::getContext()->smarty->assign(
            array(
                'tree' => $tree,
                'selected' => Tools::getValue('id_anblogcat')
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'anblog/views/templates/front/category_menu.tpl');
    }

    public function genFrontEndTree(&$data, $helper)
    {
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $id = isset($item['id_anblogcat']) ? $item['id_anblogcat'] : $item['id_category'];
                $params = array(
                    'rewrite' => $item['link_rewrite'],
                    'id' => $id
                );

                $category_link = $helper->getBlogCatLink($params);
                $data[$key]['category_link'] = $category_link;
                $children = $this->getAllChild($id);
                if (!empty($children)) {
                    $data[$key]['children'] = $children;
                    $this->genFrontEndTree($data[$key]['children'], $helper);
                }
            }
            return $data;
        }
        return false;
    }

    public static function autoCreateKey()
    {
        $sql = 'SELECT '.self::$definition['primary'].' FROM '._DB_PREFIX_.self::$definition['table'].
                ' WHERE randkey IS NULL OR randkey = ""';

        $rows = Db::getInstance()->executes($sql);
        foreach ($rows as $row) {
            $mod_group = new Anblogcat((int)$row[self::$definition['primary']]);
            include_once _PS_MODULE_DIR_.'anblog/libs/Helper.php';
            $mod_group->randkey = AnblogHelper::genKey();
            try {
                // Try caught to remove validate
                $mod_group->update();
            } catch (Exception $exc) {
            }
        }
    }
}
