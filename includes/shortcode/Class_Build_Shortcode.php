<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Build_Shortcode {
    
    public function __construct() {
      
        add_action( 'wp_ajax_rrze_remote_table_ajax_request', array($this, 'rrze_remote_table_ajax_request' ));
        add_action( 'wp_ajax_nopriv_rrze_remote_table_ajax_request', array($this, 'rrze_remote_table_ajax_request' ));
        add_shortcode('remoter', array($this, 'shortcode')); 
        add_action( 'wp_footer', array($this,'rrze_remote_table_script_footer'));
        
        add_action( 'wp_ajax_rrze_remote_glossary_ajax_request', array($this, 'rrze_remote_glossary_ajax_request' ));
        add_action( 'wp_ajax_nopriv_rrze_remote_glossary_ajax_request', array($this, 'rrze_remote_glossary_ajax_request' ));
        
        add_action( 'wp_footer', array($this,'rrze_remote_glossary_script_footer'));
        
    }
    
    public function shortcode($atts) {
        
        $this->remote_server_shortcode = shortcode_atts( array(
            'id'                => '',
            'file'              => '',
            'index'             => '',
            'recursiv'          => '1',
            'itemsperpage'      => '5',
            'filetype'          => 'pdf',
            'link'              => '0',
            'alias'             => '',
            'view'              => 'table',
            'orderby'           => 'name',
            'order'             => 'asc',
            'show'              => 'name,download',
            'showheader'        => '0',
            'filter'            => '',
            'showmetainfo'      => '1'
        ), $atts );
        
        return $this->query_args($this->remote_server_shortcode);
       
    }
    
    public function query_args($args) {
        
        $this->remote_server_args = array(
            'post_type'         =>  'remoter',
            'p'                 =>  $args['id'],
            'posts_per_page'    =>  1,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        return $this->show_results_as_list($this->remote_server_args);
    }
  
    public function show_results_as_list($query_arguments) {
        
        global $post;
        
        $shortcodeValues = array(
            'fileIndex'     => $this->remote_server_shortcode['index'],
            'view'          => $this->remote_server_shortcode['view'],
            'recursive'     => $this->remote_server_shortcode['recursiv'],
            'filetype'      => $this->remote_server_shortcode['filetype'],
            'showColumns'   => $this->remote_server_shortcode['show'],
            'link'          => $this->remote_server_shortcode['link'],
            'showHeader'    => $this->remote_server_shortcode['showheader'],
            'file'          => $this->remote_server_shortcode['file'],
            'showInfo'      => $this->remote_server_shortcode['showmetainfo']
        );
        
        $the_query = new \WP_Query( $query_arguments);
        
        //echo '<pre>';
        //print_r($the_query);
        //echo '</pre>';
        
        if ( $the_query->have_posts() ) {
            
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                
                //print_r($the_query);

                $domain = get_post_meta($post->ID, 'domain', true); 
                $api_key = get_post_meta($post->ID, 'apikey', true); 
                
                //echo '<pre>';
               // print_r($this->remote_server_shortcode);
                //echo '</pre>';

                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $domain, $api_key);
                
               
                
                $data = $this->remote_data;
                
                //echo 'Response:<pre>';
                //print_r( $data );
                //echo '</pre>';
                
                if($data){
                    //$url = parse_url(get_post_meta($post->ID, 'url', true)); 
                    //print_r( $domain);
                    
                    $view = $shortcodeValues['view'];
                    $tableHeader = Class_Help_Methods::getHeaderData($shortcodeValues['showColumns']);
                    $meta = $data;
                    
                    $meta_store = array();
                    array_multisort(array_column($meta, 'name'), SORT_ASC, $meta);
                    date_default_timezone_set('Europe/Berlin');
                    $order = $this->remote_server_shortcode['order'];
                    $orderby = $this->remote_server_shortcode['orderby'];
                    switch ($view) {
                        case 'gallery':
                            include( plugin_dir_path( __DIR__ ) . '/templates/gallery.php');
                            break;
                        case 'glossary':
                            echo 'test';
                            $id = uniqid();
                            $letters = Class_Help_Methods::createLetters();
                            $unique = Class_Help_Methods::getUsedLetters($data);
                            $array_without_numbers = Class_Help_Methods::checkforfigures($unique);
                            if(empty($array_without_numbers)) {
                                echo 'Zu diesem Dateityp gibt es keine Einträge!';
                            } else {
                                $dataSorted = Class_Help_Methods::sortArray($data, $unique);
                                $data_new = Class_Help_Methods::deleteMetaTxtEntries($dataSorted);
                                include( plugin_dir_path( __DIR__ ) . '/templates/glossary.php');
                            }
                            break;
                        case 'pagination':
                            //date_default_timezone_set('Europe/Berlin');
                            $url = parse_url(get_post_meta($post->ID, 'url', true)); 
                            $number_of_chunks = (int)$this->remote_server_shortcode['itemsperpage'];
                            $dataFirstPage = $this->remote_data;
                            $dataChunk = Class_Help_Methods::deleteMetaTxtEntries($dataFirstPage);
                            $sortOrderby = ($orderby === 'size') ? 'size' : (($orderby === 'date') ? 'change_time' : 'name');
                            $sortOrder = ($order === 'asc' ? SORT_ASC : SORT_DESC);
                            array_multisort(array_column($dataChunk, $sortOrderby), $sortOrder , $dataChunk);
                            $data = array_chunk($dataChunk, $number_of_chunks);
                            $pagecount = count($data);
                            if(empty($pagecount)) {
                                echo 'Zu diesem Dateityp gibt es keine Einträge!';
                            } else {
                            $id = uniqid();
                            $itemscount = (isset($data[0]) ? count($data[0]) : '');
                            include( plugin_dir_path( __DIR__ ) . '/templates/table.php');
                            }
                            break;
                        case 'table':
                            ob_start();
                            $header = $shortcodeValues['showHeader'];
                            $order = $this->remote_server_shortcode['order'];
                            $orderby = $this->remote_server_shortcode['orderby'];
                            include( plugin_dir_path( __DIR__ ) . '/templates/table_without_pagination.php');
                            $content = ob_get_clean();
                            return $content;
                            break;
                        case 'imagetable':
                            include( plugin_dir_path( __DIR__ ) . '/templates/imagetable.php');
                            break;
                        default:
                            include( plugin_dir_path( __DIR__ ) . '/templates/list.php');
                    }
                    
                } else {
                    echo 'Es konnten keine Daten auf dem Server gefunden werden!';
                }
                   
            }
            
            wp_reset_postdata();
            
        } else {
                echo 'no posts found';
        }
    }
    
    public function rrze_remote_table_script_footer(){ 
        
        $arr = (isset($this->res)) ? $this->res : '';
        $meta = (isset($this->a)) ? $this->a : '';
        
        ?>
  
        <script>
        jQuery(document).ready(function($) {

            var arr = <?php echo json_encode($arr); ?>;
            var meta = <?php echo json_encode($meta); ?>;

            $('a[href="#get_list"]').click(function(){
                var link = $(this).attr('class');
                var page = link.replace('page-', '');
                var pagecount = $(this).attr('data-pagecount-value');
                var chunk = $(this).attr('data-chunk');
                var host = $(this).attr('data-host');
                var index = $(this).attr('data-index');
                var recursiv = $(this).attr('data-recursiv');
                var filetype = $(this).attr('data-filetype');
                var columns = $(this).attr('data-columns');
                var link    = $(this).attr('data-link');
                
                $.ajax({
                    type: 'POST',
                    url: frontendajax.ajaxurl,
                    data: {
                        'action'    :'rrze_remote_table_ajax_request',
                        'p'         : page,
                        'count'     : pagecount,
                        'index'     : index,
                        'recursiv'  : recursiv,
                        'filetype'  : filetype,
                        'chunk'     : chunk,
                        'host'      : host,
                        'columns'   : columns,
                        'link'      : link,
                        'arr'       : arr,
                        'meta'      : meta
                    },
                    success:function(data) {
                        $( "#result" ).html(data);
                        //console.log(data);
                    },  
                    error: function(errorThrown){
                        window.alert(errorThrown);
                    }
                }); 
            });

        });
        </script>
        <?php } 
 

    
    
    public function rrze_remote_table_ajax_request() {
        
        if ( isset($_REQUEST) ) {
            
            $meta       = $_REQUEST['meta'];
        
            $dataArray  = $_REQUEST['arr'];
            
            $number_of_chunks = $_REQUEST['chunk'];
            
            $host = $_REQUEST['host'];
            
            $link = $_REQUEST['link'];
            
            
            foreach($dataArray as $key => $value) {
                if($value['name'] === '.meta.txt') {
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
            }
            else {
                $i = 0;
            }
            
            $columns = explode(",", $_REQUEST['columns']);
            
            $id = uniqid();

            $t  = '<table>';
            $t .= '<tr>';

            foreach($columns as $key => $column) {

                switch($column) {
                    case 'size':
                        $t .= '<th>' . __('Dateigröße', 'rrze-remoter') . '</th>';
                    break;
                    case 'type':
                        $t .= '<th>' . __('Dateityp', 'rrze-remoter') . '</th>';
                        break;
                    case 'download':
                        $t .= '<th>' . __('Herunterladen', 'rrze-remoter') . '</th>';
                        break;
                    case 'directory':
                        $t .= '<th>' . __('Verzeichnisname', 'rrze-remoter') . '</th>';
                        break;
                    case 'name':
                        $t .= '<th>' . __('Dateiname', 'rrze-remoter') . '</th>';
                        break;
                    case 'date':
                        $t .= '<th>' . __('Erstellungsdatum', 'rrze-remoter') . '</th>';
                        break;   
                    
                }
            }
            
            $t .= '</tr>';

            foreach ($data[$i] as $key => $value) {

                $t .= '</tr>';

                foreach($columns as $key => $column) {

                    /*$dir = pathinfo($value['path']);
                    $titel = explode("/", $dir['dirname']);
                    $folder = $titel[count($titel)-1];*/

                    switch($column) {
                        case 'size':
                            $t .= '<td>' . Class_Help_Methods::formatSize($value['size']) . '</td>';
                            break;
                        case 'type':
                            $extension = $value['extension'];
                            if ($extension == 'pdf') {
                                $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                            } elseif ($extension == 'pptx' || $extension =='ppt') {
                                $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                            } elseif ($extension == 'docx' || $extension =='doc') {
                                $t .= '<td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>'; 
                            } elseif ($extension == 'xlsx' || $extension =='xls') {
                                $t .= '<td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>'; 
                            } elseif ($extension == 'mpg' || $extension =='mpeg'|| $extension =='mp4' || $extension =='m4v') {
                                $t .= '<td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>'; 
                            } else {
                                $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                            }
                            break;
                        case 'download':
                            $t .= '<td align="center"><a href="http://' . $host . $value['dir'] . $value['name'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                            break;
                        case 'directory':
                            $t .= '<td>' . Class_Help_Methods::getFolder($value['path'])  . '</td>';
                            break;
                        case 'name':
                            $extension = $value['extension'];
                            if ($link) { 
                                $path = $value['name'];
                                $imgFormats = Class_Help_Methods::getImageFormats();   
                            
                                if (!in_array($extension, $imgFormats)) {
                                    $t .= '<td>';
                                    $t .= '<a href="http://' . $host . $value['dir'] . $value['name'] . '">';
                                    $t .= Class_Help_Methods::getMetafileNames($path, $meta, $file='');
                                    $t .= '</a>';
                                    $t .= '</td>'; 

                                } else {
                                    $t .= '<td>';
                                    $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $value['dir'] . $value['name'] .'">';
                                    $t .= Class_Help_Methods::getMetafileNames($path, $meta, $file='');
                                    $t .= '</a>';
                                    $t .= '</td>';  
                                }

                            } else { 

                            $t .= '<td>' . $data[$i]['name'] .'</td>';  

                            }
                            break;
                        case 'date':
                            $t .= '<td>' . $value['date'] .'</td>';
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
        
        $glossary_files = (isset($this->glossary_array) ? $this->glossary_array : '') ;
        $glossary_meta = (isset($this->meta)) ? $this->meta : '';
        //print_r($glossary_meta);
	 
         ?>
         <script>
        jQuery(document).ready(function($) {
            
            var glossary = <?php echo json_encode($glossary_files); ?>;
            var meta = <?php echo json_encode($glossary_meta); ?>;

            $('a[href^="#letter-"]').click(function(){
                var letter = $(this).attr('data-letter');
                var host = $(this).attr('data-host');
                var columns = $(this).attr('data-columns');
                var link    = $(this).attr('data-link');
                
                $.ajax({
                    type: 'POST',
                    url: frontendajax.ajaxurl,
                    data: {
                        'action'    :'rrze_remote_glossary_ajax_request',
                        'letter'    : letter,
                        'host'      : host,
                        'columns'   : columns,
                        'link'      : link,
                        'glossary'  : glossary,
                        'meta'      : meta
                    },
                    success:function(data) {
                        $("#glossary").html(data);
                    },  
                    error: function(errorThrown){
                        window.alert(errorThrown);
                    }
                }); 
            });

        });
        </script> 
        <?php
    }
    
    public function rrze_remote_glossary_ajax_request() {
        
        $id = uniqid();
        $link = $_REQUEST['link'];
        $host = $_REQUEST['host'];
        
        //echo '<pre>';
        $meta = isset($_REQUEST['meta']);
        //echo '</pre>';
        
        $filenames = array(); 

        foreach ($_REQUEST['glossary'] as $file) {
            $filenames[] = $file['name'];
        }
        
        array_multisort($filenames, SORT_ASC, $_REQUEST['glossary']);
        
        foreach ( $_REQUEST['glossary'] as $key => $value ) {
            if ( substr($value['name'], 0, 1) != $_REQUEST['letter']) {
                unset( $_REQUEST['glossary'][$key]);
            }
        }

        $data = array_values($_REQUEST['glossary']);
        
        $columns = explode(",", $_REQUEST['columns']);

        $t  = '<table>';
        $t .= '<tr>';

        foreach($columns as $key => $column) {

            switch($column) {
                case 'size':
                    $t .= '<th>' . __('Dateigröße', 'rrze-remoter') . '</th>';
                    break;
                case 'type':
                    $t .= '<th>' . __('Dateityp', 'rrze-remoter') . '</th>';
                    break;
                case 'download':
                    $t .= '<th>' . __('Herunterladen', 'rrze-remoter') . '</th>';
                    break;
                case 'directory':
                    $t .= '<th>' . __('Verzeichnisname', 'rrze-remoter') . '</th>';
                    break;
                case 'name':
                    $t .= '<th>' . __('Dateiname', 'rrze-remoter') . '</th>';
                    break;
                case 'date':
                    $t .= '<th>' . __('Erstellungsdatum', 'rrze-remoter') . '</th>';
                    break;   
            }
        }

        $t .= '</tr>';
        //echo $t;
        
          for($i = 0; $i < sizeof($data); $i++) {
            
            $t .= '</tr>';
        
            foreach($columns as $key => $column) {
                
                $dir = pathinfo($data[$i]['image']);
                $titel = explode("/", $dir['dirname']);
                $folder = $titel[count($titel)-1];
                
                switch($column) {
                    case 'size':
                        $t .= '<td>' . Class_Help_Methods::formatSize($data[$i]['size']) . '</td>';
                        break;
                    case 'type':
                        $extension = $data[$i]['extension'];
                        if ($extension == 'pdf') {
                            $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                        } elseif ($extension == 'pptx' || $extension =='ppt') {
                            $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                        } elseif ($extension == 'docx' || $extension =='doc') {
                            $t .= '<td align="center"><i class="fa fa-file-word-o" aria-hidden="true"></i></td>'; 
                        } elseif ($extension == 'xlsx' || $extension =='xls') {
                            $t .= '<td align="center"><i class="fa fa-file-excel-o" aria-hidden="true"></i></td>'; 
                        } elseif ($extension == 'mpg' || $extension =='mpeg'|| $extension =='mp4' || $extension =='m4v') {
                            $t .= '<td align="center"><i class="fa fa-file-movie-o" aria-hidden="true"></i></td>'; 
                        } else {
                            $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                        }
                        break;
                    case 'download':
                        $t .= '<td align="center"><a href="http://' . $host . $data[$i]['image'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                        break;
                    case 'directory':
                        $t .= '<td>' . $folder. '</td>';
                        break;
                    case 'name':
                         if ($link) { 
                            $path = basename($data[$i]['path']);
                            $imgFormats = Class_Help_Methods::getImageFormats();   
                            
                            if (!in_array($extension, $imgFormats)) {
                                $t .= '<td>';
                                $t .= '<a href="http://' . $host . $data[$i]['image'] . '">';
                                $t .= Class_Help_Methods::getMetafileNames($path, $meta, $file='');
                                $t .= '</a>';
                                $t .= '</td>'; 

                            } else {
                                $t .= '<td>';
                                $t .= '<a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $data[$i]['image'] . '">';
                                $t .= Class_Help_Methods::getMetafileNames($path, $meta, $file='');
                                $t .= '</a>';
                                $t .= '</td>';  
                            }

                        } else { 

                        $t .= '<td>' . basename($data[$i]['path']) .'</td>';  

                        }
                        break;
                    case 'date':
                        $t .= '<td>' . date('j F Y', $data[$i]['change_time']) .'</td>';
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