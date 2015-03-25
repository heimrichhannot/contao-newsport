<?php

/**
 * Table tl_extension
 */
$GLOBALS['TL_DCA']['tl_newsport_config'] = array
(

	// Config
	'config'      => array
	(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'ptable'           => 'tl_newsport',
		'sql'              => array
		(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index',
			)
		),
		'onload_callback'  => array('tl_newsport_config', 'initPalette')
	),

	// List
	'list'        => array
	(
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('title DESC'),
			'headerFields'          => array('title', 'dbHost', 'dbUser', 'dbDatabase'),
			'panelLayout'           => 'filter;sort,search,limit',
			'child_record_callback' => array('tl_newsport_config', 'listNewsPortConfig'),
			'child_record_class'    => 'no_padding',
			'disableGrouping'       => true,
		),
		'label'             => array
		(
			'fields'         => array('title', 'type'),
			'format'         => '%s <span style="color:#b3b3b3; padding-left:3px;">[%s]</span>',
			'label_callback' => array('tl_newsport_config', 'addDate'),
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
				'label' => &$GLOBALS['TL_LANG']['tl_newsport_config']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_newsport_config']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_newsport_config']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_newsport_config']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			),
			'import' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_newsport_config']['import'],
				'href'  => 'key=import',
				'icon'  => 'system/modules/devtools/assets/apply.gif'
			)
		)
	),

	// Palettes
	'palettes'    => array
	(
		'__selector__' => array('type'),
		'default'      => '{title_legend},title,newsArchive;{config_legend},dbTable,dbFieldMapping,start,end,whereClause,sourceDir,targetDir',
		//		'tt_news'      => '{title_legend},title,table;{config_legend},pids,start,end,folder;{news_legend},newsArchive;{category_legend},catTypo,catContao',
	),

	// Subpalettes
	'subpalettes' => array
	(),

	// Fields
	'fields'      => array
	(
		'id'             => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'            => array
		(
			'foreignKey' => 'tl_newsport.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp'         => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'title'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['title'],
			'search'    => true,
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'dbTable'        => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_newsport_config']['dbTable'],
			'search'           => true,
			'exclude'          => true,
			'inputType'        => 'select',
			'eval'             => array('mandatory' => true, 'submitOnChange' => true),
			'options_callback' => array('tl_newsport_config', 'getTables'),
			'sql'              => "varchar(256) NOT NULL default ''"
		),
		'dbFieldMapping' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['slick_responsive'],
			'inputType' => 'multiColumnWizard',
			'exclude'   => true,
			'eval'      => array
			(
				'tl_class'     => 'clr',
				'columnFields' => array
				(
					'type'   => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['type'],
						'inputType' => 'select',
						'options'   => array('source', 'value'),
						'eval'      => array
						(
							'style' => 'width:150px',
						)
					),
					'source' => array
					(
						'label'            => &$GLOBALS['TL_LANG']['tl_newsport_config']['source'],
						'inputType'        => 'select',
						'options_callback' => array('tl_newsport_config', 'getSourceFields'),
						'eval'             => array
						(
							'style'              => 'width:150px',
							'includeBlankOption' => true
						)
					),
					'value'  => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['value'],
						'inputType' => 'text',
						'eval'      => array
						(
							'style' => 'width:150px'
						)
					),
					'target' => array
					(
						'label'            => &$GLOBALS['TL_LANG']['tl_newsport_config']['target'],
						'inputType'        => 'select',
						'options_callback' => array('tl_newsport_config', 'getTargetFields'),
						'eval'             => array
						(
							'style' => 'width:150px'
						)
					),
				)
			),
			'sql'       => "blob NULL"
		),
		//		'pids'           => array
		//		(
		//			'label'            => &$GLOBALS['TL_LANG']['tl_newsport_config']['pids'],
		//			'inputType'        => 'checkbox',
		//			'exclude'          => true,
		//			'eval'             => array('mandatory' => true, 'submitOnChange' => true, 'multiple' => true),
		//			'options_callback' => array('tl_newsport_config', 'getPidsFromTable'),
		//			'sql'              => "blob NULL",
		//		),
		'start'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['start'],
			'inputType' => 'text',
			'exclude'   => true,
			'eval'      => array('rgxp' => 'datim', 'tl_class' => 'w50', 'datepicker' => true),
			'sql'       => "int(10) unsigned NULL"
		),
		'end'            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['end'],
			'inputType' => 'text',
			'exclude'   => true,
			'eval'      => array('rgxp' => 'datim', 'tl_class' => 'w50', 'datepicker' => true),
			'sql'       => "int(10) unsigned NULL"
		),
		'whereClause'    => array
		(
			'label'       => &$GLOBALS['TL_LANG']['tl_newsport_config']['whereClause'],
			'inputType'   => 'textarea',
			'exclude'     => true,
			'eval'        => array('allowHtml' => true, 'class' => 'monospace', 'rte' => 'ace|html', 'helpwizard' => true),
			'explanation' => 'insertTags',
			'sql'         => "text NULL"
		),
		'sourceDir'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['sourceDir'],
			'inputType' => 'fileTree',
			'exclude'   => true,
			'eval'      => array('files' => false, 'fieldType' => 'radio', 'tl_class' => 'w50'),
			'sql'       => "binary(16) NULL"
		),
		'targetDir'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_newsport_config']['targetDir'],
			'inputType' => 'fileTree',
			'exclude'   => true,
			'eval'      => array('files' => false, 'fieldType' => 'radio', 'tl_class' => 'w50'),
			'sql'       => "binary(16) NULL"
		),
		'newsArchive'    => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_newsport_config']['newsArchive'],
			'inputType'  => 'select',
			'exclude'    => true,
			'eval'       => array('mandatory' => true, 'submitOnChange' => true),
			'foreignKey' => 'tl_news_archive.title',
			'sql'        => "int(10) unsigned NULL"
		),
		//		'catTypo'     => array
		//		(
		//			'label'            => &$GLOBALS['TL_LANG']['tl_member']['catTypo'],
		//			'exclude'          => true,
		//			'inputType'        => 'checkboxWizard',
		//			'eval'             => array('multiple' => true, 'tl_class' => 'w50'),
		//			'options_callback' => array('tl_newsport_config', 'getTypoCategories'),
		//			'sql'              => "blob NULL",
		//		),
		//		'catContao'   => array
		//		(
		//			'label'            => &$GLOBALS['TL_LANG']['tl_member']['catContao'],
		//			'exclude'          => true,
		//			'inputType'        => 'checkboxWizard',
		//			'eval'             => array('multiple' => true, 'tl_class' => 'w50'),
		//			'options_callback' => array('tl_newsport_config', 'getContaoCategories'),
		//			'sql'              => "blob NULL",
		//		),
	)
);


