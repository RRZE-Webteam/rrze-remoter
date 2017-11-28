<?php

namespace RRZE\Remoter;

class Class_Custom_Post_Type_Server {
    
    public function __construct() {
        
        if ( is_admin() ) {
            self::register_post_type( 'remoter', __( 'Remote Servers', 'rrze-remoter' ), __( ' Remote Server', 'rrze-remoter' ) );
        }
    }
    
    public static function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {
        
        if ( ! $post_type || ! $plural || ! $single ) {
          
            return;
            
        }
        
       if( class_exists('RRZE\Remoter\Class_Create_Custom_Post_Type_Server') ) {
            
            $post_type = new Class_Create_Custom_Post_Type_Server( $post_type, $plural, $single, $description, $options );
            
            return $post_type;
       }
    }
}