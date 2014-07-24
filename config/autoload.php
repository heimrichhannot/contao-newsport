<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Typort
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'HeimrichHannot',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'HeimrichHannot\Typort\TypoRefIndexModel' => 'system/modules/typort/models/TypoRefIndexModel.php',
	'HeimrichHannot\Typort\TypoNewsModel'     => 'system/modules/typort/models/TypoNewsModel.php',
	'HeimrichHannot\Typort\TyportModel'       => 'system/modules/typort/models/TyportModel.php',

	// Modules
	'HeimrichHannot\Typort\ModuleTyport'      => 'system/modules/typort/modules/ModuleTyport.php',

	// Classes
	'HeimrichHannot\Typort\Database'          => 'system/modules/typort/classes/Database.php',
	'HeimrichHannot\Typort\Importer'          => 'system/modules/typort/classes/Importer.php',
	'HeimrichHannot\Typort\TypoModel'         => 'system/modules/typort/classes/TypoModel.php',
	'HeimrichHannot\Typort\NewsImporter'      => 'system/modules/typort/classes/NewsImporter.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dev_typort' => 'system/modules/typort/templates',
));
