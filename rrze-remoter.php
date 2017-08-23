<?php

/**
 * Plugin Name:     Remoter
 * Plugin URI:      https://gitlab.rrze.fau.de/rrze-webteam/rrze-remoter.git
 * Description:     Liest den DirectoryIndex eines Servers remote aus und gibt die Daten strukturiert auf einer Seite aus.
 * Version:         0.0.1
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-remoter
 */

/*
  Verzeichnisschema:
  rrze-remoter
  |-- languages                     Verzeichnis der Sprachdateien
  |   +-- rrze-remoter.pot             Vorlagedatei falls Übersetzungen in andere Sprachen nötig werden
  |   +-- rrze-remoter-de_DE.po        Deutsche Übersetzungsdatei (kann mit poedit angepasst werden)
  |   +-- rrze-remoter-de_DE.mo        Deutsche Übersetzungsdatei (wird beim Speichern in poedit aktualisiert)
  |   +-- rrze-remoter_DE_formal.po Deutsche (Sie) Übersetzungsdatei (kann mit poedit angepasst werden)
  |   +-- crrze-remoter-de_DE_formal.mo Deutsche (Sie) Übersetzungsdatei (wird beim Speichern in poedit aktualisiert)
  |-- includes                      (Optional)
      +-- autoload.php              Automatische Laden von Klassen
      +-- main.php                  Main-Klasse
      +-- options.php               Optionen-Klasse
      +-- settings.php              Settings-Klasse
  +-- README.md                     Anweisungen
  +-- rrze-remoter.php                 Hauptdatei des Plugins
 */

namespace RRZE\Remoter;

use RRZE\Remoter\Main;

defined('ABSPATH') || exit;

const RRZE_PHP_VERSION = '5.5';
const RRZE_WP_VERSION = '4.8';

register_activation_hook(__FILE__, 'RRZE\Remoter\activation');
register_deactivation_hook(__FILE__, 'RRZE\Remoter\deactivation');

add_action('plugins_loaded', 'RRZE\Remoter\loaded');
add_action( 'wp_enqueue_scripts', 'RRZE\Remoter\custom_libraries_scripts');


/* Includes */

require_once( __DIR__ . '/includes/posttype/Class_Customize_List_View.php' );
require_once( __DIR__ . '/includes/posttype/Class_Metaboxes_Data.php' );
require_once( __DIR__ . '/includes/posttype/Class_Create_Metaboxes.php' );
require_once( __DIR__ . '/includes/posttype/Class_Create_Custom_Post_Type_Server.php' );
require_once( __DIR__ . '/includes/posttype/Class_Custom_Post_Type_Server.php' );

require_once( __DIR__ . '/includes/remote/Class_Grab_Remote_Files.php' );
require_once( __DIR__ . '/includes/shortcode/Class_Build_Shortcode.php' );
require_once( __DIR__ . '/includes/shortcode/Class_Build_Shortcode_Ajax.php' );

//require_once( __DIR__ . '/includes/templates/table.php' );



//add_action( 'wp_ajax_example_ajax_request', 'RRZE\Remoter\example_ajax_request' );
//add_action( 'wp_ajax_nopriv_example_ajax_request', 'RRZE\Remoter\example_ajax_request' );

//add_action( 'wp_ajax_example_ajax_request', array($this, 'RRZE\Remoter\example_ajax_request' ));
//add_action( 'wp_ajax_nopriv_example_ajax_request', array($this, 'RRZE\Remoter\example_ajax_request' ));



/*
 * Einbindung der Sprachdateien.
 * @return void
 */
