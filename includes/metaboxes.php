<?php
namespace keesiemeijer\Additional_Content;
/**
 * Meta boxes
 *
 * @package     Additional Content
 * @subpackage  Functions/Metaboxes
 * @copyright   Copyright (c) 2015, Kees Meijer
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns classes for the rows.
 *
 * @since 1.0
 * @return array Array with classes for all rows.
 */
function metabox_classes() {
	$classes  = array();
	$defaults = array( 'append_prepend' => true, 'priority' => true );

	/**
	 * Display options or not
	 *
	 * @since 1.0
	 * @param array   Array with options.
	 */
	$options  = apply_filters( 'additional_content_metabox_options', $defaults );
	$options  = array_merge( $defaults, $options );

	foreach ( $options as $key => $option ) {
		$classes[ $key ] = $option ? 'ac-option' : 'ac-option-hide';
	}

	return $classes;
}


/**
 * Returns the text strings used by the metabox
 *
 * @since 1.0
 * @return array Array with text strings.
 */
function metabox_text() {

	/**
	 * Text strings for the additional content metabox.
	 *
	 * @since 1.0
	 * @param Array   with text strings for the metabox.
	 */
	$text =  apply_filters( 'additional_content_metabox_text', array(
			'title'                  => __( 'Additional Content', 'additional-content' ),
			'content'                => __( 'Content', 'additional-content' ),
			'prepend_content'        => __( 'Prepend Content', 'additional-content' ),
			'append_content'         => __( 'Append Content', 'additional-content' ),
			'prepend_append_content' => __( 'Prepend and Append Content', 'additional-content' ),
			'prepend'                => __( 'Prepend', 'additional-content' ),
			'append'                 => __( 'Append', 'additional-content' ),
			'priority'               => __( 'Priority', 'additional-content' ),
			'add_row'                => __( 'Add additional content', 'additional-content' ),
			'add_more_row'           => __( 'Add more additional content', 'additional-content' ),
			'remove_row'             => __( 'Remove', 'additional-content' ),
			'header_info'            => __( 'Display additional content in single posts pages.', 'additional-content' ),
			'priority_info'          => __( 'The priority gives you control over when additional content is displayed.', 'additional-content' )
			. ' ' . __( 'Default is 10.', 'additional-content' )
			//. ' ' . __( 'Higher numbers correspond with later execution in relation to plugins or themes that also add additional content.', 'additional-content' ),
			. ' ' . __( 'Content is displayed in order of priority', 'additional-content' )
		)
	);

	return $text;
}


/**
 * Metabox defaults.
 *
 * @since 1.1.3
 * @return array Array with metabox defaults.
 */
function metabox_defaults() {
	$defaults = $_defaults = get_defaults();

	// Default for new metaboxes is append.
	$_defaults['append'] = 'on';

	/**
	 * Add new defaults for the metabox options.
	 *
	 * @since 1.1.3
	 * @param array   $defaults Array with default metabox options.
	 */
	$_defaults = apply_filters( 'additional_content_metabox_defaults', $_defaults );
	$defaults  = array_merge( $defaults, $_defaults );

	if ( !( empty( $defaults['append'] ) && empty( $defaults['prepend'] ) ) ) {
		$defaults['append'] = 'on';
	}

	return $defaults;
}


/**
 * Returns the label text used for the additional content textarea.
 *
 * @since 1.0
 * @param array   $fields Array with field options.
 * @return string Label Text.
 */
function label_text( $fields ) {
	$text  = metabox_text();
	$label = $text['content'];

	if ( !empty( $fields['append'] ) && !empty( $fields['prepend'] ) ) {
		$label = $text['prepend_append_content'];
	} elseif ( !empty( $fields['append'] ) ) {
		$label = $text['append_content'];
	} elseif ( !empty( $fields['prepend'] ) ) {
		$label = $text['prepend_content'];
	}

	return $label;
}


/**
 * Add the additional content metabox in the post edit screen.
 *
 * @since 1.0
 * @param string  Post type.
 * @return void
 */
function add_meta_boxes( $post_type ) {

	$text = metabox_text();

	add_meta_box( 'additional-content', $text['title'], __NAMESPACE__ . '\\meta_box', $post_type, 'normal', 'default' );
}

add_action( 'add_meta_boxes', __NAMESPACE__ . '\\add_meta_boxes' );


/**
 * Display the additional content metabox.
 *
 * @since 1.0
 * @return void
 */
