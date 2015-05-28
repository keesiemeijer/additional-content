<?php
namespace keesiemeijer\Additional_Content;

/**
 * Scripts
 * This file is only included on the post.php and post-new.php pages.
 *
 * @package     Additional Content
 * @subpackage  Functions/Scripts
 * @copyright   Copyright (c) 2015, Kees Meijer
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues scripts in the edit post screen.
 *
 * @since 1.0
 * @return void
 */
function enqueue_scripts() {

	wp_register_script(
		'additional_content',
		ADDITIONAL_CONTENT_PLUGIN_URL . 'assets/js/additional-content.min.js',
		array( 'jquery', 'jquery-ui-sortable', 'jquery-color' ),
		false,
		true );

	wp_enqueue_script( 'additional_content' );

	$text = metabox_text();

	$js_vars = array(
		'add_row'        => __( 'Add additional content', 'additional-content' ),
		'add_more_row'   => __( 'Add more additional content', 'additional-content' ),
		'remove_row'     => __( 'Remove', 'additional-content' ),
		'hide_options'   => __( 'hide options', 'additional-content' ),
		'show_options'   => __( 'show options', 'additional-content' ),
		'content'        => $text['content'],
		'append'         => $text['append_content'],
		'prepend'        => $text['prepend_content'],
		'append_prepend' => $text['prepend_append_content'],
	);

	wp_localize_script( 'additional_content', 'ac_additional_content', $js_vars );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts', 99 );