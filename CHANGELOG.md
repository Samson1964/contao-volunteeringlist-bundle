# Funktionärsliste Changelog

## Version 2.1.2 (2021-02-16)

* Fix: Spielerregisterdaten bleiben im Speicher und beeinflussen nachfolgende Datensätze

## Version 2.1.1 (2020-12-14)

* Fix: Alternatives Template ist falsch verlinkt in Volunteeringlist.php (mit der Checkbox)
* Fix: Template ce_volunteeringlist_mini hinzugefügt

## Version 2.1.0 (2020-12-14)

* Add: Checkbox um optional im Frontend die Lebensdaten auszublenden

## Version 2.0.0 (2020-12-01)

* Fix: includeBlankOption fehlte in tl_volunteeringlist_items.spielerregister_id
* Add: Abhängigkeit schachbulle/contao-helper-bundle
* Fix: Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper ersetzt durch Schachbulle\ContaoHelperBundle\Classes
* Delete: volunteeringlist_picWidth und volunteeringlist_picHeight in tl_settings
* Add: volunteeringlist_defaultImage und volunteeringlist_imageSize in tl_settings (Bildgrößen aus Contao werden verwendet)
* Change: Template ce_volunteeringlist_default umgebaut (Contao-4-Format)
* Delete: Templates mod_volunteeringlist - werden ersetzt durch ce_volunteeringlist
* Add: Umbau auf Bildgrößen in Volunteeringlist.php

## Version 1.0.2 (2020-03-11)

* Fix: Aufruf getDate in Volunteeringlist.php
* Fix: Generierung der Funktionärsliste Frontend

## Version 1.0.1 (2020-03-11)

* Fix: Template mod_volunterringlist_mini

## Version 1.0.0 (2020-03-11)

* Fertigstellung des Bundles

## Version 0.0.1 (2020-03-04)

* Übernahme der Version 1.3.1 aus Contao 3
