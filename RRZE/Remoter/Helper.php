<?php

namespace RRZE\Remoter;

use \WP_Error;

defined('ABSPATH') || exit;

class Helper
{
    public static function getHeaderData($columns)
    {
        $shortcodeColumns = explode(",", $columns);
        return $shortcodeColumns;
    }

    public static function getImageFormats()
    {
        return array('gif', 'png', 'jpg', 'jpeg', 'tiff', 'bmp');
    }

    public static function formatSize($bytes)
    {
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

    public static function getFolder($directory)
    {
        $titel = explode("/", $directory);
        $folder = $titel[count($titel)-1];

        return self::convertUmlauts($folder);
    }

    public static function convertUmlauts($name)
    {
        $replaced_name = str_replace(
            array('ae','oe','ue','eü','Oe','Ue','Ã','Ae','idF','GöChem','aür','öthan','_','Litaün','Isräl','sexüll','blü','qü'),
            array( 'ä','ö','ü','eue','Ö','Ü','Ä','Ä','i.d.F.','GoeChem','auer','oethan',' ','Litauen','Israel','sexuell','blue','que'),
            $name
        );

        return $replaced_name;
    }

    public static function deleteMetaTxtEntries($meta)
    {
        foreach ($meta as $key => $value) {
            if ($value['name'] === '.meta.json') {
                unset($meta[$key]);
            }
        }

        $data = array_values($meta);

        return $data;
    }

    public static function createLetters()
    {
        $letters = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
        );

        return $letters;
    }

    public static function getUsedLetters($data)
    {
        foreach ($data as $file) {
            $files[] = substr($file['name'], 0, 1);
        }

        $unique = array_unique($files);

        sort($unique);

        return $unique;
    }

    public static function checkforfigures($array)
    {
        foreach ($array as $key => $value) {
            if (is_numeric($value) || ctype_lower($value) || substr($value, 0, 1) === '.') {
                unset($array[$key]);
            }
        }

        $newindex = array_values($array);

        return $newindex;
    }

    public static function sortArray($data, $unique)
    {
        $filenames = array();

        foreach ($data as $file) {
            $filenames[] = $file['name'];
        }

        array_multisort($filenames, SORT_ASC, $data);

        $array_without_numbers = Helper::checkforfigures($unique);

        foreach ($data as $key => $value) {
            if (substr($value['name'], 0, 1) !=  $array_without_numbers[0]) {
                unset($data[$key]);
            }
        }

        $array_reindexed = array_values($data);

        return $array_reindexed;
    }

    public static function getMetafileNames($path, $store, $file)
    {
        if (!empty($store)) {
            $key = array_search($path, array_column($store, 'value'));

            if ($key > 0 || $key === 0 && $file == '' && !empty($store)) {
                $name = $store[$key]['key'];
            } else {
                $name = str_replace('_', ' ', $path);
            }
        } else {
            $name = str_replace('_', ' ', $path);
        }

        return $name;
    }

    public static function getJsonFile($shortcode_atts, $data)
    {
        $recursiv = $shortcode_atts['recursiv'];
        $path = $shortcode_atts['index'];
        $maskpath = str_replace('/', '\/', $path);
        $pattern1 = ($recursiv == 1) ? '/(' . $maskpath . ')/' : '/(' . $maskpath . ')$/';
        $pattern2 = '/.meta.json$/i';

        $metajson = array_filter($data, function ($a) use ($pattern1, $pattern2) {
            if (isset($a['imagesize'])) {
                unset($a['imagesize']);
            }
            if (isset($a['imageapp13'])) {
                unset($a['imageapp13']);
            }
            return preg_grep($pattern1, $a) && preg_grep($pattern2, $a);
        });

        array_multisort(array_column($metajson, 'dir'), SORT_ASC, $metajson);

        return $metajson;
    }

    public static function getJsonData($metajson, $domain)
    {
        $meta = array();
        $metadata = array();

        foreach ($metajson as $key => $array) {
            $meta[] = file_get_contents('https://' . $domain . $metajson[$key]['dir'] . '.meta.json');
        }

        foreach ($meta as $key => $array) {
            $metadata[] = json_decode($meta[$key], true);
        }

        return $metadata;
    }

    public static function replaceCharacterList($name)
    {
        $newName = str_replace('_', ' ', $name);
        return $newName;
    }

    public static function changeUmlautsList($filename)
    {
        $str = str_replace(
            array('ae','oe','ue','Ae','Oe','Ue','Ã'),
            array('ä','ö','ü','Ä','Ö','Ü','Ä'),
            $filename
        );

        return $str;
    }

    /**
     * Serialize data, if needed.
     *
     * @param string|array|object $data Data that might be serialized.
     * @return mixed A scalar data
     */
    public static function maybeSerialize($data)
    {
        if (is_array($data) || is_object($data)) {
            return serialize($data);
        }

        // Double serialization is required for backward compatibility.
        if (self::isSerialized($data, false)) {
            return serialize($data);
        }

        return $data;
    }

    /**
     * Unserialize value only if it was serialized.
     *
     * @param string $original Maybe unserialized original, if is needed.
     * @return mixed Unserialized data can be any type.
     */
    public static function maybeUnserialize($original)
    {
        if (self::isSerialized($original)) { // don't attempt to unserialize data that wasn't serialized going in
            return @unserialize($original);
        }
        return $original;
    }

    /**
     * Check value to find if it was serialized.
     *
     * If $data is not an string, then returned value will always be false.
     * Serialized data is always a string.
     *
     * @param string $data   Value to check to see if was serialized.
     * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
     * @return bool False if not serialized and true if it was.
     */
    public static function isSerialized($data, $strict = true)
    {
        // if it isn't a string, it isn't serialized.
        if (! is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
                // or else fall through
                // no break
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }

    public static function createHash($length = 32)
    {
        if (!isset($length) || intval($length) <= 8) {
            $length = 32;
        }
        // PHP 7
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        // PHP 5 >= 5.3.0, PHP 7
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
        // PHP 4, PHP 5, PHP 7
        return bin2hex(uniqid('', true));
    }
}
