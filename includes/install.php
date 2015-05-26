<?php
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
 * Include files in edit and new post screens.
 *
 * @since 1.0
 * @return void
 */
function ac_install_additional_content() {

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

add_action( 'load-post.php',     'ac_install_additional_content' );
add_action( 'load-post-new.php', 'ac_install_additional_content' );