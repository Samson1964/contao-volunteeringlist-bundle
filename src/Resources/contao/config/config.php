<?php

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Schachbulle\ContaoVolunteeringlistBundle\ContentElements\Volunteeringlist;

/**
 * Backend-Modul
 */
$GLOBALS['BE_MOD']['content']['volunteeringlist'] = array
(
	'tables' => array('tl_volunteeringlist', 'tl_volunteeringlist_items'),
	// Contao 4.13 zeigt das Symbol in der Modulnavigation an, Contao 5 nicht mehr
	'icon'   => 'bundles/contaovolunteeringlist/images/icon.png',
);

/**
 * Inhaltselement
 *
 * Die Registrierung über $GLOBALS['TL_CTE'] wird von Contao 4.13 wie von
 * Contao 5 unterstützt, ein Fragment-Controller ist dafür nicht nötig.
 */
$GLOBALS['TL_CTE']['schach']['volunteeringlist'] = Volunteeringlist::class;
