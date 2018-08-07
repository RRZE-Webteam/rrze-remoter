<?php

namespace RRZE\Remoter\CPT;

defined('ABSPATH') || exit;

class Create_Custom_Post_Type_Server {

    public $post_type;
    public $plural;
    public $single;
    public $description;
    public $options;

    public function __construct($post_type, $plural, $single, $description, $options) {

        if (!$post_type || !$plural || !$single)
            return;

        $this->post_type = $post_type;
        $this->plural = $plural;
        $this->single = $single;
        $this->description = $description;
        $this->options = $options;

        add_action('init', array($this, 'register_post_type'));
    }

    public function register_post_type() {

        $labels = array(
            'name' => $this->single,
            'singular_name' => $this->single,
            'name_admin_bar' => $this->single,
            'add_new' => __('Add new', 'rrze-remoter'),
            'add_new_item' => sprintf(__('Add new %s', 'rrze-remoter'), $this->single),
            'edit_item' => sprintf(__('Edit %s', 'rrze-remoter'), $this->single),
            'new_item' => sprintf(__('New %s', 'rrze-remoter'), $this->single),
            'all_items' => sprintf(__('All %s', 'rrze-remoter'), $this->single),
            'view_item' => sprintf(__('Show %s', 'rrze-remoter'), $this->single),
            'search_items' => sprintf(__('Search %s', 'rrze-remoter'), $this->plural),
            'not_found' => sprintf(__('%s not found', 'rrze-remoter'), $this->plural),
            'not_found_in_trash' => sprintf(__('No %s found in Trash', 'rrze-remoter'), $this->plural),
            'parent_item_colon' => sprintf(__('Parent %s'), $this->single),
            'menu_name' => $this->single,
        );

        $args = array(
            'labels' => apply_filters($this->post_type . '_labels', $labels),
            'description' => $this->description,
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => true,
            'capability_type' => 'remoter',
            'capabilities' => array(
                'edit_post' => 'edit_remoter',
                'read_post' => 'read_remoter',
                'delete_post' => 'delete_remoter',
                'edit_posts' => 'edit_remoters',
                'edit_others_posts' => 'edit_others_remoters',
                'publish_posts' => 'publish_remoters',
                'read_private_posts' => 'read_private_remoters',
                'delete_posts' => 'delete_remoters',
                'delete_private_posts' => 'delete_private_remoters',
                'delete_published_posts' => 'delete_published_remoters',
                'delete_others_posts' => 'delete_others_remoters',
                'edit_private_posts' => 'edit_private_remoters',
                'edit_published_posts' => 'edit_published_remoters'
            ),
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => true,
            'supports' => array('title', 'thumbnail'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-links',
        );

        $args = array_merge($args, $this->options);
        register_post_type($this->post_type, apply_filters($this->post_type . '_register_args', $args, $this->post_type));
    }

}
