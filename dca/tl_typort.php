<?php

/**
 * Table tl_extension
 */
$GLOBALS['TL_DCA']['tl_typort'] = array
(

	// Config
	'config'      => array
	(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'sql'              => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list'        => array
	(
		'sorting'           => array
		(
			'mode'        => 2,
			'fields'      => array('title'),
			'flag'        => 1,
			'panelLayout' => 'search,limit'
		),
		'label'             => array
		(
			'fields'         => array('title', 'type'),
			'format'         => '%s <span style="color:#b3b3b3; padding-left:3px;">[%s]</span>',
			'label_callback' => array('tl_typort', 'addDate'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_typort']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_typort']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_typort']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_typort']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			),
			'import' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_typort']['import'],
				'href'  => 'key=import',
				'icon'  => 'system/modules/devtools/assets/apply.gif'
			)
		)
	),

	// Palettes
	'palettes'    => array
	(
		'__selector__' => array('type'),
		'default'      => '{title_legend},title,type',
		'tt_news'      => '{title_legend},title,type;{config_legend},pids,start,end,folder;{news_legend},newsArchive;{category_legend},catTypo,catContao',
	),

	// Subpalettes
	'subpalettes' => array
	(),

	// Fields
	'fields'      => array
	(
		'id'          => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp'      => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'title'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_typort']['title'],
			'search'    => true,
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'type'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_typort']['type'],
			'search'    => true,
			'exclude'   => true,
			'inputType' => 'select',
			'default'   => 'tt_news',
			'eval'      => array('mandatory' => true, 'submitOnChange' => true),
			'options'   => array
			(
				'tt_news'
			),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'pids'        => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_typort']['pids'],
			'inputType'        => 'checkbox',
			'exclude'          => true,
			'eval'             => array('mandatory' => true, 'submitOnChange' => true, 'multiple' => true),
			'options_callback' => array('tl_typort', 'getPidsFromTable'),
			'sql'              => "blob NULL",
		),
		'start'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_typort']['start'],
			'inputType' => 'text',
			'exclude'   => true,
			'eval'      => array('rgxp' => 'datim', 'tl_class' => 'w50', 'datepicker' => true),
			'sql'       => "int(10) unsigned NULL"
		),
		'end'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_typort']['end'],
			'inputType' => 'text',
			'exclude'   => true,
			'eval'      => array('rgxp' => 'datim', 'tl_class' => 'w50', 'datepicker' => true),
			'sql'       => "int(10) unsigned NULL"
		),
		'folder'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_typort']['folder'],
			'inputType' => 'fileTree',
			'exclude'   => true,
			'eval'      => array('files' => false, 'fieldType' => 'radio'),
			'sql'       => "binary(16) NULL"
		),
		'newsArchive' => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_typort']['newsArchive'],
			'inputType'  => 'select',
			'exclude'    => true,
			'eval'       => array('mandatory' => true, 'submitOnChange' => true),
			'foreignKey' => 'tl_news_archive.title',
			'sql'        => "int(10) unsigned NULL"
		),
		'catTypo'     => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_member']['catTypo'],
			'exclude'          => true,
			'inputType'        => 'checkboxWizard',
			'eval'             => array('multiple' => true, 'tl_class' => 'w50'),
			'options_callback' => array('tl_typort', 'getTypoCategories'),
			'sql'              => "blob NULL",
		),
		'catContao'   => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_member']['catContao'],
			'exclude'          => true,
			'inputType'        => 'checkboxWizard',
			'eval'             => array('multiple' => true, 'tl_class' => 'w50'),
			'options_callback' => array('tl_typort', 'getContaoCategories'),
			'sql'              => "blob NULL",
		)
	)
);


class tl_typort extends Backend
{
	public function getContaoCategories(DataContainer $dc)
	{
		$arrOptions = array();

		if (!in_array('news_categories', \Config::getInstance()->getActiveModules())) return $arrOptions;

		$objCategories = NewsCategories\NewsCategoryModel::findBy('published', 1);

		if ($objCategories === null) return $arrOptions;

		while ($objCategories->next()) {
			$arrOptions[$objCategories->id] = $objCategories->title;
		}

		return $arrOptions;
	}

	public function getTypoCategories(DataContainer $dc)
	{
		$arrOptions = array();

		if (!in_array('news_categories', \Config::getInstance()->getActiveModules())) return $arrOptions;

		$objCategories = HeimrichHannot\Typort\Database::getInstance()->prepare('SELECT * FROM tt_news_cat WHERE deleted = 0 AND hidden=0')->execute();

		if ($objCategories->count() < 1) return $arrOptions;

		while ($objCategories->next()) {
			$arrOptions[$objCategories->uid] = $objCategories->title;
		}

		return $arrOptions;
	}

	public function getPidsFromTable(DataContainer $dc)
	{
		$arrArchives = array();

		$objArchives = HeimrichHannot\Typort\Database::getInstance()->prepare(
			'SELECT p.title, p.uid, COUNT(n.uid) AS total FROM ' . $dc->activeRecord->type . ' n
			INNER JOIN pages p ON p.uid = n.pid
			WHERE n.deleted=0 AND p.deleted = 0 GROUP BY n.pid ORDER BY n.pid')
			->execute();

		if ($objArchives === null) return $arrArchives;

		while($objArchives->next())
		{
			$arrArchives[$objArchives->uid] = $objArchives->title . ' [Id: ' . $objArchives->uid . '] (Count:' . $objArchives->total . ')';
		}

		return $arrArchives;
	}


	public function addDate($row, $label)
	{

		if ($row['start'] || $row['end']) {
			$label .= '&nbsp;<strong>[';

			if ($row['start']) {
				$label .= $GLOBALS['TL_LANG']['tl_typort']['start'][0] . ': ' . \Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $row['start']);

				if ($row['end']) {
					$label .= '&nbsp;-&nbsp;';
				}
			}

			if ($row['end']) {
				$label .= $GLOBALS['TL_LANG']['tl_typort']['end'][0] . ': ' . \Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $row['end']);
			}

			$label .= ']</strong>';
		}

		return $label;
	}


}
