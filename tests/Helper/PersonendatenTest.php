<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoVolunteeringlistBundle\Helper\Personendaten;

/**
 * Prüft die Textbausteine der Funktionärsliste.
 */
class PersonendatenTest extends TestCase
{
	/**
	 * Stellt sicher, dass die Lebensdaten in allen Kombinationen richtig
	 * zusammengesetzt werden.
	 *
	 * @dataProvider lebensdatenProvider
	 *
	 * @param string $geburtstag Geburtsdatum in lesbarer Form
	 * @param string $geburtsort Geburtsort
	 * @param string $sterbetag  Sterbedatum in lesbarer Form
	 * @param string $sterbeort  Sterbeort
	 * @param string $erwartet   Die erwartete Ausgabe
	 */
	public function testLebensdaten(string $geburtstag, string $geburtsort, string $sterbetag, string $sterbeort, string $erwartet): void
	{
		$this->assertSame($erwartet, Personendaten::lebensdaten($geburtstag, $geburtsort, $sterbetag, $sterbeort));
	}

	/**
	 * Liefert die Fälle für testLebensdaten().
	 *
	 * Abgedeckt sind der Normalfall mit allen vier Angaben, verstorbene und
	 * lebende Personen, Angaben ohne Ort sowie der leere Datensatz.
	 *
	 * @return array<string, array<int, string>> Fallname => Parameter
	 */
	public function lebensdatenProvider(): array
	{
		return array
		(
			'alle Angaben'        => array('01.02.1900', 'Berlin', '03.04.1980', 'Hamburg', '* 01.02.1900 Berlin, &dagger; 03.04.1980 Hamburg'),
			'ohne Orte'           => array('1900', '', '1980', '', '* 1900, &dagger; 1980'),
			'noch am Leben'       => array('01.02.1950', 'Berlin', '', '', '* 01.02.1950 Berlin'),
			'nur Sterbedatum'     => array('', '', '03.04.1980', '', '&dagger; 03.04.1980'),
			'nur Geburtsort'      => array('', 'Berlin', '', '', '* Berlin'),
			'gar keine Angabe'    => array('', '', '', '', ''),
		);
	}

	/**
	 * Stellt sicher, dass die Amtszeit in allen Kombinationen richtig
	 * dargestellt wird.
	 *
	 * @dataProvider amtszeitProvider
	 *
	 * @param string $von       Beginn der Amtszeit
	 * @param string $bis       Ende der Amtszeit
	 * @param bool   $vonUnklar Beginn ist ungefähr
	 * @param bool   $bisUnklar Ende ist ungefähr
	 * @param string $erwartet  Die erwartete Ausgabe
	 */
	public function testAmtszeit(string $von, string $bis, bool $vonUnklar, bool $bisUnklar, string $erwartet): void
	{
		$this->assertSame($erwartet, Personendaten::amtszeit($von, $bis, $vonUnklar, $bisUnklar));
	}

	/**
	 * Liefert die Fälle für testAmtszeit().
	 *
	 * Wichtig ist vor allem der letzte Fall: Ein als ungeklärt markiertes Datum,
	 * das gar nicht erfasst wurde, darf kein alleinstehendes 'ca. ' erzeugen.
	 *
	 * @return array<string, array<int, bool|string>> Fallname => Parameter
	 */
	public function amtszeitProvider(): array
	{
		return array
		(
			'Zeitraum'             => array('1950', '1960', false, false, '1950 - 1960'),
			'laufende Amtszeit'    => array('1950', '', false, false, 'seit 1950'),
			'nur Ende bekannt'     => array('', '1960', false, false, 'bis 1960'),
			'Beginn ungeklärt'     => array('1950', '1960', true, false, 'ca. 1950 - 1960'),
			'Ende ungeklärt'       => array('1950', '1960', false, true, '1950 - ca. 1960'),
			'beide ungeklärt'      => array('1950', '1960', true, true, 'ca. 1950 - ca. 1960'),
			'gar keine Angabe'     => array('', '', true, true, ''),
		);
	}
}
