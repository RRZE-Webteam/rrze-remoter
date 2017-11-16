<?php $this->res = $this->remote_data; ?>
<?php for($i = 0; $i < sizeof($meta); $i++) { ?> 
    <?php if(!empty($meta[$i]['meta'])) { ?>
       <?php $accordionId = uniqid(); ?>
        <div class="accordion" id="accordion-1">
            <div class="accordion-group">
            <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $accordionId ?>" href="#collapse_<?php echo $accordionId ?>"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['titel'] : '');  ?></a></div>
                <div id="collapse_<?php echo $accordionId ?>" class="accordion-body" style="display: none;">
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
    <?php } ?>
<?php } ?>
<?php  $this->meta = $meta_store; ?>
<div id="result">
    <table>
        <tr>
            <?php 
            foreach($tableHeader as $key => $column) {

                switch($column) {
                    case 'size':?>
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
                     <?php   break;
                    case 'name': ?>
                        <th>Name</th>
                    <?php    break;
                    case 'date': ?>
                        <th>Datum</th>
                     <?php   break;   
                }
            }

            ?>
        </tr>
        <?php for($i = 0;  $i < 1; $i++) { ?>
            <?php for($j = 0; $j < $itemscount; $j++) { ?>
                <tr>
                    <?php foreach($tableHeader as $key => $column) { ?>

                    <?php  switch($column) { 
                        case 'size': ?>
                            <td><?php echo self::formatSize($data[$i][$j]['size']) ?></td>
                            <?php break;
                        case 'type': ?>
                            <?php  $extension = $data[$i][$j]['extension']; ?>
                            <?php if($extension == 'pdf') { ?>
                                <td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                            <?php }elseif($extension == 'pptx') { ?>
                                <td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>
                            <?php }else{ ?>
                                <td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>
                            <?php }
                            break; 
                        case 'download': ?>
                            <td><a href="http://<?php echo $url['host'] . $data[$i][$j]['image'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
                           <?php break;
                        case 'folder': ?>
                            <td><?php echo self::getFolder($data[$i][$j]['dir']) ?></td>
                         <?php break;
                        case 'name': ?>
                            <?php $extension = $data[$i][$j]['extension']; ?>
                            <?php if ($shortcodeValues['link']) { ?> 
                                <?php $path = basename($data[$i][$j]['path']); ?>
                                <?php $store = $meta_store; ?> 
                                <?php $file = $shortcodeValues['file'] ?>
                                
                                <?php if ($extension == 'pdf') { ?>
                                <td>
                                    <a href="http://<?php echo $url['host'] . $data[$i][$j]['image'] ?>">
                                        <?php
                                            echo self::getMetafileNames($path, $store, $file);
                                        ?>
                                    </a>
                                </td> 
                            
                            <?php } else { ?>
                                <td>
                                    <a class="lightbox" rel="lightbox-' . $id . '" href="http://<?php echo $url['host'] . $data[$i][$j]['image'] ?>">
                                        <?php
                                            echo self::getMetafileNames($path, $store, $file);
                                        ?>
                                    </a>
                                </td>  
                            <?php } ?>

                        <?php } else { ?>

                        <td><?php echo basename($data[$i]['path']) ?></td>  
                        
                        <?php  }
                            break;
                        case 'date': ?>
                           <td><?php echo date('j. F Y', $data[$i][$j]['change_time']) ?></td>
                          <?php break; ?>
                   <?php } ?>
                <?php } ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </table>
</div>
 
<nav class="pagination pagebreaks" role="navigation">
    <h3>Seite:</h3>
        <span class="subpages">
            <?php for ($i = 1; $i <= $pagecount; $i++) { ?>
                <a data-filetype="<?php echo $shortcodeValues['filetype'] ?>" href="#get_list"
                data-recursiv="<?php echo $shortcodeValues['recursive'] ?>"
                data-index="<?php echo $shortcodeValues['fileIndex'] ?>"
                data-host="<?php echo $url['host'] ?>"
                data-chunk="<?php echo $number_of_chunks ?>"
                data-pagecount-value="<?php echo $pagecount ?>"
                data-columns="<?php echo $shortcodeValues['showColumns'] ?>" 
                data-link= "<?php echo $shortcodeValues['link'] ?>"
                class="page-<?php echo $i ?>">
                <span class="<?php echo ($i == 1 ? 'number active' : 'number') ?>"><?php echo $i ?></span>
                </a>
            <?php } ?>
        </span>
</nav>