<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_11($object)
{
    Configuration::updateValue('GM_OMNIPRICE_SHOW_REAL_DISCOUNT', false);
    return true;
}
