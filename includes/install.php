<?php
namespace keesiemeijer\Additional_Content;

/**
 * Install
 *
 * @package     Additional Content
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2015, Kees Meijer
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Let's get Additional Content Running.
additional_content_init();


/**
 * Loads the plugin's text domain and includes files.
 *
 * @since 1.0
 * @return void.
 */
function additional_content_init() {

	$dir = dirname( plugin_basename( ADDITIONAL_CONTENT_PLUGIN_FILE ) ) . '/languages/';
	load_plugin_textdomain( 'additional-content', '', $dir );

	// Yay for PHP >= 5.4 and Composer
	require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * instantiate public class on single post pages
 *
 * @since 1.3 
 * @return void
 */
function single_init(){
	if( is_single() || is_page() ) {
		$additional_content = new AC_Public();
	}
}

add_action( 'wp', __NAMESPACE__ . '\\single_init', 99 );


/**
 * Include files in edit and new post screens.
 *
 * @since 1.0
 * @return void
 */
function metabox_includes() {

	$screen = get_current_screen();

	if ( !isset( $screen->post_type ) ) {
		return;
	}

	$public_post_types = get_post_types( array( 'public' => true ), 'names', 'and' );
	unset( $public_post_types['attachment'] );

	if ( in_array( $screen->post_type, array_keys( $public_post_types ) ) ) {
		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/scripts.php';
		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/metaboxes.php';
	}
}

add_action( 'load-post.php',     __NAMESPACE__ . '\\metabox_includes' );
add_action( 'load-post-new.php', __NAMESPACE__ . '\\metabox_includes' );