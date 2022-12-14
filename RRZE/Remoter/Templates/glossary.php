<?php
namespace RRZE\Remoter\Templates;
use RRZE\Remoter\Helper;

defined('ABSPATH') || exit;

$this->glossary_array = $data;
?>

<?php if($shortcodeValues['showInfo']) { ?>
    <?php for($i = 0; $i < sizeof($metadata); $i++) { ?> 
        <?php if(!empty($metadata[$i][0])) { ?>
            <?php $accordionId = uniqid(); ?>
            <div class="accordion" id="accordion-1">
                <div class="accordion-group">
                <div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-<?php echo $accordionId ?>" href="#collapse_<?php echo $accordionId ?>"><?php echo (!empty($metadata[$i][0]['directory']['titel']) ? $metadata[$i][0]['directory']['titel'] : '');  ?></a></div>
                    <div id="collapse_<?php echo  $accordionId ?>" class="accordion-body" style="display: none;">
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

<?php $this->meta = $meta_store; ?>
<div class="fau-glossar"><ul class="letters" aria-hidden="true">
<?php foreach ($letters as $key => $value) { ?>

   <?php if (in_array($value, $array_without_numbers)) { ?>

        <li><a href="#letter-<?php echo $value ?>" data-link="<?php echo $shortcodeValues['link'] ?>" data-columns="<?php echo $shortcodeValues['showColumns'] ?>" data-host="<?php echo $apihost ?>" data-letter="<?php echo $value ?>"><?php echo $value ?></a></li>

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
                    case 'directory': ?>
                        <th><?php _e('Directory name', 'rrze-remoter');?></th>
                    <?php break;
                    case 'name': ?>
                        <th><?php _e('Filename', 'rrze-remoter');?></th>
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
                }
            } ?>
        </tr>
        <?php for($i = 0; $i < sizeof($dataSorted); $i++) { ?>
            
            <tr>
        
            <?php foreach($tableHeader as $key => $column) { ?>

                <?php switch($column) { 
                    case 'size': ?>
                        <td><?php echo Helper::formatSize($data_new[$i]['size']) ?></td>
                        <?php  
                        break;
                    case 'type': ?>
                        <?php $extension = $data_new[$i]['extension']; ?>
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
                        <td align="center"><a href="<?php echo $apiurl . $data_new[$i]['dir'] . $data_new[$i]['name'] ?>"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>
                        <?php break;
                    case 'directory': ?>
                        <td><?php echo Helper::getFolder($data_new[$i]['path']) ?></td>
                        <?php break;
                    case 'name': ?>
                        <?php $extension = $data_new[$i]['extension']; ?>
                        <?php if ($shortcodeValues['link']) { ?> 
                            <?php $path = $data_new[$i]['name']; ?>
                            <?php $store = $meta_store; ?> 
                            <?php $file = $shortcodeValues['file'] ?>
                            <?php $imgFormats = Helper::getImageFormats(); ?>     
                            
                            <?php if (!in_array($extension, $imgFormats)) { ?>
                                <td>
                                    <a href="<?php echo $apiurl . $data_new[$i]['dir'] . $data_new[$i]['name'] ?>">
                                        <?php
                                            echo Helper::getMetafileNames($path, $store, $file);
                                        ?>
                                    </a>
                                </td> 
                            <?php } else { ?>
                                <td>
                                    <a class="lightbox" rel="lightbox-' . $id . '" href="<?php echo $apiurl . $data_new[$i]['dir'] . $data_new[$i]['name'] ?>">
                                        <?php
                                            echo Helper::getMetafileNames($path, $store, $file);
                                        ?>
                                    </a>
                                </td>  
                            <?php } ?>

                        <?php } else { ?>

                        <td><?php echo str_replace('_', ' ', basename($data_new[$i]['path'])) ?></td>  

                        <?php  }
                            break;
                    case 'date': ?>
                        <td><?php echo date("d.m.Y", $data_new[$i]['date']) ?></td>
                        <?php break; 
                }

            } ?>
            
            </tr>
            
       <?php } ?>
       
    </table>
</div>