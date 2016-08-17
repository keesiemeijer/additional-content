<?php
namespace keesiemeijer\Additional_Content;

/**
 * Functions
 *
 * @package     Additional Content
 * @subpackage  Functions/Functions
 * @copyright   Copyright (c) 2015, Kees Meijer
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Sorts and groups additional content by priority and key.
 *
 * @since 1.0
 * @param array   $options Array with additional content options.
 * @return array  Sorted array grouped by priority.
 */
function sort_by_priority( $options = array() ) {
	$priority_options = array();

	// Add sorting _priority index with priority and key
	foreach (  array_values( $options ) as $key => $option ) {
		$options[ $key ]['_priority'] =  array( $option['priority'], $key );
	}

	uasort( $options, function( $a, $b ) {
			if ( $a['_priority'][0] != $b['_priority'][0] ) {
				// sort on priority
				return $a['_priority'][0] < $b['_priority'][0] ? -1 : 1;
			} else {
				// sort on key if priority is equal.
				return $a['_priority'][1] < $b['_priority'][1] ? -1 : 1;
			}
		} );

	foreach ( array_values( $options ) as $key => $option ) {
		// Remove _priority sorting index.
		unset( $option['_priority'] );

		// Group options by priority
		$priority_options[ $option['priority'] ][] = $option;
	}

	return array_values( $priority_options );
}


/**
 * Retuns content with additional content added.
 *
 * @since 1.0
 * @param string  $content Content to add additional content for.
 * @param integer $post_id Post id.
 * @return string  Content with the additional content added.
 */
function get_content( $content = '', $post_id = 0 ) {

	if ( !absint( $post_id ) ) {
		return $content;
	}

	$additional_content = new AC_Public( absint( $post_id ) );
	$content = $additional_content->get_additional_content( (string) $content );

	return $content;
}


/**
 * Checks if a string is empty.
 * String could be "0"
 *
 * @since 1.2
 * @param string  $content Content to check.
 * @return bool True if string is not empty.
 */
function is_empty_string( $content ) {

	if ( is_null( $content ) || '' === trim( $content ) ) {
		return true;
	}

	return false;
}


/**
 * Returns the defaults for additional content metabox fields.
 *
 * @since 1.0
 * @return array Array with defaults.
 */
function get_defaults() {
	return array(
		'append'             => '',
		'prepend'            => '',
		'additional_content' => '',
		'priority'           => 10,
	);
}

/**
 * Update the additional meta for a post ID.
 *
 * @since 1.1
 * @param int     $post_id  Post ID
 * @param [type]  $new_meta New additional meta to update
 * @return void
 */
function update_additional_meta( $post_id, $new_meta ) {

	$old_meta = get_post_meta( $post_id, '_ac_additional_content', true );

	if ( !( !empty( $new_meta ) && is_array( $new_meta ) ) ) {
		if ( $old_meta ) {
			// Delete old meta if new meta is empty.
			delete_post_meta( $post_id, '_ac_additional_content' );
		}
		return;
	}

	$defaults = get_defaults();

	// Validate the new settings
	foreach ( $new_meta as $key => $setting ) {

		if ( !is_array( $setting ) ) {
			unset( $new_meta[ $key ] );
			continue;
		}

		$setting        = array_merge( $defaults, $setting );

		// Filter content for users without the unfiltered_html capability.
		$filter_content = current_user_can( 'unfiltered_html' ) ? false : true;

		/**
		 * Filter html in additional content before it is saved to the database. 
		 *
		 * @since 1.0
		 * @param bool    $filter_content Filter content. True for users without the unfiltered_html capability.
		 */
		$filter_content = apply_filters( 'ac_additional_content_filter_html', $filter_content, $setting, $post_id );

		if ( $filter_content ) {
			$setting['additional_content'] = wp_filter_post_kses( $setting['additional_content'] );
		}

		if ( is_empty_string( $setting['additional_content'] ) ) {
			unset( $new_meta[ $key ] );
			continue;
		}

		foreach ( array( 'prepend', 'append' ) as $addition ) {
			if ( 'on' !== $setting[ $addition ] ) {
				$setting[ $addition ] = $defaults[ $addition ];
			}
		}

		$setting['priority'] = absint( $setting['priority'] ) ? absint( $setting['priority'] ) : 10 ;

		$new_meta[ $key ] = $setting;
	}

	$new_meta = array_values( $new_meta );

	if ( !empty( $new_meta )  ) {

		$_new_meta  = array();
		$priorities = sort_by_priority( $new_meta );

		foreach ( $priorities as $priority ) {
			foreach ( $priority as $option ) {
				$_new_meta[] = $option;
			}
		}

		if ( $_new_meta != $old_meta ) {
			update_post_meta( $post_id, '_ac_additional_content', $_new_meta );
		}
	} elseif ( empty( $new_meta ) && $old_meta ) {
		delete_post_meta( $post_id, '_ac_additional_content' );
	}
}
