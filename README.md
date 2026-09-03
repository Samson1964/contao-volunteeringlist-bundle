# Funktionärsliste für Contao

Verwaltet Listen von Amtsträgern — etwa die Präsidenten eines Verbandes — mit Amtszeit,
Lebensdaten und Bild und gibt sie im Frontend als Inhaltselement aus.

Läuft unter **Contao 4.13 und Contao 5** mit **PHP 8.1 bis 8.4**.

## Installation

```bash
composer require schachbulle/contao-volunteeringlist-bundle
```

Anschließend im Contao-Manager oder auf der Konsole die Datenbank abgleichen:

```bash
vendor/bin/contao-console contao:migrate
```

## Was das Bundle mitbringt

* Ein Backend-Modul **Funktionärslisten** unter *Inhalte*, in dem beliebig viele Listen mit
  ihren Einträgen gepflegt werden.
* Ein Inhaltselement **Funktionärsliste** in der Gruppe *Schach-Elemente*, das eine dieser
  Listen ausgibt.
* Zwei Templates: `ce_volunteeringlist_default` mit Bild und Beschreibung sowie
  `ce_volunteeringlist_mini` als schlanke Tabelle.

## Eine Liste anlegen

1. **Inhalte → Funktionärslisten → Neue Liste**: Titel vergeben und das Template wählen, mit
   dem die Liste normalerweise ausgegeben wird.
2. Über den Bearbeiten-Knopf der Liste die einzelnen **Einträge** anlegen. Je Eintrag werden
   erfasst:
   * **Personenangaben** — Name, Geburts- und Sterbedatum samt Ort, dazu ein Bild.
   * **Amtszeit** — Von- und Bis-Datum. Ist ein Datum nur ungefähr bekannt, kennzeichnet die
     Angabe *Datum ungeklärt* es; im Frontend erscheint dann „ca.“ davor.
   * **Spielerregister** — die Verknüpfung mit einem Eintrag aus
     `schachbulle/contao-spielerregister-bundle`. Ist sie gesetzt, kommen die Lebensdaten von
     dort und die im Eintrag erfassten werden ignoriert. Der Name wird zusätzlich auf die
     Personenseite verlinkt.
   * **Veröffentlichung** — ob die Lebensdaten im Frontend erscheinen und ob der Eintrag
     überhaupt ausgegeben wird.
3. Die Reihenfolge der Einträge wird in der Übersicht per Verschieben festgelegt; sie wird
   im Frontend genau so übernommen.

## Datumsangaben

Datumsangaben dürfen unvollständig sein und werden entsprechend kurz ausgegeben:

| Eingabe      | Ausgabe      |
|--------------|--------------|
| `24.12.1900` | `24.12.1900` |
| `12.1900`    | `12.1900`    |
| `1900`       | `1900`       |

Daraus entsteht die Amtszeit:

| Von              | Bis  | Ausgabe           |
|------------------|------|-------------------|
| 1950             | 1960 | `1950 - 1960`     |
| 1950             | —    | `seit 1950`       |
| —                | 1960 | `bis 1960`        |
| 1950 (ungeklärt) | 1960 | `ca. 1950 - 1960` |

Und die Lebensdaten, sofern *Lebensdaten anzeigen* gesetzt ist:

```text
* 24.12.1900 Berlin, † 01.01.1980 Hamburg
```

## Das Inhaltselement

Im Inhaltselement wird die auszugebende Liste gewählt. Der kleine Knopf daneben öffnet ihre
Einträge direkt im Popup. Soll die Liste an dieser Stelle anders aussehen als sonst,
aktiviert **Alternatives Template verwenden** die abweichende Templatewahl.

Die Überschrift des Inhaltselements gibt Contao selbst aus, die Templates kümmern sich nur
um die Liste.

## Einstellungen

Unter *System → Einstellungen* im Abschnitt **Funktionärslisten**:

| Einstellung  | Bedeutung                                                                      |
|--------------|--------------------------------------------------------------------------------|
| Standardbild | Bild für Einträge ohne eigenes Bild. Bleibt es leer, wird kein Bild ausgegeben. |
| Bildgröße    | Größe, auf die alle Funktionärsbilder gebracht werden.                          |
| Standard-CSS | Bindet das mitgelieferte `default.css` im Frontend ein.                         |

## Eigene Templates

Ein eigenes Template wird als `ce_volunteeringlist_<name>.html5` im Ordner `templates/`
angelegt und steht danach in der Auswahl. Es bekommt diese Variablen:

| Variable       | Inhalt                                                       |
|----------------|--------------------------------------------------------------|
| `$this->title` | Titel der Liste                                              |
| `$this->id`    | ID der Liste                                                 |
| `$this->items` | Array der Einträge, immer gesetzt — auch bei leerer Liste     |

Je Eintrag in `$this->items`:

| Schlüssel                                | Inhalt                                                  |
|------------------------------------------|---------------------------------------------------------|
| `class`                                  | `odd` oder `even`                                       |
| `id`                                     | Laufende Nummer ab 0                                    |
| `name`                                   | Name der Person, bereits maskiert                       |
| `register_id`                            | ID im Spielerregister, sonst `0`                        |
| `playerbase_url`                         | Adresse der Personenseite, sonst leer                   |
| `birthday`, `deathday`                   | Geburts- und Sterbedatum als Text                       |
| `lifedate`                               | Lebensdaten als fertige Zeile, leer wenn abgeschaltet   |
| `fromDate`, `toDate`                     | Beginn und Ende der Amtszeit als Text                   |
| `fromto`                                 | Amtszeit als fertiger Zeitraum                          |
| `info`                                   | Freitext aus dem Editor, enthält gewollt HTML           |
| `image`                                  | Pfad zur Bilddatei in Originalgröße, für die Lightbox   |
| `thumbnail`                              | Pfad zum aufbereiteten Bild                             |
| `imageSize`                              | `width`- und `height`-Attribut des aufbereiteten Bildes |
| `imageAlt`, `imageTitle`, `imageCaption` | Metadaten der Bilddatei                                 |

## Tests

```bash
vendor/bin/phpunit
```

## Voraussetzungen

* [schachbulle/contao-helper-bundle](https://github.com/Samson1964/contao-helper-bundle) für
  die Umwandlung der Datumsangaben
* [schachbulle/contao-spielerregister-bundle](https://github.com/Samson1964/contao-spielerregister-bundle)
  für die Verknüpfung mit der Personendatenbank

## Lizenz

LGPL-3.0-or-later — siehe [LICENSE](LICENSE).

**Frank Hoppe**
