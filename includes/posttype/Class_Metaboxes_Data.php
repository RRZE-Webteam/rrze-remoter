<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Metaboxes_Data {

    public static function Metaboxes_Data_Loader() {
        
        /*$metabox_data[] = array( 
            'id'          => 'url',
            'title'       => __( 'Server Url', 'rrze-remoter' ),
            'post_type'   => 'remote-server',
            'context'     => 'normal',
            'priority'    => 'high',
            'args'        => array(
              'id'        => 'url',
              'type'      => 'text',
            )                        
        );*/
        
        $metabox_data[] = array( 
            'id'          => 'domain',
            'title'       => __( 'Domain', 'rrze-remoter' ),
            'post_type'   => 'remote-server',
            'context'     => 'normal',
            'priority'    => 'high',
            'args'        => array(
                'id'        => 'abteilung',
                'type'      => 'select',
                'elemente'  => array(
                    '0'         =>  array(
                            'value' =>  'fau.de'
                    ),
                    '1'         => array(
                            'value' =>  'rw.fau.de'
                    ),
                    '2'         => array(
                             'value' => 'nat.fau.de'
                    ),
                    '3'         => array(
                             'value' => 'remoter.dev'
                    ),
                )
            )                        
        );
        
        $metabox_data[] = array( 
            'id'          => 'apikey',
            'title'       => __( 'API_Key', 'rrze-remoter' ),
            'post_type'   => 'remote-server',
            'context'     => 'normal',
            'priority'    => 'high',
            'args'        => array(
              'id'        => 'apikey',
              'type'      => 'text',
            )                        
        );
        
        return $metabox_data;
    }
}