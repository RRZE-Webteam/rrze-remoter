<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class Class_Help_Methods {
    
    public static function getHeaderData($columns) {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }
    
    public static function getImageFormats() {
        return array('gif', 'png', 'jpg', 'jpeg', 'tiff', 'bmp');
    }
    
    public static function formatSize($bytes) {

        if ($bytes>= 1073741824) {
            $size = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
           $size = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 0) . ' KB';
        } elseif ($bytes > 1) {
            $size = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $size = '1 byte';
        } else {
            $size = '0 bytes';
        }
        
        return $size;
    }
    
    public static function getFolder($directory) {
 
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];
        
        return $folder;
        
    }
    
    public static function deleteMetaTxtEntries($meta) {
        foreach($meta as $key => $value) {
            if($value['name'] === '.meta.json') {
                unset($meta[$key]);
            }
        }
        
        $data = array_values($meta);
        
        return $data;
    }
    
    public static function createLetters() {
        $letters = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
        );
    
        return $letters;
    }
    
    public static function getUsedLetters($data) {
   
        foreach ($data as $file) {
            $files[] = substr($file['name'], 0, 1);
        }

        $unique = array_unique($files);

        sort($unique);

        return $unique;

    }
    
    public static function checkforfigures($array) {
        
        foreach($array as $key => $value) {
            
            if(is_numeric($value) || ctype_lower($value) || substr($value, 0, 1) === '.') {
                unset($array[$key]);
            }       
        }
        
        $newindex = array_values($array);
        
        return $newindex;
    }
    
    public static function sortArray($data, $unique) {
    
        $filenames = array(); 

        foreach ($data as $file) {
            $filenames[] = $file['name'];
        }

        array_multisort($filenames, SORT_ASC, $data);

        $array_without_numbers = Class_Help_Methods::checkforfigures($unique);

        foreach ( $data as $key => $value ) {
            if ( substr($value['name'], 0, 1) !=  $array_without_numbers[0] ) {
                unset( $data[$key]);
            }
        }

        $array_reindexed = array_values($data);

        return $array_reindexed;

    }
    
    public static function getMetafileNames($path, $store, $file) {
        
        if(!empty($store)) {
            $key = array_search($path , array_column($store, 'value'));

            if($key > 0 || $key === 0 && $file == '' && !empty($store)) {
                $name = $store[$key]['key'];
            } else {
                $name = str_replace('_', ' ', $path);
            }
        } else {
            $name = str_replace('_', ' ', $path);
        }
        
        return $name;
        
    }
    
    public static function getJsonFile($shortcodeValues, $data) {
        
        $path = $shortcodeValues['fileIndex'];
        $maskjson = str_replace('/', '\/', $path);
        $patternmeta1 = '/(' . $maskjson . ')/';
        $patternmeta2 = '/.meta.json$/i';
        
        $metajson = array_filter($data, function($a) use($patternmeta1, $patternmeta2)  {
            $c = preg_grep($patternmeta1, $a) && preg_grep($patternmeta2, $a);
            return $c;
        });
        
        array_multisort(array_column($metajson, 'dir'), SORT_ASC , $metajson);
        
        return $metajson;
    }
    
    public static function getJsonData($metajson, $domain) {
        
        $meta = array();
        $metadata = array();

        foreach ($metajson as $key => $array) {
            $meta[] = file_get_contents('http://' . $domain . $metajson[$key]['dir'] . '.meta.json');
        }

        foreach ($meta as $key => $array) {

            $metadata[] = json_decode($meta[$key],true);

        }
        
        return $metadata;
        
    }
    
}

