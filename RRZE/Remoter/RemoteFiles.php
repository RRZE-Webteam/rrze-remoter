<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class RemoteFiles
{

    // const TRANSIENT_PREFIX = 'rrze_remoter_cache_';
    // const TRANSIENT_EXPIRATION = DAY_IN_SECONDS;

    public static function getFiles($index, $apiurl, $apikey)
    {
        $response = json_decode(self::getData($apiurl, $apikey), true);
        $error = isset($response['error']) ? $response['error'] : 0;
        if ($error != 10) {
            return null;
        }

        $remote_data = explode(PHP_EOL, $response['value']);
        if (!is_array($remote_data) || empty($remote_data)) {
            return null;
        }

        $data = [];

        foreach ($remote_data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $valAry = json_decode($value, true);
            if (is_null($valAry)) {
                do_action(
                    'rrze.log.warning', 
                    [
                        'plugin' =>'rrze-remoter', 
                        'method' => __METHOD__, 
                        'message' => 'JSON cannot be decoded.',
                        'value' => $valAry
                    ]
                );
                continue;
            }
            $data[$key] = [
                'path' => $valAry['path'],
                'name' => $valAry['name'],
                'size' => $valAry['size'],
                'dir' => '/' . $valAry['path'] . '/',
                'extension' => substr(strrchr($valAry['name'], '.'), 1),
                'date' => $valAry['date'],
                'imagesize' => isset($valAry['imagesize']) ? Helper::maybeUnserialize($valAry['imagesize']) : [],
                'imageapp13' => isset($valAry['imagesize']) ? Helper::maybeUnserialize($valAry['imageapp13']) : []


            ];
        }

        unset($remote_data);

        if (empty($data)) {
            return null;
        }

        if (!empty($index['index']) && !empty($index['file'])) {
            $file = $index['file'];
            $pattern1 = '/' . $file . '/';
            $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) . ')$/i';
        } elseif (!empty($index['index']) && !empty($index['filter'])) {
            $directory = $index['index'];
            $mask = str_replace('/', '\/', $directory);
            $pattern1 = '/(' . $mask . ')/';
            $pattern2 = '/(' . $index['filter'] . ')|.json/i';
        } elseif (!empty($index['index']) && $index['recursiv'] == 1) {
            $directory = $index['index'];
            $mask = str_replace('/', '\/', $directory);
            $pattern1 = '/(' . $mask . ')/';
            $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) . ')$/i';
        } else {
            $directory = $index['index'];
            $mask = str_replace('/', '\/', $directory);
            $pattern1 = '/(' . $mask . ')$/';
            $pattern2 = '/.\.(json|' . str_replace(',', "|", $index['filetype']) . ')$/i';
        }

        $matches = array_filter($data, function ($a) use ($pattern1, $pattern2) {
            if (isset($a['imagesize'])) {
                unset($a['imagesize']);
            }
            if (isset($a['imageapp13'])) {
                unset($a['imageapp13']);
            }
            return preg_grep($pattern1, $a) && preg_grep($pattern2, $a);
        });

        return $matches;
    }


    public static function getData($apiurl, $apikey)
    {
        $domain = parse_url(site_url(), PHP_URL_HOST);

        $url = sprintf('%1$srequest.php?domain=%2$s&apikey=%3$s&request=data', $apiurl, $domain, $apikey);
        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=data' => $url]);

        $sslverify = defined('WP_DEBUG') && WP_DEBUG ? false : true;


        // $response = get_transient(self::TRANSIENT_PREFIX . $url);
        // $status_code = wp_remote_retrieve_response_code($response);

        // if ($status_code == 200) {
        //     return $response['body'];
        // }

        $response = wp_remote_get($url, ['httpversion' => '1.1', 'sslverify' => $sslverify]);
        $status_code = wp_remote_retrieve_response_code($response);

        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=data status code' => $status_code]);

        if ($status_code == 200) {
            // set_transient(self::TRANSIENT_PREFIX . $url, $response, self::TRANSIENT_EXPIRATION);
            return $response['body'];
        }

        return null;
    }
}
