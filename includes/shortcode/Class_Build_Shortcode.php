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
            'max'               => '3',
            'itemsperpage'      => '4',
            'filetype'          => '',
            'link'              => '0',
            'alias'             => '',
            'view'              => 'table',
            'orderby'           => 'name',
            'order'             => 'asc',
            'show'              => 'name,download',
            'showheader'        => '0',
            'filter'            => ''
        ), $atts );
        
        return $this->query_args($this->remote_server_shortcode);
       
    }
    
    public function query_args($args) {
        
        $this->remote_server_args = array(
            'post_type'         =>  'Remote-Server',
            'p'                 =>  $args['id'],
            'posts_per_page'    =>  1,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        return $this->show_results_as_list($this->remote_server_args);
    }
    
    public static function formatSize($bytes) {

        if ($bytes>= 1073741824) {
            $size = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
           $size = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 0) . ' KB';
        } elseif ($bytes > 1) {
            $size = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $size = '1 byte';
        } else {
            $size = '0 bytes';
        }
        
        return $size;
    }
  
    public static function getFolder($directory) {
 
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];
        
        return $folder;
    }
   
    public static function getHeaderData($columns) {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }
    
    public static function deleteMetaTxtEntries($meta) {
        foreach($meta as $key => $value) {
            if($value['name'] === '.meta.txt') {
                unset($meta[$key]);
            }
        }
        
        $data = array_values($meta);
        
        return $data;
    }
    
    public static function createLetters() {
        $letters = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    );
    
        return $letters;
    }
    
    public static function getUsedLetters($data) {
   
        foreach ($data as $file) {
            $files[] = substr($file['name'], 0, 1);
        }

        $unique = array_unique($files);

        sort($unique);

        return $unique;

    }
    
    public function checkforfigures($array) {
        
        foreach($array as $key => $value) {
            
            if(is_numeric($value) || ctype_lower($value) || substr($value, 0, 1) === '.') {
                unset($array[$key]);
            }       
        }
        
        $newindex = array_values($array);
        
        return $newindex;
    }
    
    public function sortArray($data, $unique) {
    
        $filenames = array(); 

        foreach ($data as $file) {
            $filenames[] = $file['name'];
        }

        array_multisort($filenames, SORT_ASC, $data);

        $array_without_numbers = self::checkforfigures($unique);

        foreach ( $data as $key => $value ) {
            if ( substr($value['name'], 0, 1) !=  $array_without_numbers[0] ) {
                unset( $data[$key]);
            }
        }

        $array_reindexed = array_values($data);

        return $array_reindexed;

    }
    
    public static function getMetafileNames($path, $store, $file) {
        
        $key = array_search($path , array_column($store, 'value'));

        if($key > 0 || $key === 0 && $file == '' && !empty($store)) {
            $name = $store[$key]['key'];
        } else {
            $name = $path;
        }
        
        return $name;
        
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
            'file'          => $this->remote_server_shortcode['file']
        );
        
        $the_query = new \WP_Query( $query_arguments);
        
        if ( $the_query->have_posts() ) {
            
            while ( $the_query->have_posts() ) {
                $the_query->the_post();

                $domain = get_post_meta($post->ID, 'domain', true); 
                $api_key = get_post_meta($post->ID, 'apikey', true); 

                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $domain, $api_key);
                
                $data = $this->remote_data;
                
                if($data){
                    $url = parse_url(get_post_meta($post->ID, 'url', true)); 
                    
                    $view = $shortcodeValues['view'];
                    $tableHeader = self::getHeaderData($shortcodeValues['showColumns']);
                    $meta = $data;
                    $meta_store = array();
                    array_multisort(array_column($meta, 'name'), SORT_ASC, $meta);
                    
                    switch ($view) {
                        case 'gallery':
                            include( plugin_dir_path( __DIR__ ) . '/templates/gallery.php');
                            break;
                        case 'glossary':
                            $id = uniqid();
                            $letters = self::createLetters();
                            $unique = self::getUsedLetters($data);
                            $array_without_numbers = self::checkforfigures($unique);
                            $dataSorted = self::sortArray($data, $unique);
                            $data_new = self::deleteMetaTxtEntries($data);
                            include( plugin_dir_path( __DIR__ ) . '/templates/glossary.php');
                            break;
                        case 'pagination':
                            date_default_timezone_set('Europe/Berlin');
                            $url = parse_url(get_post_meta($post->ID, 'url', true)); 
                            $number_of_chunks = (int)$this->remote_server_shortcode['itemsperpage'];
                            $dataFirstPage = $this->remote_data;
                            $dataChunk = self::deleteMetaTxtEntries($dataFirstPage);
                            $data = array_chunk($dataChunk, $number_of_chunks);
                            $pagecount = count($data);
                            $id = uniqid();
                            $itemscount = (isset($data[0]) ? count($data[0]) : '');
                            include( plugin_dir_path( __DIR__ ) . '/templates/table.php');
                            break;
                        case 'table':
                            ob_start();
                            $header = $shortcodeValues['showHeader'];
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
                    echo 'Überprüfen Sie Ihren Shortcode!';
                }
                   
            }
            
            wp_reset_postdata();
            
        } else {
                echo 'no posts found';
        }
    }
    
    public function rrze_remote_table_script_footer(){ 
        
        $arr = (isset($this->res)) ? $this->res : '';
        $meta = (isset($this->meta)) ? $this->meta : '';
        
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
                    unset($dataArray[$key]);
                    $dataChunk = array_values($dataArray);
                }
            }

            $data = array_chunk($dataChunk, $number_of_chunks);
            
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
                        $t .= '<th>Dateigröße</th>';
                        break;
                    case 'type':
                        $t .= '<th>Dateityp</th>';
                        break;
                    case 'download':
                        $t .= '<th>Download</th>';
                        break;
                    case 'folder':
                        $t .= '<th>Ordner</th>';
                        break;
                    case 'name':
                        $t .= '<th>Name</th>';
                        break;
                    case 'date':
                        $t .= '<th>Datum</th>';
                        break;   
                }
            }
            
            $t .= '</tr>';

            foreach ($data[$i] as $key => $value) {

                $t .= '</tr>';

                foreach($columns as $key => $column) {

                    $dir = pathinfo($value['image']);
                    $titel = explode("/", $dir['dirname']);
                    $folder = $titel[count($titel)-1];

                    switch($column) {
                        case 'size':
                            $t .= '<td>' . self::formatSize($value['size']) . '</td>';
                            break;
                        case 'type':
                            $extension = $value['extension'];
                            if($extension == 'pdf') {
                                $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                            }elseif($extension == 'pptx') {
                                $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                            }else{
                                $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                            }
                            break;
                        case 'download':
                            $t .= '<td><a href="http://' . $host . $value['image'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                            break;
                        case 'folder':
                            $t .= '<td>' . $folder . '</td>';
                            break;
                        case 'name':
                            if ($link) {
                            
                            $key = array_search(basename($value['path']), array_column($meta, 'value'));
                            
                                if($key === 0 || $key > 0) {
                                  $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $value['image'] . '">' . $meta[$key]['key'] . '</a></td>';
                                } else {
                                  $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $value['image'] . '">' . basename($value['path'])  . '</a></td>';
                                }
                            } else {
                                $t .= '<td>' . $value['path'] .'</td>';  
                            }
                            break;
                        case 'date':
                            $t .= '<td>' . date('j F Y', $value['change_time']) .'</td>';
                            break; 
                    }

                }

            $t .= '</tr>';
            }

        $t .= '</table></div>';
        echo $t;
    }
       
    die();
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
        $meta = $_REQUEST['meta'];
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
                    $t .= '<th>Dateigröße</th>';
                    break;
                case 'type':
                    $t .= '<th>Dateityp</th>';
                    break;
                case 'download':
                    $t .= '<th>Download</th>';
                    break;
                case 'folder':
                    $t .= '<th>Ordner</th>';
                    break;
                case 'name':
                    $t .= '<th>Name</th>';
                    break;
                case 'date':
                    $t .= '<th>Datum</th>';
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
                        $t .= '<td>' . self::formatSize($data[$i]['size']) . '</td>';
                        break;
                    case 'type':
                        $extension = $data[$i]['extension'];
                        if($extension == 'pdf') {
                            $t .= '<td align="center"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>';
                        }elseif($extension == 'pptx') {
                            $t .= '<td align="center"><i class=" file-powerpoint-o" aria-hidden="true"></i></td>'; 
                        }else{
                            $t .= '<td align="center"><i class="fa fa-file-image-o" aria-hidden="true"></i></td>'; 
                        }
                        break;
                    case 'download':
                        $t .= '<td><a href="http://' . $host . $data[$i]['image'] . '"  download><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a></td>';
                        break;
                    case 'folder':
                        $t .= '<td>' . $folder. '</td>';
                        break;
                    case 'name':
                        if ($link) {

                        $key = array_search(basename($data[$i]['path']), array_column($meta, 'value'));

                            if($key === 0 || $key > 0) {
                              $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $data[$i]['image']. '">' . $meta[$key]['key'] . '</a></td>';
                            } else {
                              $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $data[$i]['image'] . '">' . basename($data[$i]['path'])  . '</a></td>';
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