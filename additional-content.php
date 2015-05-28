<?php
/**
 * Plugin Name: Additional Content
 * Plugin URI:
 * Description: Add additional content before or after post content in single post pages. Additional content can be added or edited in the edit or publish post screen.
 * Author: keesiemijer
 * Author URI:
 * License: GPL v2
 * Author URI:
 * Version: 1.0
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

// Includes Files
require plugin_dir_path( __FILE__ ) . 'includes/install.php';

if ( !class_exists( 'WPUpdatePhp' ) ) {
	require plugin_dir_path( __FILE__ ) . 'includes/WPUpdatePhp.php';
}

// Aim high :)
$updatePhp = new WPUpdatePhp( '5.4.0' );

if ( $updatePhp->does_it_meet_required_php_version( PHP_VERSION ) ) {

	// Get Additional Content Running.
	keesiemeijer\Additional_Content\additional_content();
}