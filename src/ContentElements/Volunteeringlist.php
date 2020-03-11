<?php

/**
 * Benutzerdefinierten Namespace festlegen, damit die Klasse ersetzt werden kann
 */
namespace Schachbulle\ContaoVolunteeringlistBundle\ContentElements;

class Volunteeringlist extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_volunteeringlist_default';

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Adresse aus Datenbank laden, wenn ID übergeben wurde
		if($this->volunteeringlist)
		{
			// Listentitel laden
			$objListe = $this->Database->prepare("SELECT * FROM tl_volunteeringlist WHERE id=?")
			                           ->execute($this->volunteeringlist);
			// Liste gefunden
			if($objListe)
			{
				// Voreinstellungen Bilder laden
				$picWidth = $this->volunteeringlist_picWidth ? $this->volunteeringlist_picWidth : $GLOBALS['TL_CONFIG']['volunteeringlist_picWidth'];
				$picHeight = $this->volunteeringlist_picHeight ? $this->volunteeringlist_picHeight : $GLOBALS['TL_CONFIG']['volunteeringlist_picHeight'];

				// Standard-CSS optional einbinden
				if($GLOBALS['TL_CONFIG']['volunteeringlist_css']) $GLOBALS['TL_CSS']['volunteeringlist'] = 'bundles/contaovolunteeringlist/default.css';

				// Template zuweisen
				if($this->volunteeringlist_alttemplate) // Alternativ-Template wurde definiert
					$this->Template = new \FrontendTemplate($this->volunteeringlist_template);
				else // Kein Alternativ-Template, dann Standard-Template nehmen
					($objListe->templatefile) ? $this->Template = new \FrontendTemplate($objListe->templatefile) : $this->Template = new FrontendTemplate($this->strTemplate);
				// Restliche Variablen zuweisen
				$this->Template->id = $this->volunteeringlist;
				$this->Template->vorlage = $objListe->templatefile;
				$this->Template->title = $objListe->title;
				// Listeneinträge laden
				$objItems = $this->Database->prepare("SELECT * FROM tl_volunteeringlist_items WHERE pid = ? AND published = ? ORDER BY sorting")
				                           ->execute($this->volunteeringlist, 1);
				if($objItems)
				{

					$item = array();
					while($objItems->next())
					{
						// Spielerregister laden, wenn ID vorhanden
						if($objItems->spielerregister_id)
						{
							$objRegister = $this->Database->prepare('SELECT * FROM tl_spielerregister WHERE id = ?')
							                    ->execute($objItems->spielerregister_id);
						}

						// Bild extrahieren
						if($objItems->singleSRC)
						{
							$objFile = \FilesModel::findByPk($objItems->singleSRC);
							$image = $objFile->path;
							$thumbnail = \Image::get($objFile->path, $picWidth, $picHeight, 'crop');
						}
						else
						{
							$image = false;
							$thumbnail = false;
						}

						// Person hinzufügen
						$item[] = array
						(
							'class'             => bcmod($i,2) ? 'odd' : 'even',
							'id'                => $i,
							'name'              => $objItems->name,
							'register_id'       => $objItems->spielerregister_id,
							'birthday'          => $objRegister ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objRegister->birthday) : \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItems->birthday),
							'deathday'          => $objRegister ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objRegister->deathday) : \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItems->deathday),
							'playerbase_url'    => $objItems->spielerregister_id ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getPlayerlink($objItems->spielerregister_id) : false,
							'lifedate'          => self::getLivedata($objItems, $objRegister),
							'fromDate'          => \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItems->fromDate),
							'toDate'            => \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItems->toDate),
							'fromto'            => self::getPeriod($objItems),
							'info'              => $objItems->info,
							'image'             => $image,
							'thumbnail'         => $thumbnail
						);
						$i++;
					}
					$this->Template->items = $item;
				}
			}
		}
		return;
	}

	/**
	* Gibt die Lebensdaten formatiert zurück
	* @param mixed
	* @return mixed
	*/
	protected function getLivedata($objItem, $objRegister)
	{
		$birthday = $objRegister ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objRegister->birthday) : \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->birthday);
		$deathday = $objRegister ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objRegister->deathday) : \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->deathday);
		$birthplace = $objRegister ? $objRegister->birthplace : $objItem->birthplace;
		$deathplace = $objRegister ? $objRegister->deathplace : $objItem->deathplace;

		$return = '';
		$return .= $birthday ? '* '.$birthday : '';
		$return .= $birthplace ? ($return ? ' '.$birthplace : $birthplace) : '';
		$return .= $deathday ? ($return ? ', &dagger; '.$deathday : '&dagger; '.$deathday) : '';
		$return .= $deathplace ? ($return ? ' '.$deathplace : $deathplace) : '';

		return $return;
	}

	/**
	* Gibt die Amtszeit formatiert zurück
	* @param mixed
	* @return mixed
	*/
	protected function getPeriod($objItem)
	{
		// Artikel als Einleitung für Amtszeitbeginn festlegen
		$von = $objItem->fromDate_unknown ? 'ca. ' : '';
		// Artikel als Einleitung für Amtszeitende festlegen
		$bis = $objItem->toDate_unknown ? 'ca. ' : '';
		$between = '';

		// Von/Bis-Artikel festlegen
		if($objItem->fromDate && $objItem->toDate)
		{
			$von .= \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->fromDate);
			$bis .= \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->toDate);
			$between = ' - ';
		}
		elseif($objItem->fromDate && !$objItem->toDate)
		{
			$von = 'seit '.$von.\Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->fromDate);
			$bis = '';
		}
		elseif(!$objItem->fromDate && $objItem->toDate)
		{
			$von = '';
			$bis = 'bis '.$bis.\Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getDate($objItem->toDate);
		}
		else
		{
			$von = '';
			$bis = '';
		}

		return $von.$between.$bis;
	}

}
