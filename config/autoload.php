<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package Newsport
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
	'HeimrichHannot\Newsport\TypoRefIndexModel'   => 'system/modules/newsport/models/TypoRefIndexModel.php',
	'HeimrichHannot\Newsport\NewsportConfigModel' => 'system/modules/newsport/models/NewsportConfigModel.php',
	'HeimrichHannot\Newsport\NewsportModel'       => 'system/modules/newsport/models/NewsportModel.php',
	'HeimrichHannot\Newsport\TypoNewsModel'       => 'system/modules/newsport/models/TypoNewsModel.php',
	'HeimrichHannot\Newsport\TyportModel'         => 'system/modules/newsport/models/TyportModel.php',

	// Modules
	'HeimrichHannot\Newsport\ModuleNewsport'      => 'system/modules/newsport/modules/ModuleNewsport.php',

	// Classes
	'HeimrichHannot\Newsport\Database'            => 'system/modules/newsport/classes/Database.php',
	'HeimrichHannot\Newsport\Importer'            => 'system/modules/newsport/classes/Importer.php',
	'HeimrichHannot\Typort\TypoModel'             => 'system/modules/newsport/classes/TypoModel.php',
	'HeimrichHannot\Newsport\NewsImporter'        => 'system/modules/newsport/classes/NewsImporter.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'dev_newsport' => 'system/modules/newsport/templates',
));
