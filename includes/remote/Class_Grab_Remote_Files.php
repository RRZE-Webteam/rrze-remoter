<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Grab_Remote_Files {
    
    public function __construct() {
        
    }
    
    public static function get_files_from_remote_server($index, $domain, $api_key) {
        
       /* $postdata = self::rrze_remote_download_query($index, $api_key);
        $opts = self::rrze_remote_download_opts($postdata);
        $context  = stream_context_create($opts);
        
        //$response = @file_get_contents('http://wwww.' . $domain . '/remotefiles.php', false, $context);
        /*$response = wp_remote_post( 'http://' . $domain . '/remotefiles.php?' .
            'index=' . $index['index'],
            '&serverid=' . $serverid .    
            '&email=' . $adminemail . 
            '&domain=' . $domain . 
            '&requested_domain=' . (isset($meta[0]) ? $meta[0] : ''), 
            array( 'timeout' => 120, 'httpversion' => '1.1' )
        );

        //echo $response['body'];
        //var_dump($http_response_header);
        
        echo '<pre>';
        print_r($response);
        echo '</pre>';*/
        
        //$data = json_decode($response, true);
        
        //return $data;*/
       
       $response = wp_remote_post('http://' . $domain . '/remotefiles.php', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'index'     =>  $index['index'],
                'recursiv'  =>  $index['recursiv'],
                'filetype'  =>  $index['filetype'],
                'file'      =>  $index['file'],
                'orderby'   =>  $index['orderby'],
                'order'     =>  $index['order'],
                'filter'    =>  $index['filter'],
                'alias'     =>  $index['alias'],
                'api_key'   =>  $api_key,
            ),
            'cookies' => array()
            )
        );
        
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
         } else {
            //echo 'Response:<pre>';
            //print_r( $response );
            //echo '</pre>';
         }
        
           
        //echo '<pre>';
        //print_r($response);
        //echo '</pre>';
        
        $data = json_decode($response['body'], true);
        
        return $data;
    } 

   /* public static function rrze_remote_download_query($index, $api_key) {
        
        $postdata = http_build_query(
                
            array(
                
                'index'     =>  $index['index'],
                'recursiv'  =>  $index['recursiv'],
                'filetype'  =>  $index['filetype'],
                'file'      =>  $index['file'],
                'orderby'   =>  $index['orderby'],
                'order'     =>  $index['order'],
                'filter'    =>  $index['filter'],
                'alias'     =>  $index['alias'],
                'api_key'   =>  $api_key,
                
            )
        
        );
        
        return $postdata;
    }
    
    public static function rrze_remote_download_opts($postdata) {

        $opts = array('http' =>
            
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
            
        );
        
        return $opts;
    }*/
    
}