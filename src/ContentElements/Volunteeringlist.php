<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle\ContentElements;

use Contao\Config;
use Contao\ContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Schachbulle\ContaoHelperBundle\Classes\Helper;
use Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper as SpielerregisterHelper;
use Schachbulle\ContaoVolunteeringlistBundle\Helper\Personendaten;

/**
 * Inhaltselement, das eine im Backend gepflegte Funktionärsliste ausgibt.
 *
 * Ausgegeben werden alle veröffentlichten Einträge der gewählten Liste in der
 * im Backend festgelegten Reihenfolge. Ist einem Eintrag ein Datensatz aus dem
 * Spielerregister zugeordnet, haben dessen Lebensdaten Vorrang vor den im
 * Eintrag selbst hinterlegten Angaben.
 */
class Volunteeringlist extends ContentElement
{
	/**
	 * Standardtemplate, falls die Liste keines vorgibt
	 * @var string
	 */
	protected $strTemplate = 'ce_volunteeringlist_default';

	/**
	 * Baut die Ausgabe des Inhaltselements zusammen.
	 *
	 * Zuerst wird die gewählte Liste geladen; existiert sie nicht, bleibt das
	 * Element leer. Anschließend wird das Template festgelegt — entweder das in
	 * der Liste hinterlegte oder, wenn im Inhaltselement ausdrücklich gewünscht,
	 * ein abweichendes. Danach werden die Einträge geladen und als Array an das
	 * Template übergeben.
	 *
	 * Seiteneffekte: Ist in den Einstellungen das mitgelieferte Stylesheet
	 * aktiviert, wird es in $GLOBALS['TL_CSS'] eingehängt.
	 *
	 * Das Template wird über setName() umgestellt und nicht durch ein neu
	 * erzeugtes FrontendTemplate ersetzt. Andernfalls gingen die von
	 * ContentElement::generate() bereits gesetzten Daten des Inhaltselements
	 * verloren — unter anderem die Überschrift.
	 */
	protected function compile(): void
	{
		// Immer setzen, damit die Templates ohne Prüfung darüber laufen können,
		// auch wenn weiter unten vorzeitig abgebrochen wird
		$this->Template->items = array();

		$intListe = (int) $this->volunteeringlist;

		if (!$intListe)
		{
			return;
		}

		$objDatenbank = $this->getConnection();

		$arrListe = $objDatenbank->fetchAssociative('SELECT * FROM tl_volunteeringlist WHERE id = ?', array($intListe));

		// Die Liste wurde zwischenzeitlich gelöscht
		if (false === $arrListe)
		{
			return;
		}

		if (Config::get('volunteeringlist_css'))
		{
			$GLOBALS['TL_CSS'][] = 'bundles/contaovolunteeringlist/default.css';
		}

		$strTemplate = $this->volunteeringlist_alttemplate ? (string) $this->volunteeringlist_template : (string) $arrListe['templatefile'];

		if ('' !== $strTemplate)
		{
			$this->Template->setName($strTemplate);
		}

		$this->Template->id = $intListe;
		$this->Template->title = $arrListe['title'];

		$arrEintraege = $objDatenbank->fetchAllAssociative(
			'SELECT * FROM tl_volunteeringlist_items WHERE pid = ? AND published = ? ORDER BY sorting',
			array($intListe, '1')
		);

		$arrItems = array();

		foreach ($arrEintraege as $i => $arrEintrag)
		{
			$arrItems[] = $this->parseEintrag($arrEintrag, $i, $objDatenbank);
		}

		$this->Template->items = $arrItems;
	}

