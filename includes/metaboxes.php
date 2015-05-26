<?php
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
function additional_content_metabox_class() {
	return apply_filters( 'additional_content_metabox_row_classes', array(
			'append_prepend' => ' ac-row-show',
			'priority'       => ' ac-row-show',
		)
	);
}


/**
 * Returns the text strings used by the metabox
 *
 * @since 1.0
 * @return array Array with text strings.
 */
function additional_content_metabox_text() {
	$text =  array(
		'content'                => __( 'Content', 'additional-content' ),
		'prepend_content'        => __( 'Prepend Content', 'additional-content' ),
		'append_content'         => __( 'Append Content', 'additional-content' ),
		'prepend_append_content' => __( 'Prepend and Append Content', 'additional-content' ),
		'prepend'                => __( 'Prepend', 'additional-content' ),
		'append'                 => __( 'Append', 'additional-content' ),
		'priority'               => __( 'Priority', 'additional-content' ),
		'header_info'            => __( 'Add additional content to the post content on single post pages. ', 'additional-content' ),
		'priority_info'          => __( 'The priority gives you control over when additional content is added.', 'additional-content' )
		. ' ' . __( 'Default is 10.', 'additional-content' )
		//. ' ' . __( 'Higher numbers correspond with later execution in relation to plugins or themes that also add additional content.', 'additional-content' ),
		. ' ' . __( 'Content is added in order of priority', 'additional-content' )
	);

	return apply_filters( 'additional_content_metabox_text', $text );
}


/**
 * Returns the label text used for the content textarea.
 *
 * @since 1.0
 * @param array   $fields Array with field options.
 * @return string Label Text.
 */
function additional_content_label_text( $fields ) {
	$text = additional_content_metabox_text();

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
function additional_content_meta_boxes( $post_type ) {
	add_meta_box( 'additional-content', 'Additional Content', 'additional_content_meta_box', $post_type, 'normal', 'default' );
}

add_action( 'add_meta_boxes', 'additional_content_meta_boxes' );


/**
 * Display the additional content metabox.
 *
 * @since 1.0
 * @return void
 */
function additional_content_meta_box() {
	global $post;

	$additional = get_post_meta( $post->ID, '_ac_additional_content', true );
	$defaults   = ac_additional_content()->get_defaults();

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
			.ac-top-row {
				margin-top: 0;
			}
			.ac-row-hide {
				display: none;
			}
			.ac-row-show {
				display: block;
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

	$text  = additional_content_metabox_text();
	$class = additional_content_metabox_class();
	$class_options = ' js-visually-hidden';

	echo ( !empty( $text['header_info'] ) ) ? "<p>" . $text['header_info'] . '</p>' : '';

	wp_nonce_field( 'ac_additional_content_nonce', 'ac_additional_content_nonce' );

	echo '<div id="additional-content-container">';

	$i=0;
	if ( !empty( $additional ) ) {
		// Saved meta boxes.
		foreach ( $additional as $fields ) {
			$fields = array_merge( $defaults, $fields );
			$label  = additional_content_label_text( $fields );
			include 'partials/repeatable-fields.php';
			$i++;
		}

	} else {
		// Default meta box.
		$fields           = $defaults;
		$fields['append'] = 'on';
		$class_options    = '-none';
		$label            = $text['append_content'];
		include 'partials/repeatable-fields.php';
	}

	echo '</div>';
}


/**
 * Adds a repeatable field template for jQuery.
 *
 * @since 1.0
 * @return void
 */
function additional_content_footer_scripts() {
	$fields           = ac_additional_content()->get_defaults();
	$text             = additional_content_metabox_text();
	$class            = additional_content_metabox_class();
	$class_options    = '';
	$fields['append'] = 'on';
	$label            = $text['append_content'];
	$i = 0;
	echo '<script type="text/html" id="ac_additional_content_template">';
	include 'partials/repeatable-fields.php';
	echo '</script>';
}

add_action( 'admin_print_footer_scripts', 'additional_content_footer_scripts', 1 );


/**
 * Saves the settings from the additional content meta box.
 *
 * @since 1.0
 * @param int     Post id.
 * @return void
 */
function additional_content_save_metabox( $post_id ) {

	$nonce = ( isset( $_POST['ac_additional_content_nonce'] ) ) ?  $_POST['ac_additional_content_nonce'] : '';

	if ( empty( $nonce ) || !wp_verify_nonce( $nonce, 'ac_additional_content_nonce' ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$old      = get_post_meta( $post_id, '_ac_additional_content', true );
	$defaults = ac_additional_content()->get_defaults();
	$new      = array();

	if ( isset( $_POST['ac_additional_content'] ) ) {
		$new = $_POST['ac_additional_content'];
	}

	if ( !( !empty( $new ) && is_array( $new ) ) ) {
		if ( $old ) {
			delete_post_meta( $post_id, '_ac_additional_content' );
		}
		return;
	}

	// Validate the new settings
	foreach ( $new as $key => $setting ) {

		if ( !is_array( $setting ) ) {
			unset( $new[ $key ] );
			continue;
		}

		$setting = array_merge( $defaults, $setting );

		/**
		 * Filter html in additional content before it is saved to the database.
		 *
		 * @since 1.0
		 * @param bool    $filter_content Filter content. Default true
		 */
		$filter_content = apply_filters( 'ac_additional_content_filter_html', true, $setting, $post_id );

		if ( $filter_content ) {
			$setting['additional_content'] = wp_filter_post_kses( $setting['additional_content'] );
		}

		if ( '' === trim( $setting['additional_content'] ) ) {
			unset( $new[ $key ] );
			continue;
		}

		foreach ( array( 'prepend', 'append' ) as $addition ) {
			if ( 'on' !== $setting[ $addition ] ) {
				$setting[ $addition ] = $defaults[ $addition ];
			}
		}

		$setting['priority'] = absint( $setting['priority'] ) ? absint( $setting['priority'] ) : 10 ;

		$new[ $key ] = $setting;
	}

	$new = array_values( $new );

	if ( !empty( $new ) && $new != $old ) {

		// Order the new options by priority
		$priorities = additional_content_sort_by_priority( $new );
		$_new       = array();
		foreach ( $priorities as $priority ) {
			foreach ( $priority as $option ) {
				$_new[] = $option;
			}
		}

		update_post_meta( $post_id, '_ac_additional_content', $_new );
	} elseif ( empty( $new ) && $old ) {
		delete_post_meta( $post_id, '_ac_additional_content' );
	}
}

add_action( 'save_post', 'additional_content_save_metabox' );