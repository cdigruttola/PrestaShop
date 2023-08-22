<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($object)
{
    Configuration::updateValue('GM_OMNIPRICE_INDEX_INACTIVE', false);
    return true;
}
