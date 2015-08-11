<?php
/*
Plugin Name: VO Locator
Plugin URI: http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/
Description: Simple wordpress store locator plugin to manage multiple business locations and other any places using Google Maps. Manage a few or thousands of locations effortlessly with setup in minutes.
Version: 1.0.1
Author: Jurski
Author URI: http://www.vitalorganizer.com
*/
$vosl_version="1.0.1";
define('VOSL_VERSION', $vosl_version);
$vosl_db_version=1.0;
include_once("vosl-define.php");
include_once("vosl-functions.php");

add_action('admin_menu', 'vosl_add_options_page');

register_activation_hook( __FILE__, 'vosl_install_tables');

add_action('the_content', 'vosl_template');

function vosl_update_db_check() {
    global $vosl_db_version;
    if (vosl_data('vosl_db_version') != $vosl_db_version) {
        vosl_install_tables();
    }
}
add_action('plugins_loaded', 'vosl_update_db_check');
?>