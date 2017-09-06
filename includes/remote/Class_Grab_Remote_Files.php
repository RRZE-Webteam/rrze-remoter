<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Grab_Remote_Files {
    
    public function __construct() {
        
    }
    
    public static function get_files_from_remote_server($index, $domain, $api_key) {
        
        $postdata = self::rrze_remote_download_query($index, $api_key);
        $opts = self::rrze_remote_download_opts($postdata);
        $context  = stream_context_create($opts);
        
        $response = @file_get_contents('http://' . $domain . '/remotefiles1.php', false, $context);
        $data = json_decode($response, true);
        
        return $data;
    }
    
    public static function rrze_remote_download_query($index, $api_key) {
        
        $postdata = http_build_query(
                
            array(
                
                'index'     =>  $index['index'],
                'recursiv'  =>  $index['recursiv'],
                'filetype'  =>  $index['filetype'],
                'file'      =>  $index['file'],
                'orderby'   =>  $index['orderby'],
                'order'     =>  $index['order'],
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
    }
    
}