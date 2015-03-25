<?php

// TODO: store in tl_settings
define('TYPO3_DB', 'avv_typo3');
define('TYPO3_ROOT', '/home/rico/Kunden/avv/typo44');

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['devtools']['newsport'] = array
(
    'tables'     => array('tl_newsport', 'tl_newsport_config'),
    'import'     => array('HeimrichHannot\Newsport\ModuleNewsport', 'generate'),
    'icon'       => 'system/modules/newsport/assets/newsport.png'
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_newsport'] = 'HeimrichHannot\Newsport\NewsportModel';
$GLOBALS['TL_MODELS']['tl_newsport_config'] ='HeimrichHannot\Newsport\NewsportConfigModel';
