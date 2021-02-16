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
		// Funktionäre aus Datenbank laden, wenn ID übergeben wurde
		if($this->volunteeringlist)
		{
			// Listentitel laden
			$objListe = $this->Database->prepare("SELECT * FROM tl_volunteeringlist WHERE id=?")
			                           ->execute($this->volunteeringlist);

			// Liste gefunden
			if($objListe)
			{
				// Voreinstellungen Bilder und CSS laden
				$defaultImage = $GLOBALS['TL_CONFIG']['volunteeringlist_defaultImage'];
				$imageSize = $GLOBALS['TL_CONFIG']['volunteeringlist_imageSize'];
				if($GLOBALS['TL_CONFIG']['volunteeringlist_css']) $GLOBALS['TL_CSS'][] = 'bundles/contaovolunteeringlist/default.css';

				if($this->volunteeringlist_alttemplate)
				{
					// Alternatives Template zuweisen
					$this->Template = new \FrontendTemplate($this->volunteeringlist_template);
				}
				else
				{
					// Template aus Listenkonfiguration zuweisen
					$this->Template = new \FrontendTemplate($objListe->templatefile);
				}

				// Restliche Variablen zuweisen
				$this->Template->id = $this->volunteeringlist;
				$this->Template->title = $objListe->title;

				// Listeneinträge laden
				$objItems = $this->Database->prepare("SELECT * FROM tl_volunteeringlist_items WHERE pid = ? AND published = ? ORDER BY sorting")
				                           ->execute($this->volunteeringlist, 1);

				// Einträge der Reihe nach durchgehen
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
						else
						{
							$objRegister = NULL;
						}

						// Bild extrahieren
						if($objItems->singleSRC)
						{
							$objFile = \FilesModel::findByPk($objItems->singleSRC);
						}
						else
						{
							$objFile = \FilesModel::findByUuid($defaultImage);
						}
						$objBild = new \stdClass();
						\Controller::addImageToTemplate($objBild, array('singleSRC' => $objFile->path, 'size' => unserialize($imageSize)), \Config::get('maxImageWidth'), null, $objFile);

						// Person hinzufügen
						$item[] = array
						(
							'class'             => bcmod($i,2) ? 'odd' : 'even',
							'id'                => $i,
							'name'              => $objItems->name,
							'register_id'       => $objItems->spielerregister_id,
							'birthday'          => $objRegister ? \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objRegister->birthday) : \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItems->birthday),
							'deathday'          => $objRegister ? \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objRegister->deathday) : \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItems->deathday),
							'playerbase_url'    => $objItems->spielerregister_id ? \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getPlayerlink($objItems->spielerregister_id) : false,
							'lifedate'          => $objItems->viewLifedates ? self::getLivedata($objItems, $objRegister) : false,
							'fromDate'          => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItems->fromDate),
							'toDate'            => \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItems->toDate),
							'fromto'            => self::getPeriod($objItems),
							'info'              => $objItems->info,
							'image'             => $objBild->singleSRC,
							'imageSize'         => $objBild->imgSize,
							'imageTitle'        => $objBild->imageTitle,
							'imageAlt'          => $objBild->alt,
							'imageCaption'      => $objBild->caption,
							'thumbnail'         => $objBild->src
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
		$birthday = $objRegister ? \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objRegister->birthday) : \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->birthday);
		$deathday = $objRegister ? \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objRegister->deathday) : \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->deathday);
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
			$von .= \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->fromDate);
			$bis .= \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->toDate);
			$between = ' - ';
		}
		elseif($objItem->fromDate && !$objItem->toDate)
		{
			$von = 'seit '.$von.\Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->fromDate);
			$bis = '';
		}
		elseif(!$objItem->fromDate && $objItem->toDate)
		{
			$von = '';
			$bis = 'bis '.$bis.\Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($objItem->toDate);
		}
		else
		{
			$von = '';
			$bis = '';
		}

		return $von.$between.$bis;
	}

}