function load_textdomain() {
    load_plugin_textdomain('rrze-remoter', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/*
 * Wird durchgeführt, nachdem das Plugin aktiviert wurde.
 * @return void
 */
function activation() {
    // Sprachdateien werden eingebunden.
    load_textdomain();

    // Überprüft die minimal erforderliche PHP- u. WP-Version.
    system_requirements();

    // Ab hier können die Funktionen hinzugefügt werden, 
    // die bei der Aktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_schedule_event, flush_rewrite_rules, etc.    
}

/*
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 * @return void
 */
function deactivation() {
    // Hier können die Funktionen hinzugefügt werden, die
    // bei der Deaktivierung des Plugins aufgerufen werden müssen.
    // Bspw. wp_clear_scheduled_hook, flush_rewrite_rules, etc.
}

/*
 * Überprüft die minimal erforderliche PHP- u. WP-Version.
 * @return void
 */
function system_requirements() {
    $error = '';

    if (version_compare(PHP_VERSION, RRZE_PHP_VERSION, '<')) {
        $error = sprintf(__('Your server is running PHP version %s. Please upgrade at least to PHP version %s.', 'rrze-remoter'), PHP_VERSION, RRZE_PHP_VERSION);
    }

    if (version_compare($GLOBALS['wp_version'], RRZE_WP_VERSION, '<')) {
        $error = sprintf(__('Your Wordpress version is %s. Please upgrade at least to Wordpress version %s.', 'rrze-remoter'), $GLOBALS['wp_version'], RRZE_WP_VERSION);
    }

    // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
    if (!empty($error)) {
        deactivate_plugins(plugin_basename(__FILE__), FALSE, TRUE);
        wp_die($error);
    }
}

/*
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 * @return void
 */
function loaded() {
    // Sprachdateien werden eingebunden.
    
    load_textdomain();
    
    // Ab hier können weitere Funktionen bzw. Klassen angelegt werden.
    autoload();
    
    $remoter_custom_post_type   =   new Class_Custom_Post_Type_Server();
    $remoter_create_metaboxes   =   new Class_Create_Metaboxes();
    $remoter_customize_list     =   new Class_Customize_List_View();
    
    $remoter_get_data   =   new Class_Grab_Remote_Files();
    //$remoter_get_data->get_files_from_remote_server();
    $remoter_shortcode  =   new Class_Build_Shortcode();
    $remoter_shortcode_ajax  =   new Class_Build_Shortcode_Ajax();
    
}

/*
 * Automatische Laden von Klassen.
 * @return void
 */
function autoload() {
    require __DIR__ . '/includes/autoload.php';
    $main = new Main();
    $main->init(plugin_basename(__FILE__));
}

function custom_libraries_scripts() {
    
    wp_register_script( 'mainjs', plugins_url( 'rrze-remoter/assets/js/rrze-remoter-main.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_enqueue_script( 'mainjs' );
    wp_register_script( 'scriptsjs', plugins_url( 'rrze-remoter/assets/js/rrze-remoter-scripts.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_enqueue_script( 'scriptsjs' );
    wp_register_script( 'flexsliderjs', plugins_url( 'rrze-remoter/assets/js/jquery.flexslider.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_enqueue_script( 'flexsliderjs' );
    
    /*wp_localize_script( 'ajax-script', 'rrze-remoter_ajax_table_object',
        array( 
            'ajax_url' => admin_url( 'admin-ajax.php' ) 
        ) 
    );*/
    
    wp_localize_script( 'mainjs', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    
}

/*function example_ajax_request() {
  
    // The $_REQUEST contains all the data sent via AJAX from the Javascript call
    if ( isset($_REQUEST) ) {
        
        print_r($_REQUEST);
      
        $fruit = $_REQUEST['fruit'];
          
        // This bit is going to process our fruit variable into an Apple
        if ( $fruit == 'Banana' ) {
            $fruit = 'Apple';
        }
      
        // Now let's return the result to the Javascript function (The Callback) 
        echo $fruit;        
    }
      
    // Always die in functions echoing AJAX content
   die();
   
//add_action( 'wp_ajax_example_ajax_request', 'example_ajax_request' );
//add_action( 'wp_ajax_nopriv_example_ajax_request', 'example_ajax_request' );
}*/