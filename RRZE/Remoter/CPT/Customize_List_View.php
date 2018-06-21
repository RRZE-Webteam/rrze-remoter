<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

class Customize_List_View {

    public function __construct() {

        add_filter('manage_edit-remoter_columns', array($this, 'video_columns'));
        add_action('manage_remoter_posts_custom_column', array($this, 'show_video_columns'));
    }

    public function video_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'rrze-remoter'),
            'id' => __('ID', 'rrze-remoter'),
            'domain' => __('Domain', 'rrze-remoter'),
            'apikey' => __('API Key', 'rrze-remoter'),
            'date' => __('Date', 'rrze-remoter'),
        );

        return $columns;
    }

    public function show_video_columns($column_name) {

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
            case 'domain':
                $domain = get_post_meta($post->ID, 'domain', true);
                echo $domain;
                break;
            case 'apikey':
                $apikey = get_post_meta($post->ID, 'apikey', true);
                echo $apikey;
                break;
        }
    }

}
