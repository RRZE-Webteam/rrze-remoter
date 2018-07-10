<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

use RRZE\Remoter\CPT\Create_Custom_Post_Type_Server;

class Custom_Post_Type_Server {

    public function __construct() {

        if (is_admin()) {
            $this->register_post_type('remoter', __('Remote Servers', 'rrze-remoter'), __('Remote Server', 'rrze-remoter'));
        }
    }

    public function register_post_type($post_type = '', $plural = '', $single = '', $description = '', $options = array()) {

        if (!$post_type || !$plural || !$single) {

            return;
        }

        $post_type = new Create_Custom_Post_Type_Server($post_type, $plural, $single, $description, $options);

        return $post_type;
    }

}
