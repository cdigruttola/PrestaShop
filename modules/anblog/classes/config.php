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

class AnblogConfig
{
    public $params;
    public $cat_image_dir = '';
    /**
     * @var int id_lang current language in for, while
     */
    public $cur_id_lang = '';
    /**
     * @var int id_lang current language in for, while
     */
    public $cur_prefix_rewrite = '';
    const CACHE_HOOK_ID = 'anblogHooks';

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            // validate module
            $instance = new AnblogConfig();
        }
        return $instance;
    }

    public function __construct()
    {
       

        $defaultSettings = include _PS_MODULE_DIR_ . 'anblog/config.php';

        $valuesWithLang = ['blog_link_title', 'meta_title', 'meta_description', 'meta_keywords', 'category_rewrite', 'detail_rewrite'];

        $allConfig = [];

        foreach ($defaultSettings as $key => $value) {
            if (in_array($key, $valuesWithLang)) {
                $allConfig[$key] = Configuration::get('an_bl_' . $key , Context::getContext()->language->id, $value);
            } else {
                $allConfig[$key] = Configuration::get('an_bl_' . $key , null, $value);
            }
        } 

        $this->params = $allConfig;


        // $data = self::getConfigValue('cfg_global');

        // if ($data && $tmp = unserialize($data)) {
        //     // validate module
        //     $this->params = $tmp;
        // }


    }

    public function mergeParams($params)
    {
        // validate module
        unset($params);
    }

    public function setVar($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function get($name, $value = '')
    {
        if (isset($this->params[$name])) {
            // validate module
            return $this->params[$name];
        }
        return $value;
    }

    public static function getConfigName($name)
    {
        return Tools::strtoupper(_AN_BLOG_PREFIX_.$name);
    }

    public static function updateConfigValue($name, $value = '')
    {
        Configuration::updateValue(self::getConfigName($name), $value, true);
    }

    public static function getConfigValue($name)
    {
        return Configuration::get(self::getConfigName($name));
    }

    public static function getHooksValue($id_shop = null)
    {
        $cache_id = self::CACHE_HOOK_ID . '_' . $id_shop;
        if (!Cache::isStored($cache_id)) {
            Cache::store(
                $cache_id,
                serialize(
                    DB::getInstance(_PS_USE_SQL_SLAVE_)->
                        executeS(
                            'SELECT * FROM '._DB_PREFIX_.'anblog_hooks 
                                  LEFT JOIN '._DB_PREFIX_.'hook on 
                                  '._DB_PREFIX_.'anblog_hooks.id_hook = '._DB_PREFIX_.'hook.id_hook '
                                  .($id_shop ? 'WHERE `id_shop` = '.$id_shop : ''). ';'
                        )
                )
            );
        }
        return unserialize(Cache::retrieve($cache_id));
    }

    public static function updateHooksValues($hooksArray, $id_shop)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'anblog_hooks (`id`, `id_hook`, `status`, `post_count`, `id_shop`)  VALUES ';
        $i = 0;
        foreach ($hooksArray as $key => $hook) {
            $postCount = $hook["postCount"] ? $hook["postCount"] : 3;
            $sql .=  ' ( CONCAT(
                            (SELECT  `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = "'.$key.'" ),
                            "-'.$id_shop.'"
                         ) ,
                        (SELECT  `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = "'.$key.'" ), 
                         '.$hook["status"].', 
                         '.$postCount.', 
                         '.$id_shop.')';
            if ($i != sizeof($hooksArray) - 1) {
                $sql .= ',';
            }
            $i++;
        }
        $sql .= ' ON DUPLICATE KEY UPDATE `status`= VALUES(status), `post_count`= VALUES(post_count);';
        Cache::clean(self::CACHE_HOOK_ID);
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }
}
