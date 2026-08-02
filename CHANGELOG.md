# Funktionärsliste Changelog

## Version 3.0.0 (2026-08-02)

Das Bundle läuft ab dieser Version unter Contao 4.13 **und** Contao 5 mit PHP 8.1 bis 8.3.
Ältere PHP- und Contao-Versionen werden nicht mehr unterstützt.

**Wichtig beim Aktualisieren:** Das Standardbild in den Einstellungen muss einmal neu
ausgewählt werden, siehe den Fix zur beschädigten Bild-Kennung weiter unten.

### Contao 5 und PHP 8

* Change: `Controller::addImageToTemplate()` durch das Image-Studio (`contao.image.studio`)
  ersetzt. Die Methode gibt es in Contao 5 nicht mehr; das Studio steht in Contao 4.13 wie
  in Contao 5 zur Verfügung und liefert dieselben Template-Variablen.
* Change: Die Auswahlliste der Bildgrößen holt den Dienst unter seinem aktuellen Namen
  `contao.image.sizes`. Der bisherige Name `contao.image.image_sizes` ist in Contao 4.13 nur
  noch ein Alias und in Contao 5 entfernt — dort brach die Einstellungsseite mit „You have
  requested a non-existent service“ ab.
* Change: Die Konstanten `VERSION` und `BUILD` sowie `REQUEST_TOKEN` sind aus den DCA-Dateien
  entfernt; in Contao 5 sind sie nicht mehr definiert.
* Change: Der Bearbeiten-Knopf am Inhaltselement erzeugt seine Adresse über den Router statt
  über die seit Contao 4 nicht mehr vorhandene Datei `contao/main.php`.
* Change: `specialchars()` und `standardize()` durch `StringUtil::specialchars()` bzw.
  `StringUtil::specialcharsAttribute()` ersetzt.
* Change: Datenbankzugriffe laufen über die Doctrine-Verbindung statt über `$this->Database`;
  über `System::import()` geladene Objekte gelten in Contao 5 als veraltet.
* Change: Der Toggle-Schalter in beiden Übersichten nutzt das Contao-eigene `act=toggle`
  statt des Haste-Togglers. Damit entfällt eine Abhängigkeit, die in der `composer.json`
  ohnehin nie eingetragen war.
* Change: `'dataContainer' => 'Table'` auf `DC_Table::class` umgestellt und
  `Config::get('validImageTypes')` durch den Parameter `contao.image.valid_extensions`
  ersetzt — beides war seit Contao 4.9 bzw. 4.12 abgekündigt.
* Change: Der Eintrag für `ContainerAwareInterface` ist aus der `services.yaml` entfernt. Die
  Schnittstelle wurde in Symfony 7 gestrichen und ließ den Container unter Contao 5 schon
  beim Kompilieren scheitern.
* Change: Die Felder `guest` und `space` sind aus der Palette des Inhaltselements entfernt;
  in Contao 5 gibt es sie nicht mehr.
* Change: `bcmod()` durch den Modulo-Operator ersetzt. Der bisherige Aufruf setzte die
  bcmath-Erweiterung voraus, ohne sie in der `composer.json` zu fordern.
* Change: `unserialize()` durch `StringUtil::deserialize()` ersetzt, das mit leeren und
  nicht serialisierten Werten umgehen kann.

### Fehlerbehebungen

* Fix: Die Bild-Kennung des Standardbilds wird in den Einstellungen jetzt in lesbarer
  Schreibweise abgelegt. Bisher wurde der 16 Byte lange Binärwert in die Datei
  `system/config/localconfig.php` geschrieben, wo Nullbytes und Backslashes verloren gingen —
  das Standardbild wurde deshalb nie gefunden und Einträge ohne eigenes Bild blieben leer.
  Das Bild muss einmalig neu ausgewählt werden.
* Fix: Ist im Inhaltselement keine Liste ausgewählt oder wurde die gewählte Liste gelöscht,
  lief das Template unter PHP 8 in einen Fatal Error („foreach() argument must be of type
  array|object“). Die Liste ist jetzt in jedem Fall gesetzt.
* Fix: Die Auswahl des abweichenden Templates am Inhaltselement blieb immer leer, weil nach
  Templates mit dem Präfix `mod_volunteeringlist_` gesucht wurde, das es in diesem Bundle nie
  gab. Gesucht wird jetzt nach `ce_volunteeringlist_`.
* Fix: Das gewählte Template ersetzte bisher das komplette Template-Objekt des
  Inhaltselements, wodurch dessen Daten verloren gingen. Jetzt wird nur der Templatename
  umgestellt.
* Fix: Namen, Geburts- und Sterbeorte werden vor der Ausgabe maskiert. Bisher konnten
  Auszeichnungen aus diesen Freitextfeldern unverändert in die Seite gelangen.
* Fix: Wurde ein verknüpfter Spielerregister-Eintrag gelöscht, brach die Ausgabe ab. Jetzt
  fällt der Eintrag auf seine eigenen Lebensdaten zurück.
* Fix: Die Rückruffunktionen für die Auswahllisten kommen mit einem fehlenden
  DataContainer zurecht.
* Fix: Die Beschriftungen der Schaltflächen in beiden Übersichten waren nie hinterlegt und
  blieben leer.

### Aufräumen

* Change: Der Bearbeiten-Knopf der Listeneigenschaften prüft die Feldrechte über den
  Security-Helper. Die Knöpfe zum Kopieren und Löschen prüften bisher die Rechte der
  News-Archive (`newp`) — eine Berechtigung, die dieses Bundle gar nicht kennt, weshalb sie
  für Nicht-Administratoren immer ausgegraut waren.
* Change: Die Textbausteine für Lebensdaten und Amtszeit stecken in der eigenen Klasse
  `Helper\Personendaten` und sind mit 13 Unit-Tests abgedeckt.
* Delete: Ungenutzten Code entfernt — die Methoden `generateAlias()` und `pagePicker()`
  (beide bezogen sich auf Felder, die es in diesen Tabellen nicht gibt), die Unterpalette
  `protected`, die Einstellungen `volunteeringlist_picWidth` und `volunteeringlist_picHeight`
  sowie die Sprachbausteine der nie vorhandenen Felder `alias` und `typ`.
* Change: Jede Funktion hat einen deutschen Kommentarblock, alle Dateien beginnen mit einem
  einheitlichen Kopf, `declare(strict_types=1)` in allen Klassendateien.
* Change: `services.yml` in `services.yaml` umbenannt.

## Version 2.2.2 (2026-07-29)

* Fix: Warning: Undefined array key "deleteConfirm" bei contao:migrate -> Lesezugriffe auf $GLOBALS['TL_LANG'] in den DCA-Dateien mit `?? null` bzw. `?? array()` abgesichert, da der DcaLoader die Sprachdateien noch nicht geladen hat

## Version 2.2.1 (2023-11-29)

* Change: tl_volunteeringlist und tl_volunteeringlist_items -> Toggle-Funktion durch Haste-Toggler ersetzt
* Fix: Anpassungen PHP 8 wegen undefinierter Variablen
* Change: Beschreibung, Keywords und Homepage in der composer.json ergänzt, damit Packagist das Paket verständlich darstellt und über die Suche auffindbar macht

## Version 2.2.0 (2023-06-18)

* Add: PHP 8 in composer.json als erlaubt eingetragen

## Version 2.1.3 (2021-09-08)

* Fix: 1366 Incorrect integer value: 'text' for column 'volunteeringlist'

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
