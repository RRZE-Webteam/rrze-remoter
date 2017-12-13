<?php $this->res = $this->remote_data; ?>
<?php array_multisort(array_column($this->res, $sortOrderby), $sortOrder , $this->res); ?>
<?php if($shortcodeValues['showInfo']) { ?>
    <?php for($i = 0; $i < sizeof($metadata); $i++) { ?> 
        <?php if(!empty($metadata[$i][0])) { ?>
           <?php $accordionId = uniqid(); ?>
            <div class="accordion" id="accordion-1">
                <div class="accordion-group">
                <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $accordionId ?>" href="#collapse_<?php echo $accordionId ?>"><?php echo (!empty($metadata[$i][0]['directory']['titel']) ? $metadata[$i][0]['directory']['titel'] : '');  ?></a></div>
                    <div id="collapse_<?php echo $accordionId ?>" class="accordion-body" style="display: none;">
                        <div class="accordion-inner clearfix">
                        <table>
                            <tr>
                                <td colspan="2"><strong>Beschreibung: </strong><?php echo (!empty($metadata[$i][0]['directory']['titel']) ? $metadata[$i][0]['directory']['beschreibung'] : '');  ?></td>
                            </tr>
                            <?php foreach($metadata[$i][0]['directory']['file-aliases'][0] as $key => $value) { ?>
                                <?php $meta_store[] = array(
                                    'key'   => $value,
                                    'value' => $key
                                )
                                ?>
                                <tr>
                                    <td><strong>Dateiname:</strong> <?php echo $key ?></td>
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
<?php } ?>
<?php $this->a = $meta_store; ?>
<div id="result">
    <table>
        <tr>
            <?php 
            foreach($tableHeader as $key => $column) {

                switch($column) {
                    case 'directory': ?>
                        <th><?php _e('Verzeichnisname', 'rrze-remoter');?></th>
                    <?php break;
                    case 'name': ?>
                        <th><?php _e('Dateiname', 'rrze-remoter');?></th>
                    <?php break;
                    case 'date': ?>
                        <th><?php _e('Erstellungsdatum', 'rrze-remoter');?></th>
                    <?php break;
                    case 'type': ?>
                        <th><?php _e('Dateityp', 'rrze-remoter');?></th>
                    <?php break;
                    case 'size':?>
                        <th><?php _e('Dateigröße', 'rrze-remoter');?></th>
                       <?php break;
                    case 'download': ?>
                        <th><?php _e('Herunterladen', 'rrze-remoter');?></th>
                     <?php break;
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
                            <td><?php echo RRZE\Remoter\Class_Help_Methods::formatSize($data[$i][$j]['size']) ?></td>
                            <?php break;
                        case 'type': ?>
                            <?php  $extension = $data[$i][$j]['extension']; ?>
                            <?php if($extension == 'pdf') { ?>
                                <td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                            <?php } elseif ($extension == 'pptx' || $extension =='ppt') { ?>
                                <td align="center"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i></td>
                            <?php } elseif ($extension == 'docx' || $extension =='doc' ) { ?>
                                <td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>
                            <?php } elseif ($extension == 'xlsx' || $extension =='xls') { ?>
                                <td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>
                            <?php } elseif ($extension == 'mpg' || $extension =='mpeg'|| $extension =='mp4' || $extension =='m4v') { ?>
                                <td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>
                            <?php } else { ?>
                                <td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>
                            <?php }
                            break; 
                        case 'download': ?>
                            <td align="center"><a href="http://<?php echo $domain . $data[$i][$j]['dir'] . $data[$i][$j]['name'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
                           <?php break;
                        case 'directory': ?>
                            <td><?php echo RRZE\Remoter\Class_Help_Methods::getFolder($data[$i][$j]['path']) ?></td>
                         <?php break;
                        case 'name': ?>
                            <?php $extension = $data[$i][$j]['extension']; ?>
                            <?php if ($shortcodeValues['link']) { ?> 
                                <?php $path = $data[$i][$j]['name']; ?>
                                <?php $store = $meta_store; ?> 
                                <?php $file = $shortcodeValues['file'] ?>
                                <?php $imgFormats = RRZE\Remoter\Class_Help_Methods::getImageFormats(); ?>     
                            
                                <?php if (!in_array($extension, $imgFormats)) { ?>
                                    <td>
                                        <a href="http://<?php echo $domain . $data[$i][$j]['dir'] . $data[$i][$j]['name'] ?>">
                                            <?php
                                                echo RRZE\Remoter\Class_Help_Methods::getMetafileNames($path, $store, $file);
                                            ?>
                                        </a>
                                    </td> 
                                <?php } else { ?>
                                    <td>
                                        <a class="lightbox" rel="lightbox-' . $id . '" href="http://<?php echo $domain . $data[$i][$j]['dir'] . $data[$i][$j]['name'] ?>">
                                            <?php
                                                echo RRZE\Remoter\Class_Help_Methods::getMetafileNames($path, $store, $file);
                                            ?>
                                        </a>
                                    </td>  
                                <?php } ?>
                            <?php } else { ?>
                                <td>
                                    <?php echo str_replace('_',' ', basename($data[$i]['path'])) ?>
                                </td>  
                            <?php  }
                            break;
                        case 'date': ?>
                           <td><?php echo $data[$i][$j]['date'] ?></td>
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
                data-host="<?php echo $domain ?>"
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