<?php $meta = $data ?>
<?php $meta_store = array(); ?>
<?php for($i = 0; $i < sizeof($meta); $i++) { ?> 

    <?php if(!empty($meta[$i]['meta']) && $header == 1) { ?>
        
        <table>

            <tr><td colspan="2"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['titel'] : '');  ?><br/>
            <?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['beschreibung'] : '');  ?></td></tr>

                <?php foreach($meta[$i]['meta']['directory']['file-aliases'][0] as $key => $value) { ?>
            
                    <?php $meta_store[] = array(
                        'key'   => $value,
                        'value' => $key
                    )
                    ?>
                    <tr><td><strong>Dateiname:</strong> <?php echo $key ?></td><td><strong> beinhaltet:</strong> <?php echo $value ?></td></tr>

                <?php } ?>
                    
        </table>

    <?php } ?>

<?php } ?>
        
<?php
if (!function_exists('getHeaderData')) {
    function getHeaderData($columns) {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }
}

$tableHeader = getHeaderData($show_columns);

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

    
    
<?php for($i = 0; $i <sizeof($data); $i++) { ?> 
        
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

<?php }
            
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


if (!function_exists('getFolder')) {
    function getFolder($directory) {
 
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];
        
        return $folder;
    
    }
}
     