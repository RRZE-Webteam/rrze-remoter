RRZE Remoter
============

WordPress-Plugin
----------------

Das Remoter Plugin liest die Ordner und die darin enthaltenen Dateien eines Remote-Servers (entfernten Webservers) aus und gibt die Daten strukturiert (z. B. in einer Tabelle als Links) in Wordpress aus.

__Wichtig__: Dieses WordPress-Plugin hängt von der Installation des PHP-Skripts [rrze-remoter-server-files](https://gitlab.rrze.fau.de/rrze-webteam/rrze-remoter-server-files.git) auf dem Remote-Server ab.

### Erstellen eines neuen Remote-Servers

1. Verwenden Sie das Menü "Remote-Server / erstellen", um einen neuen Remote-Server zu erstellen
2. Geben Sie den Titel und die API-URL des Remote-Servers ein und klicken Sie dann auf "Veröffentlichen"
3. Die Daten können nun über den Shortcode [remoter] vom Remote-Server abgerufen werden.

### Shortcode-Parameter

- **id** - Wird bei der Anlage des Remote-Servers automatisch vergeben. (Standardwert leer)
- **file** - Wird lediglich nach einer bestimmten Datei gesucht, so muss hier der Dateiname angeben werden. (Standardwert leer)
- **index** - Das Verzeichnis in dem oder ab welchem gesucht werden soll. (Standardwert leer)
- **recursiv** - Bei recursiv="1" ist, dann werden alle Unterverzeichnisse mit durchsucht. Bei recursiv="0" wird nur das angegebene Verzeichnis ausgelesen. (Standardwert recursiv="1")
- **itemsperpage** - Die Anzahl der Dateien pro Seite. (nur bei view="pagination" relevant!) (Standardwert itemsperpage="5")
- **filetype** - Nach welchen Dateiendungen gesucht wird. Der Parameter akzeptiert mehrere Werte, die durch ein Komma getrennt sind. Beispielsweise filetype="pdf,jpg" es werden PDF- und JPG-Dateien angezeigt. (Standardwert filetype="pdf")
- **link** - Bei link="1" wird der Dateiename verlinkt. (Standardwert link="0")
- **alias** - Wird der Parameter "file" verwendet, so kann ein alternativer Anzeigename für den Dateinamen übergeben werden. (Standardwert leer)
- **view**  - Hier wird das Ausgabeformat angegeben. (Standardwert view="list"). Zur Auswahl stehen
    - die Galerie (view="gallery")
    - die Tabelle mit Pagination (view="pagination")
    - eine Tabelle ohne Pagination (view="table" mit showheader="1")
    - eine Tablle mit Html-Header im TinyMCE (view="table" mit showheader="0")
    - ein Glossar (view="glossary")
    - eine Liste (view="list").


- **orderby** - Hier kann die Spalte ausgewählt werden nach welcher sortiert wird. Sortierung möglich für die Spalten: "name", "size", "date". (Standardwert orderby="name")
- **order** - Hier kann die Reihenfolge festgelegt werden "asc" (aufsteigend) oder "desc" (absteigend). (Standardwert order="asc")
- **show** - Hier werden die anzuzeigenden Tabellenspalten bestimmt. Der Parameter akzeptiert mehrere Werte, die durch ein Komma getrennt sind. Die Spalten werden genau in der Reihenfolge ausgegeben, wie sie angegeben werden. Beispielsweise show="name,size,directory". (Standardwert show="name,download"). Zur Auswahl stehen
    -  Dateiname (show="name")
    -  Download (show="download")
    -  Dateigröße (show="size")
    -  Verzeichnisname (show="directory")
    -  Dateityp (show="type")
    -  Erstellungsdatum (show="date")

- **showheader** - Falls der Tabellenkopf automatisch erzeugt werden soll (view="table" mit showheader="1"). Bei showheader="0" muss der Tabellenkopf selbst über den TinyMCE erzeugt werden. (Standardwert showheader="0")
- **filter** - Eine zusätzliche Möglichkeit das Suchergebnis einzuschränken. Wird filter gesetzt, so müssen auch ein oder mehrere filetypes (z. B. filter="pdf") gesetzt werden. (Standardwert leer)
- **showmetainfo** - Die Ausgabe der .meta.json Datei wird oberhalb z. B. der Tabelle in einem Accordion angezeigt und kann ein- (showmetainfo="1") und ausgeblendet (showmetainfo="0") werden. (Standardwert showmetainfo="1")
- **gallerytitle** - Unterhalb des Gallerie Bildes wird der IPTC-Titel angezeigt (Standardwert gallerytitle="1")
- **gallerydescription** - Unterhalb der Gallerie Beschreibung wird die IPTC-Beschreibung angezeigt (Standardwert gallerydescription="1")

### Beispiele für mögliche Shortcodes

__Galerieansicht__
```
[remoter id="" index="images" filetype="jpg,gif" recursiv="0" view="gallery"]
```
Es wird im Verzeichnis images nach den Dateiformaten "jpg" oder "gif" gesucht.

__Tabelle mit Pagination__
```
[remoter id="" index="dateien" filter="englisch" filetype="pdf,jpg" recursiv="1" itemsperpage="1" view="pagination" link="1" show="directory,size,type,date,download,name" orderby="size" order="asc"]
```
Es wird ab dem Verzeichnis "dateien" rekursiv nach den Dateiformaten "pdf" oder "jpg" gesucht. Da der Filter auf "englisch" gesetzt wurde wird nach Dateien mit dem Wort "englisch" und dem dem Dateiformat "pdf" oder "jpg" gesucht. Pro Seite wird eine Datei angezeigt. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge Verzeichnis, Dateigröße, Dateityp, Datum, Download und Dateiname ausgegeben. Das Ergebnis wird nach der Spalte Dateigröße aufsteigend sortiert.

__Tabelle ohne Pagination__
```
[remoter id="" index="dateien" filetype="pdf" link="1" recursiv="1" view="table" orderby="name" show="name,download,size,directory,date" order="asc" showheader="1"]
```
Es wird ab dem Verzeichnis "dateien" rekursiv nach allen Dateien mit dem Dateiformat "pdf" gesucht. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge Dateiname, Download, Dateigröße, Verzeichnnis und Datum ausgegeben. Das Ergebnis wird nach der Spalte Dateiname aufsteigend sortiert.

__Tabelle mit Html-Header im TinyMCE__

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

Es wird nach den Dateinamen, welche bei file angegeben wurden rekursiv gesucht. Ausgangspunkt der Suche ist das Verzeichnis im Parameter index. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge Dateiname und Download (Standardwert) ausgegeben.

__Glossar__
```
[remoter id="" index="dateien" filetype="pdf" recursiv="1" view="glossary" link="1" show="download,type,date,size,name,directory"]
```
Es wird ab dem Verzeichnis "dateien" rekursiv nach allen Dateien mit dem Dateiformat "pdf" gesucht. Die Dateinamen werden verlinkt. Es werden die Spalten in der Reihenfolge von show ausgegeben.

### Ausgabe der Datei .meta.json (Optional)

In jedem Verzeichnis kann eine Datei mit dem Namen **.meta.json** hinzugefügt werden. Diese Datei folgt dem **JSON Syntax** und hat eine **vordefinierte Struktur**. Mit der **.meta.json** lassen sich **kryptische Dateinamen** in der Anzeige vermeiden. So kann jedem Dateinamen ein **alternativer Anzeigename** zugeordnet werden, welcher dann auch angezeigt wird. Darüber hinaus wird oberhalb des jeweiligen Ausgabeformates (z. B. table) ein Accordion mit den JSON-Daten angezeigt. Hier der grundlegende Aufbau der .meta.json im JSON Syntax:

```
[{
"<b>directory</b>": {
    "<b>titel</b>": "Studienordnungen Buchwissenschaften",
    "<b>beschreibung</b>": "In diesem Verzeichnis sind Studienordnungen für den Studiengang Buchwissenschaften",
    "<b>file-aliases</b>": [{
      "<b>Dateiname</b>": "<b>Anzeigename</b>",
      "Buchwissenschaften_23_5_lb_zb.pdf": "Prüfungsordnung für Buchwissenschaft",
      "":"",
      ...
    }]
  }
}]
```

Wird diesem Format **nicht strikt gefolgt**, so kann die **.meta.json nicht ausgelesen** werden und dementsprechend keine schönen Anzeigenamen ausgegeben werden. Zur besseren Handhabung mit dem **JSON Sytax** empfielt sich die Installation z. B. des Editors [Visual Studio Code](https://code.visualstudio.com/). Mit diesem kann auf einfach Art und Weise mit dem JSON Sytax gearbeitet werden und ein **Code Highlighting** (in der blauen Fußzeile unten rechts) zur besseren Darstellung eingestellt werden. Alternativ können auch sogenannte Online JSON Formatter und Validator wie [JSON Formatter](https://jsonformatter.curiousconcept.com/) oder [JSON Viewer](https://codebeautify.org/jsonviewer) um nur ein paar, der zahlreich vorhanden aufzuzählen, verwendet werden.

Wird eine rekursive Suche durchgeführt, so werden alle .meta.json Dateien ausgelesen und geordnet nach den Verzeichnissen in einem Accordion oberhalb z. B. der Tabelle angezeigt. Falls ein Verzeichnis keine .meta.json beinhaltet so ist der Anzeigename gleich dem Dateinamen. Um nicht unötig viele .meta.json Dateien pflegen zu müssen, bietet sich an im gewünschten Ausgangsverzeichnis eine einzige .meta.json anzulegen und auch für die unterhalb dieses Verzeichnis liegende Dokumente einen Dateiennamen mit dem entsprechenden Anzeigenamen zu pflegen.
