<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Grab_Remote_Files {
    
    public function __construct() {
        
    }
    
    public static function csv_to_array($filename='', $delimiter=',') {
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
        
        if(self::search_for_api_key($domain, $api_key) === TRUE) {
        
            $result = self::csv_to_array('http://'. $domain .'/data.csv');

            foreach ($result as $key => $array) {
                $result[$key]['dir'] = '/'. $result[$key]['path'] .'/';
                $result[$key]['extension'] = substr(strrchr($result[$key]['name'],'.'), 1);
                $result[$key]['date'] = strtotime( $result[$key]['date']);
                if(strpos($result[$key]['name'] , '.') === false) {
                    unset($result[$key]);
                }
            }

            if(!empty($index['index']) && !empty($index['file'])) {
                $file = $index['file'];
                $pattern1 = '/' . $file . '/';
                $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) .')$/i';
            } elseif(!empty($index['index']) && !empty($index['filter'])) {
                $directory = $index['index'];
                $mask = str_replace('/', '\/', $directory);
                $pattern1 = '/(' . $mask . ')/';
                $pattern2 = '/(' . $index['filter'] . ')|.json/i';
            } elseif(!empty($index['index']) && $index['recursiv'] == 1) {
                $directory = $index['index'];
                $mask = str_replace('/', '\/', $directory);
                $pattern1 = '/(' . $mask . ')/';
                $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) .')$/i';
            } else {
                $directory = $index['index'];
                $mask = str_replace('/', '\/', $directory);
                $pattern1 = '/(' . $mask . ')$/';
                $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) .')$/i';
            }

            $matches = array_filter($result, function($a) use($pattern1, $pattern2)  {
                $b = preg_grep($pattern1, $a) && preg_grep($pattern2, $a);
                return $b;
            });

            return $matches;
            
        } else {
            
            return '';
            
        }
        
    }
    
    public static function search_for_api_key($domain, $api_key) {
        $handle = fopen('http://'. $domain .'/.hash', 'r');
        $valid = false;
        if($api_key) {
            while (($buffer = fgets($handle)) !== false) {
                if (strpos($buffer, trim($api_key)) !== false) {
                    $valid = TRUE;
                    break;
                }
            }
        }
        fclose($handle);
        
        return $valid;
    }
}