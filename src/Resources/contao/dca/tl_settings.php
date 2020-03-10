<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   fen
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

/**
 * palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{volunteeringlist_legend:hide},volunteeringlist_picWidth,volunteeringlist_picHeight,volunteeringlist_css';

/**
 * fields
 */

$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_picWidth'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_picWidth'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_picHeight'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_picHeight'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['volunteeringlist_css'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['volunteeringlist_css'],
	'inputType'     => 'checkbox',
	'eval'          => array
	(
		'tl_class'  => 'w50',
	)
);
