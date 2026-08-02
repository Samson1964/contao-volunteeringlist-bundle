<?php

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

/**
 * Tabelle tl_volunteeringlist — die Funktionärslisten selbst
 */
$GLOBALS['TL_DCA']['tl_volunteeringlist'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ctable'                      => array('tl_volunteeringlist_items'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'title' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('title'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s'
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
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['edit'],
				'href'                => 'table=tl_volunteeringlist_items',
				'icon'                => 'edit.svg'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg',
				'button_callback'     => array('tl_volunteeringlist', 'editHeader')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '') . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title;{template_legend},templatefile;{publish_legend},published'
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'templatefile' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['templatefile'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_volunteeringlist', 'getTemplates'),
			'eval'                    => array('tl_class'=>'w50', 'includeBlankOption'=>true),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_volunteeringlist']['published'],
			'toggle'                  => true, // Aktiviert den Contao-eigenen Schnellschalter in der Übersicht
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'default'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);

/**
 * Stellt die Rückruffunktionen der Tabelle tl_volunteeringlist bereit.
 */
class tl_volunteeringlist extends Backend
{
	/**
	 * Liefert die Templates, die für eine Liste ausgewählt werden können.
	 *
	 * Gesucht wird nach dem Präfix 'ce_volunteeringlist_', also nach allen
	 * Templates des Inhaltselements. Die frühere Fallunterscheidung über die
	 * Konstanten VERSION und BUILD ist entfallen: Beide sind in Contao 5 nicht
	 * mehr definiert, und die damit abgefragte Zweitparameter-Variante von
	 * getTemplateGroup() gab es ohnehin nur bis Contao 4.7.
	 *
	 * @param DataContainer|null $dc Wird von Contao übergeben, hier nicht benötigt
	 *
	 * @return array<string, string> Templatename => Templatename
	 */
	public function getTemplates($dc = null): array
	{
		return $this->getTemplateGroup('ce_volunteeringlist_');
	}

	/**
	 * Erzeugt den Knopf zum Bearbeiten der Listeneigenschaften.
	 *
	 * Der Knopf wird ausgegraut, wenn dem angemeldeten Benutzer für keines der
	 * Felder dieser Tabelle ein Bearbeitungsrecht eingeräumt wurde. Andernfalls
	 * könnte er die Maske zwar öffnen, aber nichts darin ändern.
	 *
	 * Geprüft wird über den Security-Helper statt wie früher von Hand über
	 * $this->User->alexf. Das ist der in Contao 4.13 wie in Contao 5 vorgesehene
	 * Weg und erspart den über System::import() geladenen Benutzer, der in
	 * Contao 5 als veraltet gilt.
	 *
	 * @param array  $row        Der Datensatz der Zeile
	 * @param string $href       Die Zieladresse der Schaltfläche
	 * @param string $label      Beschriftung für das Symbol
	 * @param string $title      Text für das title-Attribut
	 * @param string $icon       Dateiname des Symbols
	 * @param string $attributes Zusätzliche HTML-Attribute
	 *
	 * @return string Der fertige HTML-Verweis oder das ausgegraute Symbol
	 */
	public function editHeader($row, $href, $label, $title, $icon, $attributes): string
	{
		$objSecurity = System::getContainer()->get('security.helper');

		if (!$objSecurity->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELDS_OF_TABLE, 'tl_volunteeringlist'))
		{
			return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
		}

		return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialcharsAttribute($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ';
	}
}