class tl_newsport_config extends \Backend
{
	public function getSourceFields($dc)
	{
		$arrOptions = array();

		if ($dc->activeRecord->dbTable == null) return $arrOptions;

		$arrFields = HeimrichHannot\Newsport\Database::getInstance(
			HeimrichHannot\Newsport\NewsportModel::findByPk($dc->activeRecord->pid)->row()
		)->listFields($dc->activeRecord->dbTable);

		if (!is_array($arrFields) || empty($arrFields)) return $arrOptions;

		foreach ($arrFields as $arrField) {
			if (in_array($arrField['type'], array('index'))) continue;

			$arrOptions[$arrField['name']] = $arrField['name'] . ' [' . $arrField['origtype'] . ']';
		}

		return $arrOptions;
	}


	public function getTargetFields($dc)
	{
		$arrOptions = array();

		$arrFields = \Database::getInstance()->listFields('tl_news');

		if (!is_array($arrFields) || empty($arrFields)) return $arrOptions;

		foreach ($arrFields as $arrField) {
			if (in_array($arrField['type'], array('index'))) continue;
			if ($arrField['name'] == 'pid') continue;

			$arrOptions[$arrField['name']] = $arrField['name'] . ' [' . $arrField['origtype'] . ']';
		}

		return $arrOptions;
	}

	public function getTables(\DataContainer $dc)
	{
		$arrTables = HeimrichHannot\Newsport\Database::getInstance(
			HeimrichHannot\Newsport\NewsportModel::findByPk($dc->activeRecord->pid)->row()
		)->listTables();

		return array_values($arrTables);
	}

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

		while ($objArchives->next()) {
			$arrArchives[$objArchives->uid] = $objArchives->title . ' [Id: ' . $objArchives->uid . '] (Count:' . $objArchives->total . ')';
		}

		return $arrArchives;
	}


	public function listNewsPortConfig($arrRow)
	{

		if ($arrRow['start'] || $arrRow['end']) {

			$span = '';

			if ($arrRow['start']) {
				$span .= \Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['start']);

				if ($arrRow['end']) {
					$span .= '&nbsp;-&nbsp;';
				}
			}

			if ($arrRow['end']) {
				$span .= \Date::parse($GLOBALS['TL_CONFIG']['datimFormat'], $arrRow['end']);
			}
		}

		return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $span . ']</span></div>';
	}


}
