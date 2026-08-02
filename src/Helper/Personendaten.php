<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle\Helper;

/**
 * Setzt die Textbausteine einer Funktionärsliste zusammen.
 *
 * Die Klasse arbeitet bewusst nur auf bereits aufbereiteten Zeichenketten und
 * kennt weder Contao noch die Datenbank. Die Umwandlung der in der Datenbank
 * abgelegten Zahlenwerte (JJJJMMTT) in lesbare Datumsangaben übernimmt der
 * Aufrufer über den Helper des contao-helper-bundle. Dadurch bleiben beide
 * Methoden ohne Framework testbar.
 */
class Personendaten
{
	/**
	 * Stellt die Lebensdaten einer Person zu einer Zeile zusammen.
	 *
	 * Ausgegeben wird die im Schachschrifttum übliche Kurzform, also etwa
	 * '* 01.02.1900 Berlin, † 03.04.1980 Hamburg'. Jeder Bestandteil ist
	 * freiwillig: Fehlt das Geburtsdatum, entfällt der Stern mitsamt Datum,
	 * fehlen alle vier Angaben, kommt eine leere Zeichenkette zurück. Das Komma
	 * vor dem Kreuz erscheint nur, wenn davor bereits etwas ausgegeben wurde.
	 *
	 * Das Kreuz wird als HTML-Entität (&dagger;) ausgegeben, weil die
	 * Zeichenkette in den Templates ohne weitere Umwandlung ausgegeben wird.
	 *
	 * @param string $geburtstag Geburtsdatum in lesbarer Form, z. B. '01.02.1900'
	 * @param string $geburtsort Geburtsort, beliebiger Text
	 * @param string $sterbetag  Sterbedatum in lesbarer Form, leer bei lebenden Personen
	 * @param string $sterbeort  Sterbeort, beliebiger Text
	 *
	 * @return string Die zusammengesetzte Zeile, leer wenn keine Angabe vorliegt
	 */
	public static function lebensdaten(string $geburtstag, string $geburtsort, string $sterbetag, string $sterbeort): string
	{
		$strGeburt = trim($geburtstag . ' ' . $geburtsort);
		$strTod = trim($sterbetag . ' ' . $sterbeort);

		$arrTeile = array();

		if ('' !== $strGeburt)
		{
			$arrTeile[] = '* ' . $strGeburt;
		}

		if ('' !== $strTod)
		{
			$arrTeile[] = '&dagger; ' . $strTod;
		}

		return implode(', ', $arrTeile);
	}

	/**
	 * Stellt die Amtszeit einer Person als Zeitraum dar.
	 *
	 * Je nachdem, welche der beiden Datumsangaben vorliegt, entsteht eine andere
	 * Form: Sind beide bekannt, wird '1950 - 1960' ausgegeben, fehlt das Ende
	 * 'seit 1950', fehlt der Anfang 'bis 1960'. Ist ein Datum als ungeklärt
	 * gekennzeichnet, wird ihm 'ca. ' vorangestellt — die Kennzeichnung wirkt
	 * dabei nur auf das jeweils vorhandene Datum, ein 'ca. ' ohne zugehöriges
	 * Datum entsteht nie.
	 *
	 * @param string $von        Beginn der Amtszeit in lesbarer Form, leer wenn unbekannt
	 * @param string $bis        Ende der Amtszeit in lesbarer Form, leer bei laufender Amtszeit
	 * @param bool   $vonUnklar  Kennzeichnet den Beginn als ungefähre Angabe
	 * @param bool   $bisUnklar  Kennzeichnet das Ende als ungefähre Angabe
	 *
	 * @return string Der Zeitraum als Text, leer wenn beide Datumsangaben fehlen
	 */
	public static function amtszeit(string $von, string $bis, bool $vonUnklar = false, bool $bisUnklar = false): string
	{
		$strVon = '' !== $von ? ($vonUnklar ? 'ca. ' : '') . $von : '';
		$strBis = '' !== $bis ? ($bisUnklar ? 'ca. ' : '') . $bis : '';

		if ('' !== $strVon && '' !== $strBis)
		{
			return $strVon . ' - ' . $strBis;
		}

		if ('' !== $strVon)
		{
			return 'seit ' . $strVon;
		}

		if ('' !== $strBis)
		{
			return 'bis ' . $strBis;
		}

		return '';
	}
}
