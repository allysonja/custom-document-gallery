<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Custom_Document_Gallery_2
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Document Gallery
 * Plugin URI:        http://example.com/custom-document-gallery-2-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Sonja Linton
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-document-gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CUSTOM_DOCUMENT_GALLERY_2_VERSION', '1.0.0' );

/**
 * Current database version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your database and update it as you release new versions.
 */
define( 'CUSTOM_DOCUMENT_GALLERY_2_DATABASE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-custom-document-gallery-2-activator.php
 */
function activate_custom_document_gallery_2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-document-gallery-2-activator.php';
	Custom_Document_Gallery_2_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-custom-document-gallery-2-deactivator.php
 */
function deactivate_custom_document_gallery_2() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-document-gallery-2-deactivator.php';
	Custom_Document_Gallery_2_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_custom_document_gallery_2' );
register_deactivation_hook( __FILE__, 'deactivate_custom_document_gallery_2' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-custom-document-gallery-2.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_custom_document_gallery_2() {

	$plugin = new Custom_Document_Gallery_2();
	$plugin->run();

}
run_custom_document_gallery_2();