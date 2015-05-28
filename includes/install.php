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


/**
 * Loads the plugin's text domain and includes files.
 *
 * @since 1.0
 * @return void.
 */
function additional_content() {

	$dir = dirname( plugin_basename( ADDITIONAL_CONTENT_PLUGIN_FILE ) ) . '/languages/';
	load_plugin_textdomain( 'additional-content', '', $dir );

	require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/functions.php';

	if ( !is_admin() ) {
		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/filters.php';
		require_once ADDITIONAL_CONTENT_PLUGIN_DIR . 'includes/class-public.php';
	}
}


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