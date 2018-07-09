<?php

namespace RRZE\Remoter;

use RRZE\Remoter\CPT\Create_Custom_Post_Type_Server;
use RRZE\Remoter\CPT\Custom_Post_Type_Server;
use RRZE\Remoter\CPT\Customize_List_View;
use RRZE\Remoter\CPT\Create_Post_Type_Submenu_Page;
use RRZE\Remoter\CPT\Metaboxes;
use RRZE\Remoter\Remote_Files;
use RRZE\Remoter\Shortcode;

defined('ABSPATH') || exit;

class Main {

    protected $plugin_file;
    
    protected $plugin_basename;

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_basename = plugin_basename($plugin_file);
        
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        

        $remoter_custom_post_type = new Custom_Post_Type_Server();
        $remoter_customize_list = new Customize_List_View();
        $remoter_create_metaboxes = new Metaboxes();
        
        $remote_files = new Remote_Files();
        $shortcode = new Shortcode($plugin_file);        
    }

    public function wp_enqueue_scripts() {
        global $post;

        wp_register_script('rrze-remoter-mainjs', plugins_url('assets/js/rrze-remoter-main.js', $this->plugin_basename), ['jquery'], '', true);
        wp_localize_script('rrze-remoter-mainjs', 'frontendajax', ['ajaxurl' => admin_url('admin-ajax.php')]);
        
        wp_register_script('rrze-remoter-scriptsjs', plugins_url('assets/js/rrze-remoter-scripts.js', $this->plugin_basename), ['jquery'], '', true);
        wp_register_style('rrze-remoter-stylescss', plugins_url('assets/css/styles.css', $this->plugin_basename));
        wp_register_style('rrze-remoter-rrze-theme-stylescss', plugins_url('assets/css/rrze-styles.css', $this->plugin_basename));
        wp_register_script('flexsliderjs', plugins_url('assets/js/jquery.flexslider.js', $this->plugin_basename), ['jquery'], '', true);
        wp_register_script('fancyboxjs', plugins_url('assets/js/jquery.fancybox.js', $this->plugin_basename), ['jquery'], '', true);

        if (is_singular() && in_array($post->post_type, ['post', 'page']) && has_shortcode($post->post_content, 'remoter')) {

            wp_enqueue_script('rrze-remoter-mainjs');
            wp_enqueue_script('rrze-remoter-scriptsjs');
            wp_enqueue_script('flexsliderjs');
            wp_enqueue_script('fancyboxjs');
                
            $stylesheet = get_stylesheet();
            $themes = ['FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak', 'FAU-Events'];
            
            if (!in_array($stylesheet, $themes)) {
                wp_enqueue_style('rrze-remoter-rrze-theme-stylescss');
            } else {
                wp_enqueue_style('rrze-remoter-stylescss');
            }
        }

    }

}
