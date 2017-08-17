<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Grab_Remote_Files {
    
    public function __construct() {
        
    }
    
    public static function get_files_from_remote_server($index) {
        
        $postdata = self::rrze_remote_download_query($index);
        $opts = self::rrze_remote_download_opts($postdata);
        $context  = stream_context_create($opts);
        
        $response = file_get_contents('http://remoter.dev/remotefiles.php', false, $context);
        $data = json_decode($response);
        
        return $data;
    }
    
    public static function rrze_remote_download_query($index) {
        
        $postdata = http_build_query(
                
            array(
                
                'index'     =>  $index['index'],
                'recursiv'  =>  $index['recursiv'],
                'filetype'  =>  $index['filetype']
                
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