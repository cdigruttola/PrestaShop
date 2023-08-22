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

if (!defined('_PS_VERSION_')) {
    exit;
}
function upgrade_module_3_2_0($object)
{
    $tabs = [
        [
            'class_name' => 'AdminAnblogSettings',
            'parent' => 'AdminAnblogManagement',
            'name' => 'Settings',
			'active' => 1
        ],	
    ];

    $languages = Language::getLanguages();
    foreach ($tabs as $tab) {
        $_tab = new Tab();
        $_tab->active = $tab['active'];
        $_tab->class_name = $tab['class_name'];
        $_tab->id_parent = Tab::getIdFromClassName($tab['parent']);
        if (empty($_tab->id_parent)) {
            $_tab->id_parent = 0;
        }

        $_tab->module = 'anblog';
        foreach ($languages as $language) {
            $_tab->name[$language['id_lang']] = $tab['name'];
        }

        $_tab->add();
    }
	
    $id_tab = Tab::getIdFromClassName('AdminAnblogDashboard');
    if ($id_tab != 0) {
        $tab = new Tab($id_tab);
        $tab->delete();
    }

    //  OLD - NEW Config
    $valuesWithLang = ['blog_link_title', 'meta_title', 'meta_description', 'meta_keywords', 'category_rewrite', 'detail_rewrite'];
    $languages = Language::getLanguages(false);
    $data = Configuration::get('ANBLOG_CFG_GLOBAL');
    if ($data && $tmp = unserialize($data)) {
        foreach ($tmp as $key => $value){
            $key = str_replace('_1', '', $key);
            if (in_array($key, $valuesWithLang)) {
                $valueLang = [];
                foreach ($languages  as $language){
                    $valueLang[$language['id_lang']] = $value;
                }      
                Configuration::updateValue('an_bl_' . $key, $valueLang);
            } else {
                Configuration::updateValue('an_bl_' . $key, $value);
            }
        }
    }

    return true;
}
