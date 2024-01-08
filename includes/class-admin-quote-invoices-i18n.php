<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.upwork.com/o/profiles/users/_~01b8271e5700438133/
 * @since      1.0.0
 *
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Admin_Quote_Invoices
 * @subpackage Admin_Quote_Invoices/includes
 * @author     Jyoti M <jmodanwal789@gmail.com>
 */
class Admin_Quote_Invoices_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'admin-quote-invoices',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
