<?php if($shortcodeValues['showInfo']) { ?>
    <?php for($i = 0; $i < sizeof($meta); $i++) { ?> 
        <?php if(!empty($meta[$i]['meta']) && $header == 1) { ?>
          <?php $accordionId = uniqid(); ?>
            <div class="accordion" id="accordion-1">
            <div class="accordion-group">
                 <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $accordionId ?>" href="#collapse_<?php echo $accordionId ?>"><?php echo (!empty($meta[$i]['meta']['directory']['titel']) ? $meta[$i]['meta']['directory']['titel'] : '');  ?></a></div>
                    <div id="collapse_<?php echo $accordionId ?>" class="accordion-body" style="display: none;">
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
        <?php } ?>
    <?php } ?>
 <?php } ?>
<?php
if($header) { ?>
<table>
    <tr>
    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
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
        
        } ?>
    <?php } ?>
    </tr>
<?php } ?>
      
<?php $data = RRZE\Remoter\Class_Help_Methods::deleteMetaTxtEntries($meta); ?>
<?php for($i = 0; $i <sizeof($data); $i++) { ?> 

    <tr>    

    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
                case 'size': ?>
        
                    <td><?php echo RRZE\Remoter\Class_Help_Methods::formatSize($data[$i]['size']) ?></td>
                    
                <?php break;
                case 'type': ?>
                    <?php $extension = $data[$i]['extension']; ?>
                
                    <?php if($extension == 'pdf') { ?>
                        <td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                    <?php }elseif($extension == 'pptx' || $extension =='ppt') { ?>
                        <td align="center"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i></td>
                    <?php }elseif($extension == 'docx' || $extension =='doc' ) { ?>
                        <td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>
                    <?php }elseif($extension == 'xlsx' || $extension =='xls') { ?>
                        <td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>
                    <?php }elseif($extension == 'mpg' || $extension =='mpeg'|| $extension =='mp4' || $extension =='m4v') { ?>
                        <td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>
                    <?php }else{ ?>
                        <td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>
                    <?php } ?>
                        
                <?php break;
                case 'download': ?>
                        
                    <td align="center"><a href="http://<?php echo $url['host'] . $data[$i]['image'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
            
                <?php break;
                case 'directory': ?>
                    
                    <td><?php echo RRZE\Remoter\Class_Help_Methods::getFolder($data[$i]['dir']) ?></td>
                    
                <?php break;
                case 'name': ?>
                    <?php $extension = $data[$i]['extension']; ?>
                    <?php if ($shortcodeValues['link']) { ?> 
                        <?php $path = basename($data[$i]['path']); ?>
                        <?php $store = $meta_store; ?> 
                        <?php $file = $shortcodeValues['file'] ?>
                        <?php $imgFormats = RRZE\Remoter\Class_Help_Methods::getImageFormats(); ?>     
                            
                        <?php if (!in_array($extension, $imgFormats)) { ?>
                        <td>
                            <a href="http://<?php echo $url['host'] . $data[$i]['image'] ?>">
                                <?php
                                    echo RRZE\Remoter\Class_Help_Methods::getMetafileNames($path, $store, $file);
                                ?>
                            </a>
                        </td> 

                    <?php } else { ?>
                        <td>
                            <a class="lightbox" rel="lightbox-' . $id . '" href="http://<?php echo $url['host'] . $data[$i]['image'] ?>">
                                <?php
                                    echo RRZE\Remoter\Class_Help_Methods::getMetafileNames($path, $store, $file);
                                ?>
                            </a>
                        </td>  
                    <?php } ?>

                    <?php } else { ?>

                    <td><?php echo str_replace('_',' ', basename($data[$i]['path'])) ?></td>  

                    <?php  }
                break;
                case 'date': ?>
                
                    <td><?php echo date('j F Y', $data[$i]['change_time']) ?></td>
                    
                <?php break;
        
                case 'default': ?>
                    
            <?php break; ?>

        <?php } ?>

    <?php } ?>
    
    </tr>

<?php } ?>