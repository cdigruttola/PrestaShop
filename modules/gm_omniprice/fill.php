<?php

require_once('../../config/config.inc.php');

$omni = Module::getInstanceByName('gm_omniprice');
$token = Tools::getValue('token');
$comparedToken = Tools::getAdminToken('gm_omniprice');
if ($token != $comparedToken) {
    die('invalid token');
}
$omni->fillMissingCache(true);
echo 'FINISH<br/>';