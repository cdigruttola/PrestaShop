<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_11($object) {
    return ($object->registerHook('displayAdminProductsExtra'));
}
