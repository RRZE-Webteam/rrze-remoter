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
            'language'          => '0',
            'view'              => 'table',
            'orderby'           => 'name',
            'order'             => 'asc',
            'show'              => 'name,download',
            'showheader'        => '0'
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
    
    public function show_results_as_list($query_arguments) {
        
        global $post;
        
        $shortcode_values = array();
        
        $the_query = new \WP_Query( $query_arguments);
        
        if ( $the_query->have_posts() ) {
            
            while ( $the_query->have_posts() ) {
                $the_query->the_post();

                $domain = get_post_meta($post->ID, 'domain', true); 
                $api_key = get_post_meta($post->ID, 'apikey', true); 

                $file_index = $this->remote_server_shortcode['index'];
                $view = $this->remote_server_shortcode['view'];
                $recursiv = $this->remote_server_shortcode['recursiv'];
                $filetype = $this->remote_server_shortcode['filetype'];
                $show_columns = $this->remote_server_shortcode['show'];
                $link = $this->remote_server_shortcode['link'];
                $language = $this->remote_server_shortcode['language'];
                $showheader = $this->remote_server_shortcode['showheader'];
                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $domain, $api_key);
                
                $data = $this->remote_data;
                
                if ($language) {
                    $data = $this->getEnglischContent($data, $language);
                } else {
                    $data = $this->remote_data;
                    
                }
                
                if($data){

                    $url = parse_url(get_post_meta($post->ID, 'url', true)); 

                    if ($view == 'gallery') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/gallery.php');
                    } elseif($view == 'list') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/list.php');
                    } elseif($view == 'pagination') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/table.php');
                    } elseif($view == 'table') {
                        ob_start();
                        $header = $showheader;
                        include( plugin_dir_path( __DIR__ ) . '/templates/table_without_pagination.php');
                        $content = ob_get_clean();
                        return $content;
                    } elseif($view == 'imagetable') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/imagetable.php');
                    } else {
                        include( plugin_dir_path( __DIR__ ) . '/templates/glossary.php');
                    }
                } else {
                    echo 'Sie sind nicht berechtigt Daten abzurufen';
                }
                   
            }
         
            wp_reset_postdata();
        } else {
                echo 'no posts found';
        }
    }
    
    public function getEnglischContent($data, $language) {
        
        $items = array();
       
        foreach($data as $key => $value) {
           
            if ( preg_match('/englisch/i', $value['basename'], $matches)) {
                $items[$key] = $matches[0];
            } else {
                echo '';
            }
        }
        
        if($language == 0) {
            return $data;
        } elseif($language == 1) {
            $only_english = array_intersect_key($data, $items);
            $new = array_values($only_english);
            return $new;
        } else {
            $without_english = array_diff_key($data, $items);
            $new = array_values($without_english);
            return $new;
        }
    }
    
    public function rrze_remote_table_script_footer(){ 
        
        $arr = (isset($this->res)) ? $this->res : '';
        
        ?>
  
        <script>
        jQuery(document).ready(function($) {

            var arr = <?php echo json_encode($arr); ?>;

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
                        'arr'       : arr
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
            
            $number_of_chunks = $_REQUEST['chunk'];
            
            $host = $_REQUEST['host'];
            
            $link = $_REQUEST['link'];

            $data = array_chunk($_REQUEST['arr'], $number_of_chunks);
            
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

                    $bytes = $value['size'];

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

                    switch($column) {
                        case 'size':
                            $t .= '<td>' . $size . '</td>';
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
                              $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $value['image'] . '">' .  basename($value['path']) . '</a></td>';    
                            } else {
                              $t .= '<td>' . basename($value['path']) .'</td>';  
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
	 
         ?>
         <script>
        jQuery(document).ready(function($) {
            
            var glossary = <?php echo json_encode($glossary_files); ?>;

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
                        'glossary'  : glossary
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
                
                $bytes = $data[$i]['size'];

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

                switch($column) {
                    case 'size':
                        $t .= '<td>' . $size . '</td>';
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
                          $t .= '<td><a class="lightbox" rel="lightbox-' . $id . '" href="http://' . $host . $data[$i]['image'] . '">' .  basename($data[$i]['path']) . '</a></td>';    
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