function meta_box() {
	global $post;

	$additional       = get_post_meta( $post->ID, '_ac_additional_content', true );
	$defaults         = get_defaults();
	$metabox_defaults = metabox_defaults();
	$text             = metabox_text();
	$class            = metabox_classes();
	$visible          = ' js-visually-hidden';
	$i                = 0;

	// Start the output of the metabox

	echo "<style type='text/css'>
			#additional-content-container > div {
				background-color: #F9F9F9;
				border: 1px solid #DFDFDF;
				padding: 8px;
				margin: 1em 0;
			}
			#additional-content-container > .sortable-placeholder {
				background-color: #fff;
				border: 1px dashed #b4b9be;
		    }
			.ac-option {
				display: block;
			}
			.ac-option-content {
				margin-top: 0;
				margin-bottom: .5em;
			}
			.ac-option-hide {
				display: none;
			}
			.js .js-visually-hidden {
				border: 0;
				clip: rect(0 0 0 0);
				height: 1px;
				margin: -1px;
				overflow: hidden;
				padding: 0;
				position: absolute;
				width: 1px;
			}
			</style>";


	$header      = ( !empty( $text['header_info'] ) ) ? "<p id='ac_info'>" . $text['header_info'] . '</p>' : '';
	$describedby = ( !empty( $header ) ) ? ' aria-describedby="ac_info"' : '';
	echo $header;

	wp_nonce_field( 'ac_additional_content_nonce', 'ac_additional_content_nonce' );

	echo '<div id="additional-content-container" role="region" aria-live="polite" aria-relevant="additions removals">';

	if ( !empty( $additional ) ) {
		// Saved meta boxes.
		foreach ( $additional as $fields ) {
			$fields = array_merge( $defaults, $fields );
			$label  = label_text( $fields );
			include 'partials/repeatable-fields.php';
			$i++;
		}

	} else {
		// New meta box.
		$fields           = $metabox_defaults;
		$visible          = ' js-no-toggle';
		$label            = label_text( $fields );
		include 'partials/repeatable-fields.php';
	}

	$add_row = get_transient( 'additional_content_add_empty_row' );

	if ( $add_row ) {
		// Javascript is disabled. Let's add an empty metabox
		delete_transient( 'additional_content_add_empty_row' );

		$i++;
		$fields           = $metabox_defaults;
		$visible          = '';
		$label            = label_text( $fields );
		include 'partials/repeatable-fields.php';
	}

	echo '</div>';
	echo '<p><input id="ac-add-row" class="button" type="submit" value="' . $text['add_more_row'] . '" name="ac-add_more"></p>';
}


/**
 * Adds a repeatable field template for jQuery.
 *
 * @since 1.0
 * @return void
 */
function admin_footer_scripts() {
	$fields           = metabox_defaults();
	$text             = metabox_text();
	$class            = metabox_classes();
	$visible          = '';
	$label            = label_text( $fields );
	$i                = 0;
	$describedby = ( !empty( $text['header_info'] ) ) ? ' aria-describedby="ac_info"' : '';

	echo '<script type="text/html" id="ac_additional_content_template">';
	include 'partials/repeatable-fields.php';
	echo '</script>';
}

add_action( 'admin_print_footer_scripts', __NAMESPACE__ . '\\admin_footer_scripts', 1 );


/**
 * Updates additional content if Javascript is disabled.
 * Updates when remove or add more butten was submitted.
 *
 * @since 1.1
 * @param string  $location The destination URL.
 * @param int     $post_id  The post ID.
 * @return string The destination URL.
 */
function redirect_filter( $location, $post_id ) {

	if ( !validate_metabox( $post_id ) ) {
		return $location;
	}

	if ( isset( $_POST['ac_additional_content'] ) && $_POST['ac_additional_content'] ) {
		$additionals = $_POST['ac_additional_content'];
		foreach ( $additionals as $key => $additional ) {
			if ( isset( $additional['remove'] ) ) {
				unset( $additionals[ $key ] );
			}
		}

		if ( current_user_can( 'edit_post', $post_id ) ) {
			// Update the post meta before the metabox is displayed
			update_additional_meta( $post_id, $additionals );
		}

	}

	if ( isset( $_POST['ac-add_more'] ) && $_POST['ac-add_more'] ) {
		set_transient( 'additional_content_add_empty_row', 1, MINUTE_IN_SECONDS * 5 );
	}

	return $location;
}

add_filter( 'redirect_post_location', __NAMESPACE__ . '\\redirect_filter', 10, 2 );


/**
 * Saves the settings from the additional content meta box.
 *
 * @since 1.0
 * @param int     Post id.
 * @return void
 */
function save_metabox( $post_id ) {

	if ( !validate_metabox( $post_id ) ) {
		return $post_id;
	}

	$new = array();
	if ( isset( $_POST['ac_additional_content'] ) ) {
		$new = $_POST['ac_additional_content'];
	}

	update_additional_meta( $post_id, $new );
}

add_action( 'save_post', __NAMESPACE__ . '\\save_metabox' );


/**
 * Checks nonce and user capabilities.
 *
 * @since 1.0
 * @param int     $post_id Post id.
 * @return boolean True if nonce and user capabilities are correct
 */
function validate_metabox( $post_id = 0 ) {

	$nonce = ( isset( $_POST['ac_additional_content_nonce'] ) ) ?  $_POST['ac_additional_content_nonce'] : '';

	if ( empty( $nonce ) || !wp_verify_nonce( $nonce, 'ac_additional_content_nonce' ) ) {
		return false;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return false;
	}

	return true;
}