	/**
	 * Bereitet einen einzelnen Listeneintrag für das Template auf.
	 *
	 * Ist dem Eintrag ein Spieler aus dem Spielerregister zugeordnet, werden
	 * dessen Lebensdaten geladen und den im Eintrag gespeicherten vorgezogen.
	 * So bleiben die Angaben gepflegter Personen an einer Stelle aktuell,
	 * während Personen ohne Registereintrag weiterhin von Hand erfasst werden
	 * können.
	 *
	 * @param array<string, mixed> $arrEintrag   Datensatz aus tl_volunteeringlist_items
	 * @param int                  $intIndex     Laufende Nummer ab 0, bestimmt die Zeilenklasse odd/even
	 * @param Connection           $objDatenbank Offene Verbindung, damit sie nicht je Eintrag neu geholt wird
	 *
	 * @return array<string, mixed> Die Template-Variablen des Eintrags
	 */
	protected function parseEintrag(array $arrEintrag, int $intIndex, Connection $objDatenbank): array
	{
		$intSpieler = (int) ($arrEintrag['spielerregister_id'] ?? 0);
		$arrRegister = null;

		if ($intSpieler)
		{
			$arrRegister = $objDatenbank->fetchAssociative('SELECT * FROM tl_spielerregister WHERE id = ?', array($intSpieler));

			// Der Registereintrag kann gelöscht worden sein, ohne dass die
			// Zuordnung im Listeneintrag mitgelöscht wurde
			if (false === $arrRegister)
			{
				$arrRegister = null;
				$intSpieler = 0;
			}
		}

		// Lebensdaten kommen aus dem Spielerregister, sofern verknüpft
		$arrQuelle = $arrRegister ?? $arrEintrag;

		$strGeburtstag = (string) Helper::getDate($arrQuelle['birthday'] ?? '');
		$strSterbetag = (string) Helper::getDate($arrQuelle['deathday'] ?? '');
		$strVon = (string) Helper::getDate($arrEintrag['fromDate'] ?? '');
		$strBis = (string) Helper::getDate($arrEintrag['toDate'] ?? '');

		$arrBild = $this->parseBild($arrEintrag['singleSRC'] ?? null);

		// Name und Orte sind Freitextfelder und werden hier maskiert, damit die
		// Templates sie unverändert ausgeben können. Das Feld 'info' bleibt
		// bewusst unmaskiert, es wird im Backend mit dem Editor gepflegt und
		// enthält gewollt Auszeichnungen.
		return array
		(
			'class'          => $intIndex % 2 ? 'odd' : 'even',
			'id'             => $intIndex,
			'name'           => StringUtil::specialchars((string) $arrEintrag['name']),
			'register_id'    => $intSpieler,
			'birthday'       => $strGeburtstag,
			'deathday'       => $strSterbetag,
			'playerbase_url' => $intSpieler ? SpielerregisterHelper::getPlayerlink($intSpieler) : '',
			'lifedate'       => $arrEintrag['viewLifedates'] ? Personendaten::lebensdaten($strGeburtstag, StringUtil::specialchars((string) ($arrQuelle['birthplace'] ?? '')), $strSterbetag, StringUtil::specialchars((string) ($arrQuelle['deathplace'] ?? ''))) : '',
			'fromDate'       => $strVon,
			'toDate'         => $strBis,
			'fromto'         => Personendaten::amtszeit($strVon, $strBis, (bool) $arrEintrag['fromDate_unknown'], (bool) $arrEintrag['toDate_unknown']),
			'info'           => $arrEintrag['info'],
			'image'          => $arrBild['singleSRC'] ?? '',
			'imageSize'      => $arrBild['imgSize'] ?? '',
			'imageTitle'     => $arrBild['imageTitle'] ?? '',
			'imageAlt'       => $arrBild['alt'] ?? '',
			'imageCaption'   => $arrBild['caption'] ?? '',
			'thumbnail'      => $arrBild['src'] ?? '',
		);
	}

	/**
	 * Erzeugt die Bildangaben eines Eintrags in der eingestellten Größe.
	 *
	 * Hat der Eintrag kein eigenes Bild, wird das in den Einstellungen
	 * hinterlegte Standardbild verwendet. Fehlt auch dieses oder liegt die
	 * Datei nicht mehr im Dateisystem, kommt ein leeres Array zurück und die
	 * Templates geben schlicht kein Bild aus.
	 *
	 * Aufbereitet wird über das Image-Studio, weil Controller::addImageToTemplate()
	 * in Contao 5 nicht mehr existiert. Das Studio gibt es in Contao 4.13 und
	 * Contao 5 gleichermaßen; getLegacyTemplateData() liefert dieselben
	 * Schlüssel, die die Templates schon bisher erwartet haben.
	 *
	 * @param string|null $varUuid Binäre UUID der Bilddatei aus dem Listeneintrag
	 *
	 * @return array<string, mixed> Template-Daten des Bildes oder ein leeres Array
	 */
	protected function parseBild($varUuid): array
	{
		$objDatei = null;

		if ($varUuid)
		{
			$objDatei = FilesModel::findByUuid($varUuid);
		}

		if (null === $objDatei)
		{
			$varStandard = Config::get('volunteeringlist_defaultImage');

			if ($varStandard)
			{
				$objDatei = FilesModel::findByUuid($varStandard);
			}
		}

		if (null === $objDatei)
		{
			return array();
		}

		/** @var Studio $objStudio */
		$objStudio = System::getContainer()->get('contao.image.studio');

		$objFigure = $objStudio
			->createFigureBuilder()
			->fromFilesModel($objDatei)
			->setSize(StringUtil::deserialize(Config::get('volunteeringlist_imageSize')))
			->buildIfResourceExists()
		;

		if (null === $objFigure)
		{
			return array();
		}

		return $objFigure->getLegacyTemplateData();
	}

	/**
	 * Liefert die Doctrine-Verbindung aus dem Container.
	 *
	 * Der Umweg über eine eigene Methode hält die Abfragen in compile() lesbar
	 * und macht deutlich, dass hier bewusst nicht mehr das abgekündigte
	 * $this->Database verwendet wird: In Contao 5 sind über System::import()
	 * geladene Objekte veraltet und verschwinden in Contao 6.
	 *
	 * @return Connection Die Standardverbindung der Contao-Installation
	 */
	protected function getConnection(): Connection
	{
		return System::getContainer()->get('database_connection');
	}
}
