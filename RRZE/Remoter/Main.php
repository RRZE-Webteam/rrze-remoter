<?php

namespace RRZE\Remoter;

use RRZE\Remoter\CPT\Create_Custom_Post_Type_Server;
use RRZE\Remoter\CPT\Custom_Post_Type_Server;
use RRZE\Remoter\CPT\Customize_List_View;
use RRZE\Remoter\CPT\Create_Post_Type_Submenu_Page;
use RRZE\Remoter\CPT\Metaboxes;
use RRZE\Remoter\RemoteFiles;
use RRZE\Remoter\Shortcode;

defined('ABSPATH') || exit;

class Main {

    protected $plugin_file;

    protected $plugin_basename;

    protected $fauThemes = [
        'FAU-Einrichtungen',
        'FAU-Natfak',
        'FAU-Philfak',
        'FAU-RWFak',
        'FAU-Techfak',
        'FAU-Medfak'
    ];

    public function __construct($plugin_file) {
        $this->plugin_file = $plugin_file;
        $this->plugin_basename = plugin_basename($plugin_file);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        $remoter_custom_post_type = new Custom_Post_Type_Server();
        $remoter_customize_list = new Customize_List_View();
        $remoter_create_metaboxes = new Metaboxes();

        $remote_files = new RemoteFiles();
        $shortcode = new Shortcode($plugin_file);
    }

    public function enqueueScripts() {
        wp_register_style('flexslider', plugins_url('assets/css/jquery.flexslider.min.css', $this->plugin_basename));
        wp_register_script('flexslider', plugins_url('assets/js/jquery.flexslider.min.js', $this->plugin_basename), [], '', true);

        wp_register_style('fancybox', plugins_url('assets/css/jquery.fancybox.min.css', $this->plugin_basename));
        wp_register_script('fancybox', plugins_url('assets/js/jquery.fancybox.min.js', $this->plugin_basename), [], '', true);

        $stylesheet = get_stylesheet();
        if (! in_array($stylesheet, $this->fauThemes)) {
            $styleDeps = ['flexslider', 'fancybox'];
            $scriptDeps = ['jquery', 'flexslider', 'fancybox'];
        } else {
            $styleDeps = ['flexslider'];
            $scriptDeps = ['jquery', 'flexslider'];
        }
        wp_register_style('rrze-remoter', plugins_url('assets/css/styles.min.css', $this->plugin_basename), $styleDeps);
        wp_register_script('rrze-remoter', plugins_url('assets/js/scripts.min.js', $this->plugin_basename), $scriptDeps, '', true);
    }

}
