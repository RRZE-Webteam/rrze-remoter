<?php array_multisort(array_column($meta, 'name'), SORT_ASC, $meta); ?>
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
                                <tr>
                                    <td colspan="2">
                                        <strong>Beschreibung: </strong>
                                            <?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['beschreibung'] : '');  ?>
                                    </td>
                                </tr>
                                <?php foreach($meta[$i]['meta']['directory']['file-aliases'][0] as $key => $value) { ?>
                                <?php $meta_store[] = array(
                                    'key'   => $value,
                                    'value' => $key
                                );
                                ?>
                                <tr>
                                    <td><strong>Dateiname:</strong>
                                        <?php echo $key ?></td><td><strong> Anzeigename:</strong> <?php echo $value ?>
                                    </td>
                                </tr>
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
      
<?php $data = self::deleteMetaTxtEntries($meta); ?>

<?php for($i = 0; $i <sizeof($data); $i++) { ?> 

    <tr>    

    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
                case 'size': ?>
        
                    <td><?php echo self::formatSize($data[$i]['size']) ?></td>
                    
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
                    
                    <td><?php echo self::getFolder($data[$i]['dir']) ?></td>
                    
            <?php break;
                case 'name': ?>
                <?php if ($shortcodeValues['link']) { ?>
                  
                    <td><a class="lightbox" rel="lightbox-' . $id . '" href="http://<?php echo $url['host'] . $data[$i]['image'] ?>">
                        
                    <?php

                    $key = array_search(basename($data[$i]['path']), array_column($meta_store, 'value'));
                    
                    if($key > 0 || $key === 0 && $shortcodeValues['file'] == '' && !empty($meta_store)) {
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