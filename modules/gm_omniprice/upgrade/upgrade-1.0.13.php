<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_13($object)
{
    Configuration::updateValue('GM_OMNIPRICE_TEXT_COLOR', '#FFFFFF');
    Configuration::updateValue('GM_OMNIPRICE_PRICE_COLOR', '#FFFFFF');
    Configuration::updateValue('GM_OMNIPRICE_BG_COLOR', '#666666');
    return true;
}
