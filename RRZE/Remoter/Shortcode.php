<?php

namespace RRZE\Remoter;

use RRZE\Remoter\RemoteFiles;
use RRZE\Remoter\Parser;
use RRZE\Remoter\Helper;
use \WP_Error;

defined('ABSPATH') || exit;

class Shortcode
{
    protected $plugin_file;
    
    protected $plugin_dir_path;
    
    protected $shortcode_atts;
    
    protected $parser;
    
    protected $glossary_array;
    
    protected $meta;
    
    protected $res;
    
    protected $a;
    
    protected $fau_themes = [
        'FAU-Einrichtungen',
        'FAU-Natfak',
        'FAU-Philfak',
        'FAU-RWFak',
        'FAU-Techfak',
        'FAU-Medfak'
    ];

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_dir_path = plugin_dir_path($plugin_file);
        
        $this->parser = new Parser();
                
        add_shortcode('remoter', [$this, 'shortcode']);
        
        add_action('wp_ajax_rrze_remote_glossary_ajax_request', [$this, 'rrze_remote_glossary_ajax_request']);
        add_action('wp_ajax_nopriv_rrze_remote_glossary_ajax_request', [$this, 'rrze_remote_glossary_ajax_request']);

        
        
        add_action('wp_ajax_rrze_remote_table_ajax_request', [$this, 'rrze_remote_table_ajax_request']);
        add_action('wp_ajax_nopriv_rrze_remote_table_ajax_request', [$this, 'rrze_remote_table_ajax_request']);

        
    }

    public function shortcode($atts)
    {
        $this->shortcode_atts = shortcode_atts(
        [
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
        ],
            $atts
        );

        $content = $this->output();
        if (is_wp_error($content)) {
            return sprintf('[remote] %s', $content->get_error_message());
        }
                
        wp_enqueue_script('rrze-remoter-mainjs');
        wp_enqueue_script('rrze-remoter-scriptsjs');
        wp_enqueue_script('flexsliderjs');
        wp_enqueue_script('fancyboxjs');
            
        $stylesheet = get_stylesheet();
        
        if (!in_array($stylesheet, $this->fau_themes)) {
            wp_enqueue_style('rrze-remoter-rrze-theme-stylescss');
        } else {
            wp_enqueue_style('rrze-remoter-stylescss');
        }
                    
        add_action('wp_footer', [$this, 'rrze_remote_glossary_script_footer']);
        add_action('wp_footer', [$this, 'rrze_remote_table_script_footer']);
                
        return $content;        
    }

    private function output()
    {
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

        $apiurl = get_post_meta($remoter_post->ID, '_rrze_remoter_apiurl', true);
        $apihost = parse_url($apiurl, PHP_URL_HOST);
        
        $apikey = get_post_meta($remoter_post->ID, '_rrze_remoter_apikey', true);

        $data = RemoteFiles::getFiles($this->shortcode_atts, $apiurl, $apikey);
                
        if ($data) {            
            $view = $this->shortcode_atts['view'];
            $tableHeader = Helper::getHeaderData($shortcodeValues['showColumns']);
            $meta = $data;

            $meta_store = array();
            $order = $this->shortcode_atts['order'];
            $orderby = $this->shortcode_atts['orderby'];
            
            switch ($view) {
                case 'glossary':
                    ob_start();
                    $id = Helper::createHash(10);
                    $metajson = Helper::getJsonFile($this->shortcode_atts, $data);
                    $metadata = Helper::getJsonData($metajson, $apiurl);
                    $letters = Helper::createLetters();
                    $unique = Helper::getUsedLetters($data);
                    $array_without_numbers = Helper::checkforfigures($unique);
                    
                    if (empty($array_without_numbers)) {
                        _e('There are no entries for this file type!', 'rrze-remoter');
                    } else {
                        $dataSorted = Helper::sortArray($data, $unique);
                        $data_new = Helper::deleteMetaTxtEntries($dataSorted);
                        include $this->plugin_dir_path . 'RRZE/Remoter/Templates/glossary.php';
                    }
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'pagination':
                    ob_start();
                    $this->res = $data;
                    
                    $metajson = Helper::getJsonFile($this->shortcode_atts, $data);
                    $metadata = Helper::getJsonData($metajson, $apiurl);
                    
                    $number_of_chunks = (int) $this->shortcode_atts['itemsperpage'];
                    $dataFirstPage = $data;
                    $dataChunk = Helper::deleteMetaTxtEntries($dataFirstPage);
                    $sortOrderby = ($orderby === 'size') ? 'size' : (($orderby === 'date') ? 'date' : 'name');
                    $sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
                    array_multisort(array_column($dataChunk, $sortOrderby), $sortOrder, $dataChunk);
                    
                    $data = array_chunk($dataChunk, $number_of_chunks);
                    
                    $pagecount = count($data);
                    if (empty($pagecount)) {
                        _e('There are no entries for this file type!', 'rrze-remoter');
                    } else {
                        $id = Helper::createHash(10);
                        $itemscount = (isset($data[0]) ? count($data[0]) : '');
                        include $this->plugin_dir_path . 'RRZE/Remoter/Templates/pagination.php';
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
                    $metajson = Helper::getJsonFile($this->shortcode_atts, $data);
                    $metadata = Helper::getJsonData($metajson, $apiurl);
                    
                    $sortOrderby = ($orderby === 'size') ? 'size' : (($orderby === 'date') ? 'date' : 'name');
                    $sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
                    
                    $deletejson = $data;
                    $data = Helper::deleteMetaTxtEntries($deletejson);
                    
                    array_multisort(array_column($data, $sortOrderby), $sortOrder, $data);
                    include $this->plugin_dir_path . 'RRZE/Remoter/Templates/table.php';
                    $content = ob_get_clean();
                    return $content;
                    break;
                case 'gallery':
                    return $this->galleryView($data, $apiurl);
                    break;
                case 'imagetable':
                    return $this->imagetableView($data, $apiurl);
                    break;
                default:
                    return $this->listView($data, $apiurl);
            }
                        
        } else {
            return new WP_Error('no_remote_data_found', __('No data could be found on the server!', 'rrze-remoter'));
        }
    }
        
    protected function listView($remote_data, $apiurl) {
        $data = [];
        
        $template = $this->plugin_dir_path . 'RRZE/Remoter/Templates/list.html';
        
        $sortOrderby = $this->shortcode_atts['orderby'] == 'size' ? 'size' : 'name';
        $sortOrder = $this->shortcode_atts['order'] == 'asc' ? SORT_ASC : SORT_DESC;
        array_multisort(array_column($data, $sortOrderby), $sortOrder , $remote_data);
                
        foreach ($remote_data as $key => $value) {
            $ext = $value['extension'];
            if($ext == 'pdf') { 
                $icon ='fa-file-pdf-o';
            } elseif ($ext == 'pptx' || $ext =='ppt') { 
                $icon ='fa-file-powerpoint-o';
            } elseif ($ext == 'docx' || $ext =='doc' ) { 
                $icon ='fa fa-file-word-o';
            } elseif ($ext == 'xlsx' || $ext =='xls') { 
                $icon ='fa-file-excel-o';
            } elseif ($ext == 'mpg' || $ext =='mpeg'|| $ext =='mp4' || $ext =='m4v') { 
                $icon = 'fa-file-movie-o';
            } else { 
                $icon ='fa-file-image-o';
            }
            
            $data['files'][$key]['icon'] = $icon;
            $data['files'][$key]['url'] = $apiurl . $value['dir'] . $value['name'] . '';;
            $data['files'][$key]['name'] = Helper::replaceCharacterList(Helper::changeUmlautsList($value['name']));
            $data['files'][$key]['size'] = Helper::formatSize($value['size']);
            
        }
        
        return $this->parser->template($template, $data);
    }
    
    protected function galleryView($remote_data, $apiurl) {
        $data = [];
        
        $template = $this->plugin_dir_path . 'RRZE/Remoter/Templates/gallery.html';
        
        $stylesheet = get_stylesheet();
        
        if (in_array($stylesheet, $this->fau_themes)) {
            global $usejslibs;
            $usejslibs['flexslider'] = true;
        }
        
        $data['id'] = Helper::createHash(10);
        
        $gallerytitle = $this->shortcode_atts['gallerytitle'];
        $gallerydescription = $this->shortcode_atts['gallerydescription'];        
        
        foreach ($remote_data as $key => $value) {

            $url = $apiurl . $value['dir'] . $value['name'] . '';
            $timeout = 10;
            $tmpfile = Helper::download_url($url, $timeout);
            if (is_wp_error($tmpfile)) {
                continue;
            }
            $imginfo = getimagesize($tmpfile, $info);
            unlink($tmpfile); // important!
            
            if (!$imginfo[0] || !$imginfo[1]) {
                continue;
            }

            $iptcdata = isset($info["APP13"]) ? iptcparse($info["APP13"]) : null;
            
            $title = $iptcdata && isset($iptcdata["2#120"][0]) ? $iptcdata["2#120"][0] : '';
            $desc = $iptcdata && isset($iptcdata["2#105"][0]) ? $iptcdata["2#105"][0] : '';
            
            if ($gallerytitle && $gallerydescription) {
                $description = $desc . '<br/>' . $title;
            } elseif ($gallerytitle && !$gallerdescription) {
                $description = $title;
            } elseif (!$gallerytitle && $gallerydescription) {
                $description = $desc;
            } else {
                $description = '';
            }
            
            $data['images'][$key]['id'] = $data['id'];
            $data['images'][$key]['url'] = $url;
            $data['images'][$key]['title'] = $title;
            $data['images'][$key]['description'] = $description;
            $data['images'][$key]['Enlarge'] = __('Enlarge', 'rrze-remoter');
        }
        
        return $this->parser->template($template, $data);
    }
    
    protected function imagetableView($remote_data, $apiurl) {
        $data = [];
        
        $template = $this->plugin_dir_path . 'RRZE/Remoter/Templates/imagetable.html';

        $data['id'] = Helper::createHash(10);
        
        foreach ($remote_data as $key => $value) {
            $url = $apiurl . $value['dir'] . $value['name'] . '';
            $timeout = 10;
            $tmpfile = Helper::download_url($url, $timeout);
            if (is_wp_error($tmpfile)) {
                continue;
            }
            $imginfo = getimagesize($tmpfile, $info);
            unlink($tmpfile); // important!
            
            if (!$imginfo[0] || !$imginfo[1]) {
                continue;
            }

            $iptcdata = isset($info["APP13"]) ? iptcparse($info["APP13"]) : null;
            
            $data['images'][$key]['id'] = $data['id'];
            $data['images'][$key]['url'] = $url;                        
        }
        
        return $this->parser->template($template, $data);
    }
    
    public function rrze_remote_table_script_footer()
    {
        $arr = (!empty($this->res)) ? $this->res : '';
        $meta = (!empty($this->a)) ? $this->a : ''; ?>

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

    public function rrze_remote_table_ajax_request()
    {
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

            $id = Helper::createHash(10);

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
                            $t .= '<td>' . Helper::formatSize($value['size']) . '</td>';
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
                            $t .= '<td>' . Helper::getFolder($value['path']) . '</td>';
                            break;
                        case 'name':
                            $extension = $value['extension'];
                            if ($link) {
                                $path = $value['name'];
                                $imgFormats = Helper::getImageFormats();

                                if (!in_array($extension, $imgFormats)) {
                                    $t .= '<td>';
                                    $t .= '<a href="https://' . $host . $value['dir'] . $value['name'] . '">';
                                    $t .= Helper::getMetafileNames($path, $meta, $file = '');
                                    $t .= '</a>';
                                    $t .= '</td>';
                                } else {
                                    $t .= '<td>';
                                    $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="https://' . $host . $value['dir'] . $value['name'] . '">';
                                    $t .= Helper::getMetafileNames($path, $meta, $file = '');
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

    public function rrze_remote_glossary_script_footer()
    {
        $glossary_files = (!empty($this->glossary_array)) ? $this->glossary_array : '';
        $glossary_meta = (!empty($this->meta)) ? $this->meta : ''; ?>
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

    public function rrze_remote_glossary_ajax_request()
    {
        $id = Helper::createHash(10);
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
                        $t .= '<td>' . Helper::formatSize($data[$i]['size']) . '</td>';
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
                        $t .= '<td>' . Helper::getFolder($data[$i]['path']) . '</td>';
                        break;
                    case 'name':
                        if ($link) {
                            $path = $data[$i]['name'];
                            $imgFormats = Helper::getImageFormats();

                            if (!in_array($extension, $imgFormats)) {
                                $t .= '<td>';
                                $t .= '<a href="https://' . $host . $data[$i]['dir'] . $data[$i]['name'] . '">';
                                $t .= Helper::getMetafileNames($path, $meta, $file = '');
                                $t .= '</a>';
                                $t .= '</td>';
                            } else {
                                $t .= '<td>';
                                $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="https://' . $host . $data[$i]['dir'] . $data[$i]['name'] . '">';
                                $t .= Helper::getMetafileNames($path, $meta, $file = '');
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
        exit;
    }
}
