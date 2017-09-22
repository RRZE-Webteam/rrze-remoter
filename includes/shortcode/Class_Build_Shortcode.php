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
            'server_id' => '2212879',
            'file'      => '',
            'index'     => '',
            'recursiv'  => '1',
            'max'       => '3',
            'chunk'     => '3',
            'filetype'  => '',
            'view'      => 'list',
            'orderby'   => 'size',
            'order'     => 'asc'
        ), $atts );
        
        return $this->query_args($this->remote_server_shortcode);
       
    }
    
    public function query_args($args) {
        
        $this->remote_server_args = array(
            'post_type'         =>  'Remote-Server',
            'p'                 =>  $args['server_id'],
            'posts_per_page'    =>  1,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        return $this->show_results_as_list($this->remote_server_args);
    }
    
    public function show_results_as_list($query_arguments) {
        
        echo '<pre>';
        print_r($query_arguments);
        echo '</pre>';
        
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
                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $domain, $api_key);
                
                /*echo '<pre>';
                print_r($this->remote_data);
                echo '</pre>';*/
                
                if($this->remote_data){

                    $url = parse_url(get_post_meta($post->ID, 'url', true)); 

                    if ($view == 'gallery') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/gallery.php');
                    } elseif($view == 'list') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/list.php');
                    } elseif($view == 'table') {
                        include( plugin_dir_path( __DIR__ ) . '/templates/table.php');
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
            
            $table = '<table><tr>';
            $table .= '<th>Name</th>';
            $table .= '<th>Änderungsdatum</th>';
            $table .= '<th>Dateityp</th>';
            $table .= '<th>Dateigröße</th>';
            $table .= '</tr>';

            $id = uniqid();

            foreach ($data[$i] as $key => $value) {
                
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

                $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $_REQUEST['host']  . $value['image'] . '">';
                $table .=  substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a>';
                $table .= '</td><td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td>';
                $table .= '<td>' . $value['extension'] . '</td>';
                $table .= '<td>' . $size.  '</td></tr>';
            }

            $table .= '</table>';
            echo $table;
        }
       
       die();
    }
    
    public function rrze_remote_glossary_script_footer() { 
        
        $glossary_files = $this->glossary_array;
         
        print_r($glossary_files);
	 
         ?>
         <script>
        jQuery(document).ready(function($) {
            
            var glossary = <?php echo json_encode($glossary_files); ?>;

            $('a[href^="#letter-"]').click(function(){
                var letter = $(this).attr('data-letter');
                /*var link^ = $(this).attr('class');
                var page = link.replace('page-', '');
                var pagecou^nt = $(this).attr('data-pagecount-value');
                var chunk = $(this).attr('data-chunk');*/
                var host = $(this).attr('data-host');
                /*var index = $(this).attr('data-index');
                var recursiv = $(this).attr('data-recursiv');
                var filetype = $(this).attr('data-filetype');*/
                
                $.ajax({
                    type: 'POST',
                    url: frontendajax.ajaxurl,
                    data: {
                        'action'    :'rrze_remote_glossary_ajax_request',
                        //'whatever'  : 1244,
                        'letter'    : letter,
                        /*'p'         : page,
                        'count'     : pagecount,
                        'index'     : index,
                        'recursiv'  : recursiv,
                        'filetype'  : filetype,
                        'chunk'     : chunk,*/
                        'host'      : host,
                        'glossary'  : glossary
                    },
                    success:function(data) {
                        $("#glossary").html(data);
                        //console.log(data);
                        //alert(data);
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

        $new_glossary = array_values($_REQUEST['glossary']);
        
        /*echo '<pre>';
        print_r($new_glossary);
        echo '</pre>';*/
        
        $table = '<table><tr>';
        $table .= '<th>Name</th>';
        $table .= '<th>Änderungsdatum</th>';
        $table .= '<th>Dateityp</th>';
        $table .= '<th>Dateigröße</th>';
        $table .= '</tr>';
        
        foreach ($new_glossary as $key => $value) {
                
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
            
            $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $_REQUEST['host']  . $value['image'] . '">';
            $table .=  substr($value['basename'], 0, strrpos($value['basename'], '.')) . '</a>';
            $table .= '</td><td>' . date('Y-m-d H:i:s', $value['change_time']) . '</td>';
            $table .= '<td>' . $value['extension'] . '</td>';
            $table .= '<td>' . $size.  '</td></tr>';
        }

        $table .= '</table>';
        echo $table;
        
        wp_die();
    }
}