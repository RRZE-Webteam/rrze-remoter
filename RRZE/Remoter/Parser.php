<?php

namespace RRZE\Remoter;

use \WP_Error;

class Parser
{
    public $l_delim = '{';

    public $r_delim = '}';

    public function __construct()
    {
    }

    public function template($template, $data)
    {
        if (!file_exists($template)) {
            return new WP_Error('template_file_not_found', __('Template file not found!', 'rrze-remoter'));
        }
        
        ob_start();
        include($template);
        $template = ob_get_contents();
        @ob_end_clean();
        
        if ($template === '') {
            return new WP_Error('template_is empty', __('Template is empty!', 'rrze-remoter'));
        }

        $replace = array();
        foreach ($data as $key => $val) {
            $replace = array_merge(
                $replace,
                is_array($val)
                    ? $this->parse_pair($key, $val, $template)
                    : $this->parse_single($key, (string) $val, $template)
            );
        }

        unset($data);
        $template = strtr($template, $replace);

        return $template;
    }

    public function set_delimiters($l = '{', $r = '}')
    {
        $this->l_delim = $l;
        $this->r_delim = $r;
    }

    protected function parse_single($key, $val, $string)
    {
        return array($this->l_delim.$key.$this->r_delim => (string) $val);
    }

    protected function parse_pair($variable, $data, $string)
    {
        $replace = array();
        preg_match_all(
            '#'.preg_quote($this->l_delim.$variable.$this->r_delim).'(.+?)'.preg_quote($this->l_delim.'/'.$variable.$this->r_delim).'#s',
            $string,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $str = '';
            foreach ($data as $row) {
                $temp = array();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        $pair = $this->parse_pair($key, $val, $match[1]);
                        if (! empty($pair)) {
                            $temp = array_merge($temp, $pair);
                        }
                        
                        continue;
                    }

                    $temp[$this->l_delim.$key.$this->r_delim] = $val;
                }

                $str .= strtr($match[1], $temp);
            }

            $replace[$match[0]] = $str;
        }

        return $replace;
    }
}
