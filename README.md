RRZE Remoter
===================

WordPress-Plugin
----------------

Das Remoter Plugin liest die Ordner und die darin enthaltenen Dateien eines Servers aus und gibt die Daten strukturiert (z. B. in einer Tabelle als Links) in Wordpress aus.
Der Zugriff auf die Ordner und Dateien erfolgt eingeschränkt, da hierfür ein API-Key notwendig ist.

### __Grundlegende Funktionsweise des Plugins:__

1. Die Inhalte des Repositories __rrze-remoter-server-files__ (Gitlab)
müssen auf dem Remote-Server im Root-Verzeichnis des Webservers abgelegt werden
2. Der Remote-Server (z. B. zuv.fau.de) muss registiert werden. Dies geschieht im WP-Backend nach der Installation des Plugins.
3. Es muss ein API-Key angefordert werden. 
4. Hierzu muss im WP-Backend der Punkt API-Key Request  aufrufen und die Server-ID eintragen werden.
5. Der API-Key wird im WP-Backend eingetragen
6. Nun können die Daten von einem Remote-Server abgefragt werden.

![Vorgehensweise](img/vorgehensweise.png)

### __Dateien können auf folgende Möglichkeiten ausgelesen werden:__

- Es wird ein Verzeichnis **rekursiv** ausgelesen.
- Es wird ein Verzeichnis **nicht rekursiv** ausgelesen.
- Es wird **rekursiv nach einen Dateiennamen** gesucht und dieser ausgelesen.

### __Der hierfür benötigte Shortcode beinhaltet folgende Parameter:__

- **id** - Wird bei der Anlage des Remote-Servers automatisch vergeben
- **file** - Wird lediglich nach einer bestimmten Datei gesucht, so muss hier der Dateiname angeben werden.
- **index** - Das Verzeichnis in dem oder ab welchem gesucht werden soll.
- **recursiv** - Ist dieser werden auf 1, so werden alle Unterverzeichnisse mit durchsucht. Bei 0 wird nur das angegebene Verzeichnis ausgelesen.
- **itemsperpage** - Die Anzahl der Dateien pro Seite (nur bei view="pagination" relevant!)
- **filetype** - Nach welchen Dateiendungen gesucht werden soll (z. B. pdf). Wird der Parameter "all" verwendet, so wird nach folgenden Dateiendungen gesucht (jpg, jpeg, png, tif, gif, txt, doc, docx, xls, pdf).
- **link** - Bei link="1" wird der Dateiename wird verlinkt.
- **alias** - Wird der Parameter "file" verwendet, so kann ein alternativer Anzeigename für den Dateinamen übergeben werden.
- **view**  - Hier wird das Ausgabeformat angegeben. Zur Auswahl stehen:

```
- die Galerie (gallery), 
- die Tabelle mit Pagination (pagination), 
- eine Tabelle ohne Pagination (view="table" mit showheader=1), 
- eine Tablle mit Html-Header im TinyMCE (view="table" mit showheader=0)
- ein Glossar
```

- **orderby** - Hier kann die Spalte ausgewählt werden nach welcher sortiert wird. (siehe Tabellenspalten weiter unten)
- **order** - Hier kann die Reihenfolge festgelegt werden asc (aufsteigend) oder desc (absteigend).
- **show** - Hier werden die anzuzeigenden Tabellenspalten bestimmt:

```
    -  Name (name),
    -  Download (download),
    -  Dateigröße (size),
    -  Verzeichnisname (folder),
    -  Dateityp (type),
    -  Das Datum der Datei (date)
```

Die Spalten werden genau in der Reihenfolge ausgegeben, wie sie angegeben werden (z. B. show="name,size,folder"

- **showheader** - Falls der Tabellenkopf automatisch erzeugt werden soll (view="table" mit showheader=1). Bei showheader=0 wird der Tabellenkopf über den TinyMCE erzeugt.
- **filter** - Eine zusätzliche Möglichkeit das Suchergebnis einzuschränken. Wird filter gesetzt so müssen auch ein oder mehrere filetypes gesetzt werden.

### __Beispiele für mögliche Shortcodes:__

_für die Galerieansicht:_

[remoter id="" index="images" filetype="all" recursiv="0" view="gallery"]

Es wird im Verzeichnis images nach den gängisten Dateiformaten (siehe oben) gesucht.

_Tabelle mit Pagination:_

[remoter id="" index="universitaet" filter="englisch" filetype="pdf,jpg" recursiv="1" itemsperpage="1" view="pagination" link="1" show="folder,size,type,date,download,name" orderby="size" order="asc"]

Es wird ab dem Verzeichnis univerisitaet rekursiv nach den Dateiformaten (pdf,jpg) gesucht. Da der Filter auf Englisch gesetzt wurde wird nach Dateien mit dem Wort Englisch und dem dem Dateiformat (pdf oder jpg) gesucht.
Pro Seite wird eine Datei angezeigt.
Die Dateinamen werden verlinkt. 
Es werden die Spalten in der Reihenfolge Verzeichnis, Dateigröße, Dateityp, Datum, Download und Dateiname ausgegeben. Das Ergebnis wird nach der Spalte Dateigröße aufsteigend sortiert.

_Tabelle ohne Pagination:_

[remoter id="" index="universitaet" filetype="pdf" link="1" recursiv="1" view="table" orderby="name" show="name,download,size,folder,date" order="asc" showheader="1"]

Es wird ab dem Verzeichnis univerisitaet rekursiv nach allen Dateien mit dem Dateiformat (pdf) gesucht. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge Dateiname, Download, Dateigröße, Verzeichnnis und Datum ausgegeben. Das Ergebnis wird nach der Spalte Dateiname aufsteigend sortiert.

_Tabelle mit Html-Header im TinyMCE:_

```
<div>
    <table>
        <tr>
            <th>Dateiname</th>
            <th>Download</th>
        </tr>
        [remoter id="2212879" index="universitaet/organisation/recht/pruefungsordnungen/phil" alias="Testfile" file="Buchwissenschaften 2-Fach-BA 20150815 i.d.F. 20170415 -Aenderungssatzung.pdf"]
        [remoter id="2212879" index="universitaet/organisation/recht/pruefungsordnungen/phil" alias="Testfile2" file="Buchwissenschaften 2-Fach-BA 20150815 i.d.F. 20170415 -konsolidierte Fassung.pdf"]
    </table>
</div>
```

Es wird nach den beiden Dateien, welche unter file angeben sind ab dem index rekursiv gesucht. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge Dateiname, Download ausgegeben.