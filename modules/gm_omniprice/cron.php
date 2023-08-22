<?php

$start = microtime(true);
require('template.php');
require_once(dirname(__FILE__) . '/../../config/config.inc.php');

$omni = Module::getInstanceByName('gm_omniprice');
$token = Tools::getValue('token');
$comparedToken = Tools::getAdminToken('gm_omniprice');
if ($token != $comparedToken) {
    die('invalid token');
}
echo 'PS ' . _PS_VERSION_ . '<br/>';
echo 'OmniPrice ' . $omni->version . '<br/>';
if (Tools::isSubmit('reset')) {
    $omni->resetIndex();
    echo 'RESET</br>';
}
$productId = null;
if (Tools::isSubmit('pid')) {
    $productId = (int) Tools::getValue('pid');
    $omni->removeProductFromTodaysIndex($productId);
    $omni->removeProductFromTodaysHistory($productId);
}
$omni->savePrices(true, $productId);
echo 'FINISH<br/>';
if (Tools::isSubmit('debug')) {
    echo 'DEBUG:<br/>';
    $debug = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gm_omniprice_history` WHERE `id_product` = ' . $productId);
    if ($debug) {
        echo $omni->displayTable($debug, array_keys($debug[0]));
    }
    $debug = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gm_omniprice_cache` WHERE `id_product` = ' . $productId);
    if ($debug) {
        echo $omni->displayTable($debug, array_keys($debug[0]));
    }
}
$timeElapsedSeconds = microtime(true) - $start;
echo round($timeElapsedSeconds, 4) . ' s<br/>';
