<?php

/**
 * Table tl_extension
 */
$GLOBALS['TL_DCA']['tl_typort'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title', 'type'),
			'format'                  => '%s <span style="color:#b3b3b3; padding-left:3px;">[%s]</span>'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_typort']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_typort']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_typort']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_typort']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_typort']['import'],
				'href'                => 'key=import',
				'icon'                => 'system/modules/devtools/assets/apply.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type'),
		'default'                     => '{title_legend},title,type',
        'tt_news'                     => '{title_legend},title,type;{config_legend},pid,start,end,folder',
	),

	// Subpalettes
	'subpalettes' => array
	(
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['title'],
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['type'],
			'search'                  => true,
			'inputType'               => 'select',
            'default'                 => 'tt_news',
			'eval'                    => array('mandatory'=>true, 'submitOnChange'=>true),
			'options' => array
			(
				'tt_news'
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
        'pid'   => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['pid'],
			'inputType'               => 'select',
			'eval'                    => array('mandatory'=>true, 'submitOnChange'=>true),
			'options_callback'        => array('tl_typort', 'getPidsFromTable'),
			'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'start' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['start'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'datim', 'tl_class'=>'w50', 'datepicker' => true),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'end' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['end'],
            'inputType'               => 'text',
            'eval'                    => array('rgxp'=>'datim', 'tl_class'=>'w50', 'datepicker' => true),
            'sql'                     => "int(10) unsigned NULL"
        ),
        'folder' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_typort']['folder'],
            'inputType'               => 'fileTree',
            'eval'                    => array('files'=>false, 'fieldType'=>'radio'),
            'sql'                     => "binary(16) NULL"
        )
	)
);


class tl_typort extends Backend
{

    public function getPidsFromTable(DataContainer $dc)
    {
        $objArchives = HeimrichHannot\Typort\Database::getInstance()->prepare('SELECT DISTINCT pid FROM ' . $dc->activeRecord->type)->execute();

        return $objArchives === null ? array() : $objArchives->fetchEach('pid');
    }
}
