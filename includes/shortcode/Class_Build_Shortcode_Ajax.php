<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Build_Shortcode_Ajax {
    
    public function __construct() {
      
        add_action( 'wp_ajax_test_example_ajax_request', array($this, 'test_example_ajax_request' ));
        add_action( 'wp_ajax_nopriv_test_example_ajax_request', array($this, 'test_example_ajax_request' ));
        add_shortcode('remoter-ajax', array($this, 'shortcode')); 
        add_action( 'wp_footer', array($this,'test_add_this_script_footer'));
        
    }
    
    public function shortcode($atts) {
        
        $this->remote_server_shortcode = shortcode_atts( array(
            'server_id' => '2212879',
            'file'      => 'http://remoter.dev/images/jeny.png',
            'index'     => 'images',
            'recursiv'  => '1',
            'max'       => '5',
            'filetype'  => 'jpg',
            'view'      => 'list',
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

                echo '<pre>';
                print_r($this->remote_server_shortcode);
                echo '</pre>';

                $url = get_post_meta($post->ID, 'url', true); 

                $file_index = $this->remote_server_shortcode['index'];
                $view = $this->remote_server_shortcode['view'];
                $recursiv = $this->remote_server_shortcode['recursiv'];
                $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode, $url);

                $url = parse_url(get_post_meta($post->ID, 'url', true)); 

                $number_of_chunks = 3;

                //----------------------------------------------------------------------------
                // Break Array Into Chunks
                //----------------------------------------------------------------------------

                $data = array_chunk($this->remote_data, $number_of_chunks);
                
                echo '<pre>';
                print_r($data);
                echo '</pre>';

                //----------------------------------------------------------------------------
                // Get Page Count
                //----------------------------------------------------------------------------

                $pagecount = count($data);
                
                
                echo '<h3>Tabellenansicht mit Pagination</h3>';

                $table = '<table>';

                $id = uniqid();

                foreach ($this->remote_data as $key => $value) {

                    $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

                }

                $table .= '</table>';
                echo $table;
                
                $i = 0;
                /*if (isset($_GET['p']) && (is_numeric($_GET['p']))) {
                    if ($_GET['p'] > $pagecount) {
                        die('<span style="color:#FF0000">Error: Page Does Not Exist</span>');
                    }
                    $i = $_GET['p'] - 1;
                }
                else {
                    $i = 0;
                }*/

                
                //$res = '<div id="result"></div>';
                
                //$res1 = wp_strip_all_tags($res);
                
                //echo $res;
                
                echo '<pre>';
                print_r($data[$i]);
                echo '</pre>';
                
                $output = '<div id="result"><table>';
                
                foreach ($data[$i] as $key => $value) {

                    $output .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $url['host'] . '/' . $file_index . (($recursiv == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

                }
                 $output .= '</table></div>';
                echo $output;
                //$this->c = json_encode($this->remote_data);
               

                for ($i = 1; $i <= $pagecount; $i++) {
                    echo '<a data-index="' . $file_index . '" data-host="' . $url['host'] . '" data-chunk="' . $number_of_chunks . '" data-pagecount-value= "' . $pagecount . '" class="site-'. $i.'" href="#sign_up">'.$i.'</a> | ';
                   
                }

            }
         
            wp_reset_postdata();
        } else {
                echo 'no posts found';
        }
    }
    
    public function test_add_this_script_footer(){ 
        
        $test = 'samstag';
        
        $data = $this->remote_server_shortcode['server_id'];
        
        $recursiv = $this->remote_server_shortcode['recursiv'];
        
        $filetype = $this->remote_server_shortcode['filetype'];
        
        echo $filetype;
        
        $args = $this->remote_server_args['p'];
        
        $arr = $this->remote_data;
        
        $b = json_encode($this->c);
        
        print_r($b);
        //print_r($this->pagecount);
        
        //$test = json_encode($arr);
        
        //var_dump($test);
        
        ?>
  
        <script>
        jQuery(document).ready(function($) {

            // This is the variable we are passing via AJAX
            var fruit = 'Banana';
            
            //var index = <?php echo "'$args'" ?>
            
            var rec = <?php echo "'$recursiv'" ?>
            
            var filetype = <?php echo "'$filetype'" ?>
            
            //var test = <?php $test ?>
            
            var arr = <?php echo json_encode($arr); ?>;
            
            //var pcount = <?php $pagecount_ajax ?>
            
            /*$('a[href="#sign_up"]').click(function(){
                var link = $(this).attr('class');
                var substr = link.replace('site-', '');
                alert(substr);
            });*/ 
            

            // This does the ajax request (The Call).
            $('a[href="#sign_up"]').click(function(){
                var link = $(this).attr('class');
                var substr = link.replace('site-', '');
                var pagecount = $(this).attr('data-pagecount-value');
                var chunk = $(this).attr('data-chunk');
                var host = $(this).attr('data-host');
                var index = $(this).attr('data-index');
                //alert(substr);
                
                $.ajax({
                    url: frontendajax.ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
                    data: {
                        'action':'test_example_ajax_request', // This is a our PHP function below
                        'fruit' : fruit,
                        'p' : substr,
                        'count': pagecount,
                        'index': index,
                        'recursiv': rec,
                        'filetype': filetype,
                        'chunk'  : chunk,
                        'host' : host,
                        'arr'   : arr
                        //'test' : test
                        //'count' : pcount
                        //'arr'   : arr// This is the variable we are sending via AJAX
                    },
                    //async: true,
                    //dataType: 'json',
                    success:function(data) {
                // This outputs the result of the ajax request (The Callback)
                        //console.log(data);
                        //var tempDiv = $('<div>').html(data).find('#result').remove();
                        $( "#result" ).html(data);
                        
        
                        //$('a[href="#sign_up"]').attr('data-userId', data);
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
 

    
    
    public function test_example_ajax_request() {
        
        global $post;
        
        $args = array(
            'post_type'         =>  'Remote-Server',
            'p'                 =>  $_REQUEST['index'],
            'posts_per_page'    =>  1,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        if ( isset($_REQUEST) ) {
            
            /*echo '<pre>';
            print_r($_REQUEST);
            echo '<pre>';*/
            
            
            $number_of_chunks = $_REQUEST['chunk'];

            //----------------------------------------------------------------------------
            // Break Array Into Chunks
            //----------------------------------------------------------------------------

            $data = array_chunk($_REQUEST['arr'], $number_of_chunks);

            /*echo '<pre>';
            print_r($data);
            echo '</pre>';*/
            
          
            
            if (null !== $_REQUEST['p']) {
                if ($_REQUEST['p'] > $_REQUEST['count']) {
                    die('<span style="color:#FF0000">Error: Page Does Not Exist</span>');
                }
                $i = $_REQUEST['p'] - 1;
            }
            else {
                $i = 0;
            }
            
            //echo (int)$i;
            
            /*echo '<pre>';
            print_r($data[$i]);
            echo '</pre>';*/
            
            $table = '<table>';

            $id = uniqid();

            foreach ($data[$i] as $key => $value) {

                $table .= '<tr><td><a class="lightbox" rel="lightbox-' . $id . '" href="http://'. $_REQUEST['host'] . '/' . $_REQUEST['index'] . (($_REQUEST['recursive'] == 1) ? '' : '/') . $value . '">' . basename($value) . '</a></td></tr>';

            }

            $table .= '</table>';
            echo $table;
        }
       
       die();
    }
}