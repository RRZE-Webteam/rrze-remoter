<?php

namespace RRZE\Remoter;

use RRZE\Remoter\Grab_Remote_Files;
use RRZE\Remoter\Help_Methods;

defined('ABSPATH') || exit;

class Build_Shortcode {

    protected $plugin_file;
    
    protected $plugin_dir_path;
    
    protected $shortcode_atts;
    
    protected $glossary_array;
    
    protected $meta;
    
    protected $res;
    
    protected $a;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_dir_path = plugin_dir_path($plugin_file);

        add_action('wp_ajax_rrze_remote_table_ajax_request', [$this, 'rrze_remote_table_ajax_request']);
        add_action('wp_ajax_nopriv_rrze_remote_table_ajax_request', [$this, 'rrze_remote_table_ajax_request']);
        add_shortcode('remoter', [$this, 'shortcode']);
        add_action('wp_footer', [$this, 'rrze_remote_table_script_footer']);

        add_action('wp_ajax_rrze_remote_glossary_ajax_request', [$this, 'rrze_remote_glossary_ajax_request']);
        add_action('wp_ajax_nopriv_rrze_remote_glossary_ajax_request', [$this, 'rrze_remote_glossary_ajax_request']);

        add_action('wp_footer', [$this, 'rrze_remote_glossary_script_footer']);
    }

    public function shortcode($atts) {

        $this->shortcode_atts = shortcode_atts(array(
            'id' => '',
            'file' => '',
            'index' => '',
            'recursiv' => '1',
            'itemsperpage' => '5',
            'filetype' => 'pdf',
            'link' => '1',
            'alias' => '',
            'view' => 'table',
            'orderby' => 'name',
            'order' => 'asc',
            'show' => 'name,download',
            'showheader' => '0',
            'filter' => '',
            'showmetainfo' => '1',
            'errormsg' => '1',
            'fileheader' => '0',
            'gallerytitle' => '1',
            'gallerydescription' => '1'
                ), $atts);

        return $this->show_results();
    }

    private function show_results() {

        $shortcodeValues = array(
            'fileIndex' => $this->shortcode_atts['index'],
            'view' => $this->shortcode_atts['view'],
            'recursive' => $this->shortcode_atts['recursiv'],
            'filetype' => $this->shortcode_atts['filetype'],
            'showColumns' => $this->shortcode_atts['show'],
            'link' => $this->shortcode_atts['link'],
            'showHeader' => $this->shortcode_atts['showheader'],
            'file' => $this->shortcode_atts['file'],
            'showInfo' => $this->shortcode_atts['showmetainfo'],
            'alias' => $this->shortcode_atts['alias'],
            'errormsg' => $this->shortcode_atts['errormsg'],
            'fileheader' => $this->shortcode_atts['fileheader'],
            'gallerytitle' => $this->shortcode_atts['gallerytitle'],
            'gallerydescription' => $this->shortcode_atts['gallerydescription']
        );

        $remoter_post = get_post(absint($this->shortcode_atts['id']));
        if (!$remoter_post || $remoter_post->post_type != 'remoter') {
            return '';
        }

        $domain = get_post_meta($remoter_post->ID, 'domain', true);
        $api_key = get_post_meta($remoter_post->ID, 'apikey', true);

        $data = Grab_Remote_Files::get_files_from_remote_server($this->shortcode_atts, $domain, $api_key);

        if ($data) {
            $view = $shortcodeValues['view'];
            $tableHeader = Help_Methods::getHeaderData($shortcodeValues['showColumns']);
            $meta = $data;

            $meta_store = array();
            $order = $this->shortcode_atts['order'];
            $orderby = $this->shortcode_atts['orderby'];
            
            switch ($view) {
                case 'gallery':
                    ob_start();
                    $gallerytitle = $shortcodeValues['gallerytitle'];
                    $gallerydescription = $shortcodeValues['gallerydescription'];
                    include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/gallery.php';
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'glossary':
                    ob_start();
                    $id = uniqid();
                    $metajson = Help_Methods::getJsonFile($shortcodeValues, $data);
                    $metadata = Help_Methods::getJsonData($metajson, $domain);
                    $letters = Help_Methods::createLetters();
                    $unique = Help_Methods::getUsedLetters($data);
                    $array_without_numbers = Help_Methods::checkforfigures($unique);
                    
                    if (empty($array_without_numbers)) {
                        _e('There are no entries for this file type!', 'rrze-remoter');
                    } else {
                        $dataSorted = Help_Methods::sortArray($data, $unique);
                        $data_new = Help_Methods::deleteMetaTxtEntries($dataSorted);
                        include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/glossary.php';
                    }
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'pagination':
                    ob_start();
                    $this->res = $data;
                    
                    $metajson = Help_Methods::getJsonFile($shortcodeValues, $data);
                    $metadata = Help_Methods::getJsonData($metajson, $domain);
                    
                    $number_of_chunks = (int) $this->shortcode_atts['itemsperpage'];
                    $dataFirstPage = $data;
                    $dataChunk = Help_Methods::deleteMetaTxtEntries($dataFirstPage);
                    $sortOrderby = ($orderby === 'size') ? 'size' : (($orderby === 'date') ? 'date' : 'name');
                    $sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
                    array_multisort(array_column($dataChunk, $sortOrderby), $sortOrder, $dataChunk);
                    
                    $data = array_chunk($dataChunk, $number_of_chunks);
                    
                    $pagecount = count($data);
                    if (empty($pagecount)) {
                        _e('There are no entries for this file type!', 'rrze-remoter');
                    } else {
                        $id = uniqid();
                        $itemscount = (isset($data[0]) ? count($data[0]) : '');
                        include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/table.php';
                    }
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'table':
                    ob_start();
                    $fileheader = $shortcodeValues['fileheader'];
                    $header = $shortcodeValues['showHeader'];
                    $order = $this->shortcode_atts['order'];
                    $orderby = $this->shortcode_atts['orderby'];
                    $alias = $shortcodeValues['alias'];
                    $metajson = Help_Methods::getJsonFile($shortcodeValues, $data);
                    $metadata = Help_Methods::getJsonData($metajson, $domain);
                    $sortOrderby = ($orderby === 'size') ? 'size' : (($orderby === 'date') ? 'date' : 'name');
                    $sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
                    $deletejson = $data;
                    $data = Help_Methods::deleteMetaTxtEntries($deletejson);
                    array_multisort(array_column($data, $sortOrderby), $sortOrder, $data);
                    include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/table_without_pagination.php';
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'imagetable':
                    ob_start();
                    include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/imagetable.php';
                    $content = ob_get_clean();
                    return $content;
                    break;
                default:
                    ob_start();
                    include_once $this->plugin_dir_path . 'RRZE/Remoter/Templates/list.php';
                    $orderby = $this->shortcode_atts['orderby'];
                    $content = ob_get_clean();
                    return $content;
            }
        } else {
            $error = $shortcodeValues['errormsg'];
            if ($error) {
                _e('No data could be found on the server!', 'rrze-remoter');
            } else {
                echo '';
            }
        }
    }

    public function rrze_remote_table_script_footer() {

        $arr = (!empty($this->res)) ? $this->res : '';
        $meta = (!empty($this->a)) ? $this->a : '';
        ?>

        <script>
            jQuery(document).ready(function ($) {

                var arr = <?php echo json_encode($arr); ?>;
                var meta = <?php echo json_encode($meta); ?>;

                $('a[href="#get_list"]').click(function () {
                    var link = $(this).attr('class');
                    var page = link.replace('page-', '');
                    var pagecount = $(this).attr('data-pagecount-value');
                    var chunk = $(this).attr('data-chunk');
                    var host = $(this).attr('data-host');
                    var index = $(this).attr('data-index');
                    var recursiv = $(this).attr('data-recursiv');
                    var filetype = $(this).attr('data-filetype');
                    var columns = $(this).attr('data-columns');
                    var link = $(this).attr('data-link');

                    $.ajax({
                        type: 'POST',
                        url: frontendajax.ajaxurl,
                        data: {
                            'action': 'rrze_remote_table_ajax_request',
                            'p': page,
                            'count': pagecount,
                            'index': index,
                            'recursiv': recursiv,
                            'filetype': filetype,
                            'chunk': chunk,
                            'host': host,
                            'columns': columns,
                            'link': link,
                            'arr': arr,
                            'meta': meta
                        },
                        success: function (data) {
                            $("#result").html(data);
                            //console.log(data);
                        },
                        error: function (errorThrown) {
                            window.alert(errorThrown);
                        }
                    });
                });

            });
        </script>
    <?php
    }

    public function rrze_remote_table_ajax_request() {

        if (isset($_REQUEST)) {

            $meta = $_REQUEST['meta'];

            $dataArray = $_REQUEST['arr'];

            $number_of_chunks = $_REQUEST['chunk'];

            $host = $_REQUEST['host'];

            $link = $_REQUEST['link'];


            foreach ($dataArray as $key => $value) {
                if ($value['name'] === '.meta.json') {
                    $i = 1;
                    unset($dataArray[$key]);
                    $dataChunk = array_values($dataArray);
                }
            }

            $data = array_chunk($i == 1 ? $dataChunk : $dataArray, $number_of_chunks);

            if (null !== $_REQUEST['p']) {
                if ($_REQUEST['p'] > $_REQUEST['count']) {
                    die('<span style="color:#FF0000">Error: Page Does Not Exist</span>');
                }
                $i = $_REQUEST['p'] - 1;
            } else {
                $i = 0;
            }

            $columns = explode(",", $_REQUEST['columns']);

            $id = uniqid();

            $t = '<table>';
            $t .= '<tr>';

            foreach ($columns as $key => $column) {

                switch ($column) {
                    case 'size':
                        $t .= '<th>' . __('File size', 'rrze-remoter') . '</th>';
                        break;
                    case 'type':
                        $t .= '<th>' . __('Type of file', 'rrze-remoter') . '</th>';
                        break;
                    case 'download':
                        $t .= '<th>' . __('Download', 'rrze-remoter') . '</th>';
                        break;
                    case 'directory':
                        $t .= '<th>' . __('Directory name', 'rrze-remoter') . '</th>';
                        break;
                    case 'name':
                        $t .= '<th>' . __('Filename', 'rrze-remoter') . '</th>';
                        break;
                    case 'date':
                        $t .= '<th>' . __('Creation date', 'rrze-remoter') . '</th>';
                        break;
                }
            }

            $t .= '</tr>';

            foreach ($data[$i] as $key => $value) {

                $t .= '</tr>';

                foreach ($columns as $key => $column) {

                    /* $dir = pathinfo($value['path']);
                      $titel = explode("/", $dir['dirname']);
                      $folder = $titel[count($titel)-1]; */

                    switch ($column) {
                        case 'size':
                            $t .= '<td>' . Help_Methods::formatSize($value['size']) . '</td>';
                            break;
                        case 'type':
                            $extension = $value['extension'];
                            if ($extension == 'pdf') {
                                $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                            } elseif ($extension == 'pptx' || $extension == 'ppt') {
                                $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>';
                            } elseif ($extension == 'docx' || $extension == 'doc') {
                                $t .= '<td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>';
                            } elseif ($extension == 'xlsx' || $extension == 'xls') {
                                $t .= '<td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>';
                            } elseif ($extension == 'mpg' || $extension == 'mpeg' || $extension == 'mp4' || $extension == 'm4v') {
                                $t .= '<td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>';
                            } else {
                                $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>';
                            }
                            break;
                        case 'download':
                            $t .= '<td align="center"><a href="https://' . $host . $value['dir'] . $value['name'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                            break;
                        case 'directory':
                            $t .= '<td>' . Help_Methods::getFolder($value['path']) . '</td>';
                            break;
                        case 'name':
                            $extension = $value['extension'];
                            if ($link) {
                                $path = $value['name'];
                                $imgFormats = Help_Methods::getImageFormats();

                                if (!in_array($extension, $imgFormats)) {
                                    $t .= '<td>';
                                    $t .= '<a href="https://' . $host . $value['dir'] . $value['name'] . '">';
                                    $t .= Help_Methods::getMetafileNames($path, $meta, $file = '');
                                    $t .= '</a>';
                                    $t .= '</td>';
                                } else {
                                    $t .= '<td>';
                                    $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="https://' . $host . $value['dir'] . $value['name'] . '">';
                                    $t .= Help_Methods::getMetafileNames($path, $meta, $file = '');
                                    $t .= '</a>';
                                    $t .= '</td>';
                                }
                            } else {

                                $t .= '<td>' . $value['name'] . '</td>';
                            }
                            break;
                        case 'date':
                            $date = date("d.m.Y", $value['date']);
                            $new_date = strtotime(' + 1 day', strtotime($date));
                            $t .= '<td>' . date("d.m.Y", $new_date) . '</td>';
                            break;
                    }
                }

                $t .= '</tr>';
            }

            $t .= '</table></div>';
            echo $t;
        }

        wp_die();
    }

    public function rrze_remote_glossary_script_footer() {

        $glossary_files = (!empty($this->glossary_array)) ? $this->glossary_array : '';
        $glossary_meta = (!empty($this->meta)) ? $this->meta : '';
        ?>
        <script>
            jQuery(document).ready(function ($) {

                var glossary = <?php echo json_encode($glossary_files) ?>;
                var meta = <?php echo json_encode($glossary_meta); ?>;

                $('a[href^="#letter-"]').click(function () {
                    var letter = $(this).attr('data-letter');
                    var host = $(this).attr('data-host');
                    var columns = $(this).attr('data-columns');
                    var link = $(this).attr('data-link');

                    $.ajax({
                        type: 'POST',
                        url: frontendajax.ajaxurl,
                        data: {
                            'action': 'rrze_remote_glossary_ajax_request',
                            'letter': letter,
                            'host': host,
                            'columns': columns,
                            'link': link,
                            'glossary': glossary,
                            'meta': meta
                        },
                        success: function (data) {
                            $("#glossary").html(data);
                            //alert(data);
                        },
                        error: function (errorThrown) {
                            window.alert(errorThrown);
                        }
                    });
                });

            });
        </script> 
        <?php
    }

    public function rrze_remote_glossary_ajax_request() {

        //print_r($_REQUEST);

        $id = uniqid();
        $link = $_REQUEST['link'];
        $host = $_REQUEST['host'];

        $meta = $_REQUEST['meta'];

        $filenames = array();

        foreach ($_REQUEST['glossary'] as $file) {
            $filenames[] = $file['name'];
        }

        array_multisort($filenames, SORT_ASC, $_REQUEST['glossary']);

        foreach ($_REQUEST['glossary'] as $key => $value) {
            if (substr($value['name'], 0, 1) != $_REQUEST['letter']) {
                unset($_REQUEST['glossary'][$key]);
            }
        }

        $data = array_values($_REQUEST['glossary']);

        $columns = explode(",", $_REQUEST['columns']);

        $t = '<table>';
        $t .= '<tr>';

        foreach ($columns as $key => $column) {

            switch ($column) {
                case 'size':
                    $t .= '<th>' . __('File size', 'rrze-remoter') . '</th>';
                    break;
                case 'type':
                    $t .= '<th>' . __('Type of file', 'rrze-remoter') . '</th>';
                    break;
                case 'download':
                    $t .= '<th>' . __('Download', 'rrze-remoter') . '</th>';
                    break;
                case 'directory':
                    $t .= '<th>' . __('Directory name', 'rrze-remoter') . '</th>';
                    break;
                case 'name':
                    $t .= '<th>' . __('Filename', 'rrze-remoter') . '</th>';
                    break;
                case 'date':
                    $t .= '<th>' . __('Creation date', 'rrze-remoter') . '</th>';
                    break;
            }
        }

        $t .= '</tr>';
        //echo $t;

        for ($i = 0; $i < sizeof($data); $i++) {

            $t .= '</tr>';

            foreach ($columns as $key => $column) {

                /* $dir = pathinfo($data[$i]['image']);
                  $titel = explode("/", $dir['dirname']);
                  $folder = $titel[count($titel)-1]; */
                $extension = $data[$i]['extension'];

                switch ($column) {
                    case 'size':
                        $t .= '<td>' . Help_Methods::formatSize($data[$i]['size']) . '</td>';
                        break;
                    case 'type':
                        if ($extension == 'pdf') {
                            $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                        } elseif ($extension == 'pptx' || $extension == 'ppt') {
                            $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>';
                        } elseif ($extension == 'docx' || $extension == 'doc') {
                            $t .= '<td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>';
                        } elseif ($extension == 'xlsx' || $extension == 'xls') {
                            $t .= '<td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>';
                        } elseif ($extension == 'mpg' || $extension == 'mpeg' || $extension == 'mp4' || $extension == 'm4v') {
                            $t .= '<td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>';
                        } else {
                            $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>';
                        }
                        break;
                    case 'download':
                        $t .= '<td align="center"><a href="https://' . $host . $data[$i]['dir'] . $data[$i]['name'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                        break;
                    case 'directory':
                        $t .= '<td>' . Help_Methods::getFolder($data[$i]['path']) . '</td>';
                        break;
                    case 'name':
                        if ($link) {
                            $path = $data[$i]['name'];
                            $imgFormats = Help_Methods::getImageFormats();

                            if (!in_array($extension, $imgFormats)) {
                                $t .= '<td>';
                                $t .= '<a href="https://' . $host . $data[$i]['dir'] . $data[$i]['name'] . '">';
                                $t .= Help_Methods::getMetafileNames($path, $meta, $file = '');
                                $t .= '</a>';
                                $t .= '</td>';
                            } else {
                                $t .= '<td>';
                                $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="https://' . $host . $data[$i]['dir'] . $data[$i]['name'] . '">';
                                $t .= Help_Methods::getMetafileNames($path, $meta, $file = '');
                                $t .= '</a>';
                                $t .= '</td>';
                            }
                        } else {

                            $t .= '<td>' . basename($data[$i]['path']) . '</td>';
                        }
                        break;
                    case 'date':
                        $t .= '<td>' . date("d.m.Y", $data[$i]['date']) . '</td>';
                        break;
                }
            }

            $t .= '</tr>';
        }

        $t .= '</table></div>';
        echo $t;

        wp_die();
    }

}
