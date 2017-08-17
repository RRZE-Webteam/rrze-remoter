<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Build_Shortcode {
    
    public function __construct() {
        
        add_shortcode('remoter', array($this, 'shortcode')); 
        
       /* $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server();
        
        echo '<pre>';
        print_r($this->remote_data);
        echo '</pre>';*/
        
    }
    
    public function shortcode($atts) {
        $this->remote_server_shortcode = shortcode_atts( array(
            'server_id' => '2212879',
            'index'     => 'images',
            'recursiv'  => '0',
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
        
        global $post;
        
        $shortcode_values = array();
        
        $the_query = new \WP_Query( $query_arguments);
        
        if ( $the_query->have_posts() ) {
            echo '<ul>';
            while ( $the_query->have_posts() ) {
                    $the_query->the_post();
                    echo '<li>' . get_the_title() . '</li>';
                    echo '<li>' . get_the_ID() . '</li>';
                    echo '<li>' . get_post_meta($post->ID, 'url', true) . '</li>';
                    
                    echo '<pre>';
                    print_r($this->remote_server_shortcode);
                    echo '</pre>';
                    
                    $file_index = $this->remote_server_shortcode['index'];
                    $this->remote_data = Class_Grab_Remote_Files::get_files_from_remote_server($this->remote_server_shortcode);
                    echo '<pre>';
                    print_r($this->remote_data);
                    echo '</pre>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
                echo 'no posts found';
        }
    }
}