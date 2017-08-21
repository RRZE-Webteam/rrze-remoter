<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Build_Shortcode {
    
    public function __construct() {
        
        //add_action(' init', array($this,'add_this_script_footer'));
        add_action( 'wp_ajax_example_ajax_request', array($this, 'example_ajax_request' ));
        add_action( 'wp_ajax_nopriv_example_ajax_request', array($this, 'example_ajax_request' ));

        add_shortcode('remoter', array($this, 'shortcode')); 
        
        
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
    
    public function query_args_table($args) {
        
        $this->remote_server_args = array(
            'post_type'         =>  'Remote-Server',
            'p'                 =>  $args['server_id'],
            'posts_per_page'    =>  5,
            'orderby'           =>  'date',
            'order'             =>  'DESC'
        );
        
        return $this->show_results_as_table($this->remote_server_args);
        
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

                if ($view == 'gallery') {
                    include( plugin_dir_path( __DIR__ ) . '/templates/gallery.php');
                } elseif($view == 'list') {
                    include( plugin_dir_path( __DIR__ ) . '/templates/list.php');
                } elseif($view == 'table') {
                    include( plugin_dir_path( __DIR__ ) . '/templates/table.php');
                    //$test = new Class_Remoter_Table_View();
                    //$test->rrze_remoter_script();
                } else {
                    include( plugin_dir_path( __DIR__ ) . '/templates/imagetable.php');
                }
                   
            }
         
            wp_reset_postdata();
        } else {
                echo 'no posts found';
        }
    }
    
    public function add_this_script_footer(){ ?>
  
        <script>
        jQuery(document).ready(function($) {

            // This is the variable we are passing via AJAX
            var fruit = 'Banana';

            // This does the ajax request (The Call).
            $.ajax({
                url: frontendajax.ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
                data: {
                    'action':'example_ajax_request', // This is a our PHP function below
                    'fruit' : fruit // This is the variable we are sending via AJAX
                },
                success:function(data) {
            // This outputs the result of the ajax request (The Callback)
                    window.alert(data);
                },  
                error: function(errorThrown){
                    window.alert(errorThrown);
                }
            });   

        });
        </script>
        <?php } 
 

    
    
    public function example_ajax_request() {
  
        // The $_REQUEST contains all the data sent via AJAX from the Javascript call
        if ( isset($_REQUEST) ) {

            print_r($_REQUEST);

            echo $this->remote_server_shortcode;

            $fruit = $_REQUEST['fruit'];

            // This bit is going to process our fruit variable into an Apple
            if ( $fruit == 'Banana' ) {
                $fruit = 'Apple';
            }

            // Now let's return the result to the Javascript function (The Callback) 
            echo $fruit;        
        }

        // Always die in functions echoing AJAX content
       die();
    }
}