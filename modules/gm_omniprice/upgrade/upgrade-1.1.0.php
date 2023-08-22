<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0($object)
{
    Configuration::updateValue('GM_OMNIPRICE_SHOW_IF_NO_HISTORY', false);
    return true;
}
