<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Metaboxes_Data {

    public static function Metaboxes_Data_Loader() {
        
        return $metabox_data[] = array( 
          'id'          => 'url',
          'title'       => __( 'Server Url', 'rrze-remoter' ),
          'post_type'   => 'remote-server',
          'context'     => 'normal',
          'priority'    => 'high',
          'args'        => array(
            'id'        => 'url',
            'type'      => 'text',
          )                        
        );
    }
}