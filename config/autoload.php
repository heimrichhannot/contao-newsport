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
	// Modules
	'HeimrichHannot\Typort\ModuleTyport' => 'system/modules/typort/modules/ModuleTyport.php',

	// Classes
	'HeimrichHannot\Typort\Database'     => 'system/modules/typort/classes/Database.php',
	'HeimrichHannot\Typort\Importer'     => 'system/modules/typort/classes/Importer.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dev_typort' => 'system/modules/typort/templates',
));
