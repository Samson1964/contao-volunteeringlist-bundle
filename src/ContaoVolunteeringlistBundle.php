<?php

declare(strict_types=1);

/*
 * Dieser Quelltext gehört zu schachbulle/contao-volunteeringlist-bundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoVolunteeringlistBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle-Klasse der Funktionärslisten.
 *
 * Aus dem Klassennamen leitet Symfony den Namen des Asset-Verzeichnisses ab,
 * unter dem die Dateien aus Resources/public erreichbar sind: In diesem Fall
 * 'bundles/contaovolunteeringlist'. Der Name darf deshalb nicht geändert
 * werden, ohne die Pfade in Templates und DCA-Dateien mitzuziehen.
 */
class ContaoVolunteeringlistBundle extends Bundle
{
}
