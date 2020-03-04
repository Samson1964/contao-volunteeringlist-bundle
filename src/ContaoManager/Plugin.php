<?php

namespace Schachbulle\ContaoVolunteeringlistBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoVolunteeringlistBundle\ContaoVolunteeringlistBundle;

class Plugin implements BundlePluginInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(ContaoVolunteeringlistBundle::class)
				->setLoadAfter([ContaoCoreBundle::class]),
		];
	}
}
