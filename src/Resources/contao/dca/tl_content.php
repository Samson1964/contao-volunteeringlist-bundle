<?php

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

/**
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'volunteeringlist_alttemplate';
$GLOBALS['TL_DCA']['tl_content']['palettes']['volunteeringlist'] = '{type_legend},type,headline;{volunteer_legend},volunteeringlist,volunteeringlist_alttemplate;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['volunteeringlist_alttemplate'] = 'volunteeringlist_template';

/**
 * Felder
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist'],
	'default'              => '0',
	'exclude'              => true,
	'options_callback'     => array('tl_content_volunteeringlist', 'getVolunteeringlist'),
	'inputType'            => 'select',
	'eval'                 => array
	(
		'mandatory'      => true,
		'multiple'       => false,
		'chosen'         => true,
		'submitOnChange' => true,
		'tl_class'       => 'wizard'
	),
	'wizard'               => array
	(
		array('tl_content_volunteeringlist', 'editListe')
	),
	'sql'                  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist_alttemplate'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist_alttemplate'],
	'exclude'       => true,
	'inputType'     => 'checkbox',
	'eval'          => array('tl_class'=>'clr', 'isBoolean'=>true, 'submitOnChange'=>true),
	'sql'           => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['volunteeringlist_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['volunteeringlist_template'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_volunteeringlist', 'getTemplates'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

/**
 * Stellt die Rückruffunktionen für das Inhaltselement bereit.
 */
class tl_content_volunteeringlist extends Backend
{
	/**
	 * Erzeugt den Knopf, der die gewählte Liste im Popup zum Bearbeiten öffnet.
	 *
	 * Der Knopf erscheint erst, wenn eine Liste ausgewählt ist — ohne Liste
	 * gäbe es nichts zu bearbeiten. Die Adresse wird über den Router erzeugt
	 * statt wie früher fest auf 'contao/main.php' zu zeigen; diese Datei gibt
	 * es seit Contao 4 nicht mehr. Das Anfrage-Token holt sich Contao im
	 * Backend selbst, die frühere Konstante REQUEST_TOKEN ist in Contao 5
	 * entfallen.
	 *
	 * @param DataContainer $dc Enthält in $dc->value die ID der gewählten Liste
	 *
	 * @return string Der fertige HTML-Verweis oder eine leere Zeichenkette,
	 *                wenn noch keine Liste ausgewählt wurde
	 */
	public function editListe(DataContainer $dc): string
	{
		$intListe = (int) $dc->value;

		if ($intListe < 1)
		{
			return '';
		}

		$strUrl = System::getContainer()->get('router')->generate('contao_backend', array
		(
			'do'    => 'volunteeringlist',
			'table' => 'tl_volunteeringlist_items',
			'id'    => $intListe,
			'popup' => '1',
		));

		$strTitel = sprintf($GLOBALS['TL_LANG']['tl_content']['volunteeringlist_edit'][1] ?? '', $intListe);

		return ' <a href="' . StringUtil::specialcharsAttribute($strUrl) . '" title="' . StringUtil::specialcharsAttribute($strTitel) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialcharsAttribute(str_replace("'", "\\'", $strTitel)) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $GLOBALS['TL_LANG']['tl_content']['volunteeringlist_edit'][0] ?? '', 'style="vertical-align:top"') . '</a>';
	}

	/**
	 * Liefert alle angelegten Funktionärslisten als Auswahlliste.
	 *
	 * Sortiert wird nach dem Titel, damit die Auswahl auch bei vielen Listen
	 * überschaubar bleibt. Der Schlüssel ist die ID, die im Inhaltselement
	 * gespeichert wird.
	 *
	 * Der Parameter ist bewusst ohne Typ und mit Vorgabewert deklariert: Contao
	 * reicht den DataContainer je nach Aufrufweg durch, ruft die Auswahlliste
	 * aber auch ohne ihn auf.
	 *
	 * @param DataContainer|null $dc Wird von Contao übergeben, hier nicht benötigt
	 *
	 * @return array<int, string> ID => Titel, leeres Array wenn keine Liste existiert
	 */
	public function getVolunteeringlist($dc = null): array
	{
		$arrListen = array();

		$objListen = Database::getInstance()->execute('SELECT id, title FROM tl_volunteeringlist ORDER BY title');

		while ($objListen->next())
		{
			$arrListen[$objListen->id] = $objListen->title;
		}

		return $arrListen;
	}

	/**
	 * Liefert die verfügbaren Templates für die abweichende Templatewahl.
	 *
	 * Gesucht wird nach dem Präfix 'ce_volunteeringlist_'. Bis Version 2.2.2
	 * wurde hier fälschlich nach 'mod_volunteeringlist_' gesucht — ein Präfix,
	 * das es in diesem Bundle nie gab, weshalb die Auswahl immer leer blieb.
	 *
	 * @param DataContainer|null $dc Wird von Contao übergeben, hier nicht benötigt
	 *
	 * @return array<string, string> Templatename => Templatename
	 */
	public function getTemplates($dc = null): array
	{
		return $this->getTemplateGroup('ce_volunteeringlist_');
	}
}
