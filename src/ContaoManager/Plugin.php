<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoVolunteeringlistBundle\ContaoVolunteeringlistBundle;

/**
 * Meldet das Bundle beim Contao Manager an.
 */
class Plugin implements BundlePluginInterface
{
	/**
	 * Gibt die Bundle-Konfiguration an den Contao Manager zurück.
	 *
	 * Das Bundle wird nach dem Contao-Kern geladen, damit dessen DCA-Dateien
	 * und Sprachdateien bereits vorliegen, wenn dieses Bundle die Tabellen
	 * tl_content und tl_settings erweitert.
	 *
	 * @param ParserInterface $parser Wird vom Manager übergeben, hier nicht benötigt
	 *
	 * @return array<int, BundleConfig> Die Konfiguration dieses einen Bundles
	 */
	public function getBundles(ParserInterface $parser): array
	{
		return array
		(
			BundleConfig::create(ContaoVolunteeringlistBundle::class)
				->setLoadAfter(array(ContaoCoreBundle::class)),
		);
	}
}
