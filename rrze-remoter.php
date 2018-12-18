<?php

/**
 * Plugin Name:     Remoter
 * Plugin URI:      https://gitlab.rrze.fau.de/rrze-webteam/rrze-remoter.git
 * Description:     Liest den DirectoryIndex eines Servers remote aus und gibt die Daten strukturiert auf einer Seite aus.
 * Version:         1.5.4
 * Author:          RRZE-Webteam
 * Author URI:      https://blogs.fau.de/webworking/
 * License:         GNU General Public License v2
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Text Domain:     rrze-remoter
 */

namespace RRZE\Remoter;

use RRZE\Remoter\Main;

defined('ABSPATH') || exit;

const RRZE_PHP_VERSION = '7.1';
const RRZE_WP_VERSION = '5.0';

register_activation_hook(__FILE__, 'RRZE\Remoter\activation');
register_deactivation_hook(__FILE__, 'RRZE\Remoter\deactivation');

add_action('plugins_loaded', 'RRZE\Remoter\loaded');

function load_textdomain() {
    load_plugin_textdomain('rrze-remoter', FALSE, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));
}

/*
 * Wird durchgeführt, nachdem das Plugin aktiviert wurde.
 * @return void
 */
function activation() {
    load_textdomain();

    system_requirements();

    //register_remoter_post_type();
    //flush_rewrite_rules();

    $caps_remoter = get_caps('remoter');
    add_caps('administrator', $caps_remoter);
}

/*
 * Wird durchgeführt, nachdem das Plugin deaktiviert wurde.
 * @return void
 */
function deactivation() {
    $caps_remoter = get_caps('remoter');
    remove_caps('administrator', $caps_remoter);
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
    foreach ($caps as $cap) {
        $role->add_cap($cap);
    }
}

function remove_caps($role, $caps) {
    $role = get_role($role);
    foreach ($caps as $cap) {
        $role->remove_cap($cap);
    }
}

function loaded() {
    load_textdomain();
    autoload();
}

function autoload() {
    require 'autoload.php';
    return new Main(__FILE__);
}
