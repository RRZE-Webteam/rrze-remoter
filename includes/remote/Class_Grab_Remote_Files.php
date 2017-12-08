<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Grab_Remote_Files {
    
    public function __construct() {
        
    }
    
    public static function csv_to_array($filename='', $delimiter=',') {
        if(!file_exists($filename) || !is_readable($filename))
                return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                        if(!$header)
                                $header = $row;
                        else
                                $data[] = array_combine($header, $row);
                }
                fclose($handle);
        }
        return $data;
    }
    
    public static function getListOfFileExtensions() {
        return array('jpg', 'jpeg', 'png', 'tif', 'gif', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf');
    }
    
    public static function get_files_from_remote_server($index, $domain, $api_key) {
        
        $response = wp_remote_post('http://' . $domain . '/data.csv');
        
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            /*echo 'Response:<pre>';
            //print_r( $response['body'] );
            echo '</pre>';*/
        }
        
        $fp = fopen('wp-content/plugins/rrze-remoter/includes/remote/result.csv', 'w+');
        fwrite($fp, $response['body']);
        fclose($fp);
        
        $result = self::csv_to_array('wp-content/plugins/rrze-remoter/includes/remote/result.csv');
        
        $path = $index['index'];
        $maskjson = str_replace('/', '\/', $path);
        $patternmeta1 = '/(' . $maskjson . ')/';
        $patternmeta2 = '/.json$/i';
        
        foreach ($result as $key => $array) {
            $result[$key]['dir'] = str_replace('/proj/websource/docs/FAUWeb/www.uni-erlangen.de/websource','', $result[$key]['path'] .'/');
            $result[$key]['extension'] = substr(strrchr($result[$key]['name'],'.'), 1);
            if(strpos($result[$key]['name'] , '.') === false) {
                unset($result[$key]);
            }
        }
        
        if(!empty($index['index']) && !empty($index['file'])) {
            $file = $index['file'];
            $pattern1 = '/' . $file . '/';
            $pattern2 = '/.\.(txt|' . str_replace(',', "|", $index['filetype']) .')$/i';
        } else {
            $directory = $index['index'];
            $mask = str_replace('/', '\/', $directory);
            $pattern1 = '/(' . $mask . ')/';
            $pattern2 = '/.\.(txt|' . str_replace(',', "|", $index['filetype']) .')$/i';
        }

        $matches = array_filter($result, function($a) use($pattern1, $pattern2)  {
            $b = preg_grep($pattern1, $a) && preg_grep($pattern2, $a);
            return $b;
        });
        
        return $matches;
        
    }
    
}