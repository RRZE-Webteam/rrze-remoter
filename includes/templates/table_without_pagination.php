<?php

/*
* Die Ausgabe der Datei .meta.txt
* Es wird eine Tabelle überhalb der Dateien ausgegeben
*/
?>

<?php $meta = $data ?>
<?php $meta_store = array(); ?>
<?php for($i = 0; $i < sizeof($meta); $i++) { ?> 

    <?php if(!empty($meta[$i]['meta']) && $header == 1) { ?>

        <?php $transient = get_transient('rrze-remoter-transient'); 

            if(empty($transient)) {
                $j = 1;
            } else {
                $j = $transient;
                $j++;
            }

        ?>
        
        <div class="accordion" id="accordion-1">
        <div class="accordion-group">
        <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $j ?>" href="#collapse_<?php echo $j ?>"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['titel'] : '');  ?></a></div>
        <div id="collapse_<?php echo $j ?>" class="accordion-body" style="display: none;">
        <div class="accordion-inner clearfix">
        
        <table>
            
            <tr><td colspan="2"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['beschreibung'] : '');  ?></td></tr>

            <?php foreach($meta[$i]['meta']['directory']['file-aliases'][0] as $key => $value) { ?>

                <?php $meta_store[] = array(
                    'key'   => $value,
                    'value' => $key
                )
                ?>
                <tr><td><strong>Dateiname:</strong> <?php echo $key ?></td><td><strong> Anzeigename:</strong> <?php echo $value ?></td></tr>

            <?php } ?>
                    
        </table>
            
        </div>
        </div>
        </div>
        </div>  

        <?php set_transient( 'rrze-remoter-transient', $j, DAY_IN_SECONDS ); ?>
    
    <?php } ?>

<?php } ?>

<?php
/*
* Die Spaltennamen werden zurückgegenben
* z. B. date,name,download
*/    


if (!function_exists('getHeaderData')) {
    function getHeaderData($columns) {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }
}

$tableHeader = getHeaderData($show_columns);

/*
* Der Tabellenkopf wird erstellt
* Ist $header 1, dann wird der Tabellenkopf automatisch erzeugt
*/    

if($header) { ?>

<table>
    
    <tr>
    
    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
                case 'size': ?>
            <th>Dateigröße</th>
            <?php break;
                case 'type': ?>
            <th>Dateityp</th>
            <?php break;
                case 'download': ?>
            <th>Download</th>
            <?php break;
                case 'folder': ?>
            <th>Verzeichnisname</th>
            <?php break;
                case 'name': ?>
            <th>Dateiname</th>
            <?php break;
                case 'date': ?>
            <th>Datum</th>
            <?php break;
                case 'default': ?>
            <?php break; ?>

        <?php } ?>

    <?php } ?>
    
    </tr>
        
<?php } ?>
    
<?php 

/*
* Der Tabelleneintrag .meta.txt wird aus den Tabelleneinträgen entfernt
*/ 

for($i = 0; $i <sizeof($meta); $i++) { ?> 

    <?php if(($key = array_search('.meta.txt', $meta[$i])) !== false) { ?>

        <?php unset($meta[$i]);  ?>

        <?php $data = array_values($meta); ?>

    <?php } ?>

<?php } ?>
    
<?php 

/*
* Ausgabe der Tabelleneinträge gemäß der Sortierung $tableHeader
*/ 

for($i = 0; $i <sizeof($data); $i++) { ?> 

    <tr>    

    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
                case 'size': ?>
        
                    <td><?php echo formatSize($data[$i]['size']) ?></td>
                    
            <?php break;
                case 'type': ?>
                    <?php $extension = $data[$i]['extension']; ?>
                
                    <?php if ($extension == 'pdf') { ?>
                    
                        <td><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                        
                    <?php } elseif($extension == 'pptx') { ?>
                        
                        <td><i class=" file-powerpoint-o" aria-hidden="true"></i></td>

                    <?php } else{ ?>
                        
                        <td><i class="fa fa-file-image-o" aria-hidden="true"></i></td> 
                        
                    <?php } ?>
                        
            <?php break;
                case 'download': ?>
                        
                    <td><a href="http://<?php echo $url['host'] . $data[$i]['image'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
            
            <?php break;
                case 'folder': ?>
                    
                    <td><?php echo getFolder($data[$i]['dir']) ?></td>
                    
            <?php break;
                case 'name': ?>
                <?php if ($link) { ?>
                  
                    <td><a class="lightbox" rel="lightbox-' . $id . '" href="http://<?php echo $url['host'] . $data[$i]['image'] ?>">
                        
                    <?php

                    $key = array_search(basename($data[$i]['path']), array_column($meta_store, 'value'));

                    if($key) {
                        echo $meta_store[$key]['key'];
                    } else {
                        echo basename($data[$i]['path']); 
                    }

                    ?></a></td>    
                    
                <?php } else { ?>
                    
                    <td><?php echo basename($data[$i]['path']) ?></td>  
                <?php  } ?>
            <?php break;
                case 'date': ?>
                
                    <td><?php echo date('j F Y', $data[$i]['change_time']) ?></td>
                    
            <?php break;
        
                case 'default': ?>
                    
            <?php break; ?>

        <?php } ?>

    <?php } ?>
    
    </tr>

<?php } ?>

<?php

/*
* Formatierung der Dateigröße
* return $size
*/ 
            
if (!function_exists('formatSize')) {
    function formatSize($bytes) {

        if ($bytes>= 1073741824) {
            $size = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
           $size = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 0) . ' KB';
        } elseif ($bytes > 1) {
            $size = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $size = '1 byte';
        } else {
            $size = '0 bytes';
        }
        
        return $size;
    }
}

/*
* Verzeichnis wird ermittelt
* return $folder
*/

if (!function_exists('getFolder')) {
    function getFolder($directory) {
 
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];
        
        return $folder;
    
    }
}
     