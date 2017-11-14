<?php $this->glossary_array = $this->remote_data ?>
<?php for($i = 0; $i < sizeof($meta); $i++) { ?> 

    <?php if(!empty($meta[$i]['meta'])) { ?>

        <?php $transient = get_transient('rrze-remoter-transient-table'); 

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
                            <td colspan="2"><strong>Beschreibung: </strong><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['beschreibung'] : '');  ?></td>
                        </tr>
                        <?php foreach($meta[$i]['meta']['directory']['file-aliases'][0] as $key => $value) { ?>
                            <?php $meta_store[] = array(
                                'key'   => $value,
                                'value' => $key
                            )
                            ?>
                            <tr>
                                <td>
                                    <strong>Dateiname:</strong> <?php echo $key ?></td>
                                <td><strong> Anzeigename:</strong> <?php echo $value ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    </div>
                </div>
            </div>
        </div>  
        <?php set_transient( 'rrze-remoter-transient-table', $j, DAY_IN_SECONDS ); ?>
    <?php } ?>
<?php } ?>
<h3>Glossar</h3>
<div class="fau-glossar"><ul class="letters" aria-hidden="true">
<?php foreach ($letters as $key => $value) { ?>

   <?php if (in_array($value, $array_without_numbers)) { ?>

        <li><a href="#letter-<?php echo $value ?>"data-link="<?php echo $shortcodeValues['link'] ?>"data-columns="<?php echo $shortcodeValues['showColumns'] ?>"data-host="<?php echo $url['host'] ?>" data-letter="<?php echo $value ?>"><?php echo $value ?></a></li>

   <?php } else { ?>

       <li class="muted"><?php echo $value ?></li>

   <?php } ?>
<?php } ?>
</ul>
</div>
<div id="glossary">
    <table>
        <tr>
            <?php foreach($tableHeader as $key => $column) {

                switch($column) {
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
                        <th>Ordner</th>
                    <?php break;
                    case 'name': ?>
                        <th>Name</th>
                    <?php break;
                    case 'date': ?>
                        <th>Datum</th>
                    <?php break;   
                }
            } ?>
        </tr>
       <?php for($i = 0; $i < sizeof($dataSorted); $i++) { ?>
            
            <tr>
        
            <?php foreach($tableHeader as $key => $column) { ?>

                <?php switch($column) { 
                    case 'size': ?>
                        <td><?php echo self::formatSize($data[$i]['size']) ?></td>
                        <?php  
                        break;
                    case 'type': ?>
                        <?php $extension = $data[$i]['extension']; ?>
                        <?php if($extension == 'pdf') { ?>
                            <td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                        <?php } elseif($extension == 'pptx') { ?>
                            <td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>
                        <?php } else { ?>
                            <td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>
                        <?php }
                        break;
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
                    <?php  } 
                        break;
                    case 'date': ?>
                        <td><?php echo date('j F Y', $data[$i]['change_time']) ?></td>
                        <?php break; 
                }

            } ?>
            
            </tr>
            
       <?php } ?>
       
    </table>
</div>