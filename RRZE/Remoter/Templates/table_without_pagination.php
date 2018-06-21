<?php
namespace RRZE\Remoter\Templates;
use RRZE\Remoter\Help_Methods;

defined('ABSPATH') || exit;

if($shortcodeValues['showInfo']) { ?>
    <?php for($i = 0; $i < sizeof($metadata); $i++) { ?> 
        <?php if(!empty($metadata[$i][0]) && $header == 1) { ?>
          <?php $accordionId = uniqid(); ?>
            <div class="accordion" id="accordion-1">
            <div class="accordion-group">
                 <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $accordionId ?>" href="#collapse_<?php echo $accordionId ?>"><?php echo (!empty($metadata[$i][0]['directory']['titel']) ? $metadata[$i][0]['directory']['titel'] : '');  ?></a></div>
                    <div id="collapse_<?php echo $accordionId ?>" class="accordion-body" style="display: none;">
                        <div class="accordion-inner clearfix">
                            <table>
                                <tr>
                                    <td colspan="2"><strong>Beschreibung: </strong>
                                        <?php echo (!empty($metadata[$i][0]['directory']['titel']) ? $metadata[$i][0]['directory']['beschreibung'] : '');  ?>
                                    </td>
                                </tr>
                                <?php foreach($metadata[$i][0]['directory']['file-aliases'][0] as $key => $value) { ?>
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
                <th><?php _e('Directory name', 'rrze-remoter');?></th>
            <?php break;
            case 'name': ?>
                <th><?php if($fileheader) { 
                    echo Help_Methods::getFolder($data[0]['path']); 
                } else {
                    _e('Filename', 'rrze-remoter');
                }
                ?></th>
            <?php break;
            case 'date': ?>
                <th><?php _e('Creation date', 'rrze-remoter');?></th>
            <?php break;
            case 'type': ?>
                <th><?php _e('Type of file', 'rrze-remoter');?></th>
            <?php break;
            case 'size':?>
                <th><?php _e('File size', 'rrze-remoter');?></th>
               <?php break;
            case 'download': ?>
                <th><?php _e('Download', 'rrze-remoter');?></th>
             <?php break;
        
        } ?>
    <?php } ?>
    </tr>
<?php } ?>
<?php for($i = 0; $i <sizeof($data); $i++) { ?> 
    <tr>    

    <?php foreach($tableHeader as $key => $column) { ?>

        <?php switch($column) {
                case 'size': ?>
        
                    <td><?php echo Help_Methods::formatSize($data[$i]['size']) ?></td>
                    
                <?php break;
                case 'type': ?>
                    <?php $extension = $data[$i]['extension']; ?>
                
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
                    <?php } ?>
                        
                <?php break;
                case 'download': ?>
                        
                    <td align="center"><a class="no_mtli" rel="no_mtli" href="https://<?php echo $domain . $data[$i]['dir'] . $data[$i]['name'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
            
                <?php break;
                case 'directory': ?>
                    
                    <td><?php echo Help_Methods::getFolder($data[$i]['path']) ?></td>
                    
                <?php break;
                case 'name': ?>
                    <?php $extension = $data[$i]['extension']; ?>
                    <?php $replaced_name = Help_Methods::convertUmlauts($data[$i]['name']) ?>
                    <?php if ($shortcodeValues['link']) { ?> 
                        <?php $path = $replaced_name ?>
                        <?php $store = $meta_store; ?> 
                        <?php $file = $shortcodeValues['file'] ?>
                        <?php $imgFormats = Help_Methods::getImageFormats(); ?>     
                            
                        <?php if (!in_array($extension, $imgFormats)) { ?>
                        <td>
                            <a class="no_mtli" rel="no_mtli" href="https://<?php echo $domain . $data[$i]['dir'] . $data[$i]['name'] ?>"><?php echo ($alias) ? $alias : Help_Methods::getMetafileNames($path, $store, $file); ?></a>
                        </td> 
                        <?php } else { ?>
                        <td>
                            <a class="lightbox" rel="lightbox-' . $id . '" href="https://<?php echo $domain . $data[$i]['dir'] . $data[$i]['name'] ?>"><?php echo ($alias) ? $alias : Help_Methods::getMetafileNames($path, $store, $file);?></a>
                        </td>  
                        <?php } ?>

                    <?php } else { ?>
                        <td><?php echo str_replace('_',' ', $data[$i]['name']) ?></td>  
                    <?php  }
                    break;
                case 'date': ?>
                
                        <td><?php echo date("d.m.Y", $data[$i]['date']) ?></td>
                    
                <?php break;
        
                case 'default': ?>
                    
            <?php break; ?>

        <?php } ?>

    <?php } ?>
    
    </tr>

<?php } ?>

<?php if($header) echo '</table>'; ?>