<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/meet-tech-expert
 * @since             1.0.0
 * @package           Admin_Quote_Invoices
 *
 * @wordpress-plugin
 * Plugin Name:       Admin Quote Invoices
 * Plugin URI:        #
 * Description:       This plugin implements the functionality of admin modules.
 * Version:           1.0.0
 * Author:            Rinkesh
 * Author URI:        https://github.com/meet-tech-expert
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-quote-invoices
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_AQI_VERSION', '1.0.0' );
define( 'AQI_ADMIN_VIEW_PATH', plugin_dir_path( __FILE__ ).'admin/views' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-quote-invoices-activator.php
 */
function activate_admin_quote_invoices() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-quote-invoices-activator.php';
	Admin_Quote_Invoices_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-quote-invoices-deactivator.php
 */
function deactivate_admin_quote_invoices() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-quote-invoices-deactivator.php';
	Admin_Quote_Invoices_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_admin_quote_invoices' );
register_deactivation_hook( __FILE__, 'deactivate_admin_quote_invoices' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-admin-quote-invoices.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_admin_quote_invoices() {

	$plugin = new Admin_Quote_Invoices();
	$plugin->run();

}
run_admin_quote_invoices();
