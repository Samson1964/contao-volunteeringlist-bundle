<?php

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\DC_Table;
use Contao\StringUtil;
use Schachbulle\ContaoHelperBundle\Classes\Helper;

/**
 * Tabelle tl_volunteeringlist_items — die einzelnen Amtsträger einer Liste
 */
$GLOBALS['TL_DCA']['tl_volunteeringlist_items'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ptable'                      => 'tl_volunteeringlist',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'headerFields'            => array('title', 'templatefile'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_volunteeringlist_items', 'listPersons'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{person_legend},name,birthday,birthplace,deathday,deathplace,singleSRC;{function_legend},fromDate,toDate,fromDate_unknown,toDate_unknown,info;{register_legend},spielerregister_id;{publish_legend},viewLifedates,published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_volunteeringlist.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 255,
				'tl_class'            => 'w50',
				'mandatory'           => true
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'birthday' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['birthday'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array(Helper::class, 'getDate')
			),
			'save_callback' => array
			(
				array(Helper::class, 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'birthplace' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['birthplace'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 255,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'deathday' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['deathday'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array(Helper::class, 'getDate')
			),
			'save_callback' => array
			(
				array(Helper::class, 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'deathplace' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['deathplace'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 255,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'fromDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['fromDate'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array(Helper::class, 'getDate')
			),
			'save_callback' => array
			(
				array(Helper::class, 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'fromDate_unknown' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['fromDate_unknown'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'toDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['toDate'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array(Helper::class, 'getDate')
			),
			'save_callback' => array
			(
				array(Helper::class, 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'toDate_unknown' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['toDate_unknown'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array
			(
				'filesOnly'           => true,
				'fieldType'           => 'radio',
				// Platzhalter für den Container-Parameter, den Contao beim
				// Aufbau des Widgets auflöst. Config::get('validImageTypes')
				// gilt seit Contao 4.12 als veraltet.
				'extensions'          => '%contao.image.valid_extensions%',
				'tl_class'            => 'clr'
			),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper', 'getRegister'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['info'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'rte'                 => 'tinyMCE',
				'tl_class'            => 'clr long',
				'helpwizard'          => true
			),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'viewLifedates' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['viewLifedates'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'default'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true,
				'doNotCopy'           => true
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist_items']['published'],
			'toggle'                  => true, // Aktiviert den Contao-eigenen Schnellschalter in der Übersicht
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'default'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true,
				'doNotCopy'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);

/**
 * Stellt die Rückruffunktionen der Tabelle tl_volunteeringlist_items bereit.
 */
class tl_volunteeringlist_items extends Backend
{
	/**
	 * Erzeugt die Zeilenbeschriftung eines Eintrags in der Übersicht.
	 *
	 * Ausgegeben wird die Amtszeit gefolgt vom Namen; unbekannte Datumsangaben
	 * erscheinen als Fragezeichen, damit Lücken sofort auffallen. Ist der
	 * Eintrag mit dem Spielerregister verknüpft, weist ein kleines Symbol
	 * darauf hin, dass die Lebensdaten von dort kommen.
	 *
	 * Name und Symboltitel werden maskiert, weil der Name als Freitext erfasst
	 * wird und sonst Auszeichnungen in die Übersicht einschleusen könnte.
	 *
	 * @param array $arrRow Der Datensatz der Zeile
	 *
	 * @return string Die Beschriftung als HTML
	 */
	public function listPersons($arrRow): string
	{
		$strVon = $arrRow['fromDate'] ? Helper::getDate($arrRow['fromDate']) : '?';
		$strBis = $arrRow['toDate'] ? Helper::getDate($arrRow['toDate']) : '?';

		$strLabel = '<div class="tl_content_left">' . $strVon . ' - ' . $strBis;

		if ($arrRow['name'])
		{
			$strLabel .= ' <b>' . StringUtil::specialchars($arrRow['name']) . '</b>';
		}

		if ($arrRow['spielerregister_id'])
		{
			$strLabel .= ' <img src="bundles/contaovolunteeringlist/images/spielerregister.png" alt="" title="' . StringUtil::specialcharsAttribute($GLOBALS['TL_LANG']['tl_volunteeringlist_items']['spielerregister'] ?? '') . '">';
		}

		return $strLabel . '</div>';
	}
}
