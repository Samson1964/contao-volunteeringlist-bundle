<?php

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Contao\BackendUser;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;

/**
 * Palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{volunteeringlist_legend:hide},volunteeringlist_defaultImage,volunteeringlist_imageSize,volunteeringlist_css';

/**
 * Felder
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_defaultImage'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_defaultImage'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50'
	),
	// Der Dateibaum liefert die UUID als 16 Byte Binärwert. Die Einstellungen
	// landen aber in system/config/localconfig.php, also in einer PHP-Datei,
	// die den Binärwert nicht unbeschadet übersteht: Nullbytes und Backslashes
	// gehen dabei verloren, und FilesModel::findByUuid() findet die Datei
	// später nicht mehr. Deshalb wird hier in die lesbare Schreibweise
	// umgewandelt, die findByUuid() ebenso versteht.
	'save_callback' => array
	(
		static function ($varValue)
		{
			return Validator::isBinaryUuid($varValue) ? StringUtil::binToUuid($varValue) : $varValue;
		}
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_imageSize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_imageSize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	// Liefert die im System hinterlegten Bildgrößen als Auswahlliste, beschränkt
	// auf die Größen, die der angemeldete Benutzer sehen darf. Der Dienst heißt
	// seit Contao 5 "contao.image.sizes"; unter Contao 4.13 ist
	// "contao.image.image_sizes" nur noch ein Alias darauf, der alte Name führt
	// in Contao 5 dagegen zu einem Fehler.
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
	}
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_css'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_css'],
	'inputType'     => 'checkbox',
	'eval'          => array
	(
		'tl_class'  => 'w50 clr',
		'isBoolean' => true
	)
);
