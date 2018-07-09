<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

class Customize_List_View {

    public function __construct() {

        add_filter('manage_edit-remoter_columns', array($this, 'columns'));
        add_action('manage_remoter_posts_custom_column', array($this, 'custom_column'));
    }

    public function columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'rrze-remoter'),
            'id' => __('ID', 'rrze-remoter'),
            'apiurl' => __('API URL', 'rrze-remoter'),
            'date' => __('Date', 'rrze-remoter'),
        );

        return $columns;
    }

    public function custom_column($column_name) {

        global $post;

        switch ($column_name) {
            case 'title':
                $title = get_post_meta($post->ID, 'title', true);
                echo $title;
                break;
            case 'id':
                $id = get_the_ID();
                echo $id;
                break;
            case 'apiurl':
                $domain = get_post_meta($post->ID, '_rrze_remoter_apiurl', true);
                echo $domain;
                break;
        }
    }

}
