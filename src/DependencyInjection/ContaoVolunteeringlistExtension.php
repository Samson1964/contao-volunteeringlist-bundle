<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Lädt die Dienstdefinitionen des Bundles in den Symfony-Container.
 */
class ContaoVolunteeringlistExtension extends Extension
{
	/**
	 * Liest die services.yaml des Bundles ein.
	 *
	 * Wird beim Aufbau des Containers einmalig von Symfony aufgerufen. Das
	 * Bundle hat keine eigene Konfiguration, deshalb wird $mergedConfig nicht
	 * ausgewertet.
	 *
	 * @param array            $mergedConfig Die zusammengeführte Bundle-Konfiguration, hier ungenutzt
	 * @param ContainerBuilder $container    Der im Aufbau befindliche Container
	 */
	public function load(array $mergedConfig, ContainerBuilder $container): void
	{
		$loader = new YamlFileLoader(
			$container,
			new FileLocator(__DIR__ . '/../Resources/config')
		);

		$loader->load('services.yaml');
	}
}
