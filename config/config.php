<?php

// TODO: store in tl_settings
define('TYPO3_DB', 'avv_typo3');
define('TYPO3_ROOT', '/home/rico/Kunden/avv/typo44');

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['devtools']['typort'] = array
(
    'tables'     => array('tl_typort'),
    'import'     => array('HeimrichHannot\Typort\ModuleTyport', 'generate'),
    'icon'       => 'system/modules/typort/assets/typort.png'
);
