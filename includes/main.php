<?php

namespace RRZE\Remoter;

use RRZE\Remoter\Options;
use RRZE\Remoter\Settings;

defined('ABSPATH') || exit;

class Main {
    
    public $options;
    
    public $settings;

    public function init($plugin_basename) {
        $this->options = new Options();
        //$this->settings = new Settings($this);       
        
        //add_action('admin_menu', array($this->settings, 'admin_settings_page'));
        //add_action('admin_init', array($this->settings, 'admin_settings'));         
        
    }
    
}
