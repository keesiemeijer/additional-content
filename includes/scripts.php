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
		ADDITIONAL_CONTENT_PLUGIN_URL . 'includes/assets/js/additional-content.min.js',
		array( 'jquery', 'wp-util', 'jquery-ui-sortable', 'jquery-color' ),
		false,
		true );

	wp_enqueue_script( 'additional_content' );

	$text = metabox_text();

	$js_vars = array(
		'hide_options'   => __( 'hide options', 'additional-content' ),
		'show_options'   => __( 'show options', 'additional-content' ),
		'add_row'        => $text['add_row'],
		'add_more_row'   => $text['add_more_row'],
		'remove_row'     => $text['remove_row'],
		'content'        => $text['content'],
		'append'         => $text['append_content'],
		'prepend'        => $text['prepend_content'],
		'append_prepend' => $text['prepend_append_content'],
	);

	wp_localize_script( 'additional_content', 'ac_additional_content', $js_vars );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts', 99 );