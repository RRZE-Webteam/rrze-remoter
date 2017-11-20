RRZE Remoter
===================

WordPress-Plugin
----------------

Das Remoter Plugin liest die Ordner und die darin enthaltenen Dateien eines Servers aus und gibt die Daten strukturiert in Wordpress aus.
Der Zugriff auf die Ordner und Dateien erfolgt eingeschränkt, da hierfür ein API-Key notwendig ist.

### __Grundlegende Funktionsweise des Plugins:__

1. Die Inhalte des Repositories __rrze-remoter-server-files__
müssen auf dem Remote-Server im Root abgelegt werden
2. Der Remote-Server muss registiert werden. Dies geschieht im WP-Backend nach der Installation des Plugins
3. Es muss ein API-Key angefordert werden.
4. Den Punkt API-Key Request im WP-Backend aufrufen und die Server-ID eintragen.
5. Die Server-ID im Shortcode bei id eintragen
6. Nun können die Daten von einem Remote-Server abgefragt werden.

![Vorgehensweise](img/vorgehensweise.png)

### __Dateien können auf folgende Möglichkeiten ausgelesen werden:__

- Es wird ein Verzeichnis **rekursiv** ausgelesen.
- Es wird ein Verzeichnis **nicht rekursiv** ausgelesen.
- Es wird **rekursiv nach einen Dateiennamen** gesucht und dieser ausgelesen.

### __Der hierfür benötigte Shortcode beinhaltet folgende Parameter:__

- **id** - Wird bei der Anlage eines Servers automatisch vergeben
- **file** - Wir lediglich nach einer bestimmten Datei gesucht, so muss hier der Dateiname angeben werden.
- **index** - Das Verzeichnis ab welchem gesucht werden soll.
- **recursiv** - Ist dieser werden auf 1, so werden alle Unterverzeichnisse mit durchsucht. Bei 0 wird nur das angegebene Verzeichnis ausgelesen.
- **itemsperpage** - Die Anzahl der Dateien pro Seite (nur view="pagination" relevant!)
- **filetype** - Nach welchen Dateiendungen gesucht werden soll (z. B. pdf). Wird der Parameter "all" verwendet wird nach den gängisten Dateiendungen gesucht.
- **link** - Bei link="1" wird der Dateiename wird verlinkt.
- **alias** - Wird der Parameter "file" verwendet, so kann ein Alias für den Dateinamen erzeugt werden.
- **view**  - Hier wird das Ausgabeformat angegeben. Zur Auswahl stehen:

```
- die Galerie (gallery), 
- die Tabelle mit Pagination (pagination), 
- eine Tabelle ohne Pagination (table), 
- eine Tablle mit <table></table> im MCE (table mit showheader=0)
- ein Glossar
```

- **orderby** - Hier kann die Spalte ausgewählt werden. Nach dieser wird sortiert.
**order** - Hier kann die Reihenfolge festgelegt werden asc (aufsteigend) oder desc (absteigend).
- **show** - Hier werden die anzuzeigenden Tabellenspalten bestimmt:

```
    -  Name (name),
    -  Download (download),
    -  Dateigröße (size),
    -  Verzeichnisname (folder),
    -  Dateityp (type),
    -  Das Datum der Datei (date)
```

Die Spalten werden genau in der Reihenfolge ausgegeben, wie Sie angegeben werden (z. B. show="name,size,folder"

- **showheader** - Falls der Tabellenkopf automatisch erzeugt werden soll (Tabelle ohne Pagination wird showheader=0 gesetzt)
- **filter** - Eine zusätzliche Möglichkeit das Suchergebnis einzuschränken. Wird filter gesetzt so müssen auch ein oder mehrere filetypes gesetzt werden.

