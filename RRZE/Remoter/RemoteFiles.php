<?php

namespace RRZE\Remoter;

defined('ABSPATH') || exit;

class RemoteFiles
{
    public static function getFiles($index, $apiurl, $apikey)
    {
        $response = json_decode(self::getData($apiurl, $apikey), true);

        $error = isset($response['error']) ? $response['error'] : 0;
        if ($error != 10) {
            return null;
        }
        
        $data = json_decode($response['value'], true);
        if (!is_array($data) || empty($data)) {
            return null;
        }

        foreach ($data as $key => $value) {
            $data[$key]['dir'] = '/' . $data[$key]['path'] . '/';
            $data[$key]['extension'] = substr(strrchr($data[$key]['name'], '.'), 1);
            $data[$key]['date'] = strtotime($data[$key]['date']);
            if (strpos($data[$key]['name'], '.') === false) {
                unset($data[$key]);
            }
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
            $b = preg_grep($pattern1, $a) && preg_grep($pattern2, $a);
            return $b;
        });

        return $matches;
    }

    public static function getData($apiurl, $apikey)
    {
        $domain = parse_url(site_url(), PHP_URL_HOST);

        $url = sprintf('%1$srequest.php?domain=%2$s&apikey=%3$s&request=data', $apiurl, $domain, $apikey);
        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=data' => $url]);

        $sslverify = defined('WP_DEBUG') && WP_DEBUG ? false : true;

        $response = wp_remote_get($url, ['httpversion' => '1.1', 'sslverify' => $sslverify]);
        $status_code = wp_remote_retrieve_response_code($response);

        do_action('rrze.log.debug', ['plugin' => 'rrze-remoter', 'remote url request=data status code' => $status_code]);

        if ($status_code == 200) {
            return $response['body'];
        }

        return null;
    }
}
