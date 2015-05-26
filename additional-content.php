<?php
/**
 * Plugin Name: Additional Content
 * Plugin URI:
 * Description: Add additional content before or after post content in single post pages. Additional content can be added or edited in the edit or publish post screen.
 * Author: keesiemijer
 * Author URI:
 * License: GPL v2
 * Author URI:
 * Version: 1.0-beta1
 * Text Domain: additional-content
 * Domain Path: languages
 *
 * Additional Content
 * Copyright 2015  Kees Meijer  (email : keesie.meijer@gmail.com)
 *
 * Additional Content is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Additional Content is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Additional Content. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Additional Content
 * @category Core
 * @author Kees Meijer
 * @version 1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AC_Additional_Content' ) ) :

	final class AC_Additional_Content {
	/** Singleton **/

	/**
	 * This plugin's instance
	 *
	 * @since 1.0
	 * @var object.
	 */
	private static $instance;


	/**
	 * Main AC_Additional_Content Instance
	 *
	 * Insures that only one instance of AC_Additional_Content exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @return The AC_Additional_Content instance.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AC_Additional_Content ) ) {
			self::$instance = new AC_Additional_Content;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->load_textdomain();
		}

		return self::$instance;
	}


	/**
	 * Returns the defaults for additional content metabox fields.
	 *
	 * @since 1.0
	 * @return array Array with defaults.
	 */
	public function get_defaults() {
		return array(
			'append'             => '',
			'prepend'            => '',
			'additional_content' => '',
			'priority'           => 10,
		);
	}


	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'additional-content' ), '1.6' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'additional-content' ), '1.6' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'ADDITIONAL_CONTENT_VERSION' ) ) {
			define( 'ADDITIONAL_CONTENT_VERSION', '1.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'ADDITIONAL_CONTENT_PLUGIN_DIR' ) ) {
			define( 'ADDITIONAL_CONTENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'ADDITIONAL_CONTENT_PLUGIN_URL' ) ) {
			define( 'ADDITIONAL_CONTENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'ADDITIONAL_CONTENT_PLUGIN_FILE' ) ) {
			define( 'ADDITIONAL_CONTENT_PLUGIN_FILE', __FILE__ );
		}
	}


	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/functions.php';

		if ( !is_admin() ) {
			require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/filters.php';
			require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/class-public.php';
		}

		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/install.php';
	}


	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {
		$dir = dirname( plugin_basename( ADDITIONAL_CONTENT_PLUGIN_FILE ) ) . '/languages/';
		load_plugin_textdomain( 'additional-content', '', $dir );
	}

}

endif; // End if class_exists check


/**
 * The main function responsible for returning this plugin's instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing.
 * to declare the global.
 *
 * Example: <?php $additional_content = ac_additional_content(); ?>
 *
 * @since 1.0
 * @return object This plugin's instance.
 */
function ac_additional_content() {
	return AC_Additional_Content::instance();
}

// Get Additional Content Running.
ac_additional_content();