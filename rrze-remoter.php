<?php

/**
 * Plugin Name:     Remoter
 * Plugin URI:      https://gitlab.rrze.fau.de/rrze-webteam/rrze-remoter.git
 * Description:     Liest den DirectoryIndex eines Servers remote aus und gibt die Daten strukturiert auf einer Seite aus.
 * Version:         1.3.4
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
require_once( __DIR__ . '/includes/posttype/Class_Create_Post_Type_Submenu_Page.php' );

require_once( __DIR__ . '/includes/remote/Class_Grab_Remote_Files.php' );
require_once( __DIR__ . '/includes/helper/Class_Help_Methods.php' );
require_once( __DIR__ . '/includes/shortcode/Class_Build_Shortcode.php' );

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
    
    //register_remoter_post_type();
    //flush_rewrite_rules();
    
    $caps_remoter = get_caps('remoter');
    add_caps('administrator', $caps_remoter);

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
    
    $caps_remoter = get_caps('remoter');
    remove_caps('administrator',  $caps_remoter);
    flush_rewrite_rules();
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

/*function register_remoter_post_type() {
    
    $remoter_custom_post_type   =   new Class_Custom_Post_Type_Server();
    $remoter_create_metaboxes   =   new Class_Create_Metaboxes();
    $remoter_customize_list     =   new Class_Customize_List_View();
    $remoter_add_submenu        =   new Class_Create_Post_Type_Submenu_Page();
    
    if( get_transient('rrze-remoter-options') ) {
        flush_rewrite_rules();
        delete_transient('rrze-remoter-options');
    }
    
}
*/
function get_caps($cap_type) {
    $caps = array(
        "edit_" . $cap_type,
        "read_" . $cap_type,
        "delete_" . $cap_type,
        "edit_" . $cap_type . "s",
        "edit_others_" . $cap_type . "s",
        "publish_" . $cap_type . "s",
        "read_private_" . $cap_type . "s",
        "delete_" . $cap_type . "s",
        "delete_private_" . $cap_type . "s",
        "delete_published_" . $cap_type . "s",
        "delete_others_" . $cap_type . "s",
        "edit_private_" . $cap_type . "s",
        "edit_published_" . $cap_type . "s",                
    );
    
    return $caps;
}

function add_caps($role, $caps) {
    $role = get_role($role);
    foreach($caps as $cap) {
        $role->add_cap($cap);
    }        
}

function remove_caps($role, $caps) {
    $role = get_role($role);
    foreach($caps as $cap) {
        $role->remove_cap($cap);
    }        
}    


/*
 * Wird durchgeführt, nachdem das WP-Grundsystem hochgefahren
 * und alle Plugins eingebunden wurden.
 * @return void
 */
function loaded() {
    
    //add_action('init', 'RRZE\Remoter\register_remoter_post_type');
    
    // Sprachdateien werden eingebunden.
    
    load_textdomain();
    
    // Ab hier können weitere Funktionen bzw. Klassen angelegt werden.
    autoload();
    
    $remoter_custom_post_type   =   new Class_Custom_Post_Type_Server();
    $remoter_create_metaboxes   =   new Class_Create_Metaboxes();
    $remoter_customize_list     =   new Class_Customize_List_View();
    $remoter_add_submenu        =   new Class_Create_Post_Type_Submenu_Page();
   
    
    $remoter_get_data   =   new Class_Grab_Remote_Files();
    $remoter_shortcode  =   new Class_Build_Shortcode();
    
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
    
    global $post;
    
    wp_register_script( 'rrze-remoter-mainjs', plugins_url( 'rrze-remoter/assets/js/rrze-remoter-main.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_register_script( 'rrze-remoter-scriptsjs', plugins_url( 'rrze-remoter/assets/js/rrze-remoter-scripts.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_register_style( 'rrze-remoter-stylescss', plugins_url( 'rrze-remoter/assets/css/styles.css', dirname(__FILE__) ) );
    wp_register_style( 'rrze-remoter-rrze-theme-stylescss', plugins_url( 'rrze-remoter/assets/css/rrze-styles.css', dirname(__FILE__) ) );
    wp_register_script( 'flexsliderjs', plugins_url( 'rrze-remoter/assets/js/jquery.flexslider.js', dirname(__FILE__)), array('jquery'),'', true);
    wp_register_script( 'fancyboxjs', plugins_url( 'rrze-remoter/assets/js/jquery.fancybox.js', dirname(__FILE__)), array('jquery'),'', true);
    
    if( is_page() && has_shortcode( $post->post_content, 'remoter') ) {
        wp_enqueue_script( 'rrze-remoter-mainjs' );
        wp_enqueue_script( 'rrze-remoter-scriptsjs' );
        
        $current_theme = wp_get_theme();
        $themes = array('FAU-Einrichtungen', 'FAU-Natfak', 'FAU-Philfak', 'FAU-RWFak', 'FAU-Techfak', 'FAU-Medfak');
        
        if(!in_array($current_theme, $themes)) {
            wp_enqueue_style( 'rrze-remoter-rrze-theme-stylescss' );
            wp_enqueue_script( 'flexsliderjs' );
            wp_enqueue_script( 'fancyboxjs' );
        } else {
            wp_enqueue_style( 'rrze-remoter-stylescss' );
        } 
       
    }
    
    wp_localize_script( 'rrze-remoter-mainjs', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}