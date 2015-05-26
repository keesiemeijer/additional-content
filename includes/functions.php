<?php
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
 * Sorts and groups the options array by priority.
 *
 * @since 1.0
 * @param array   $options Array with additional content options.
 * @return array  Sorted array grouped by priority.
 */
function additional_content_sort_by_priority( $options = array() ) {
	$priority_options = array();

	// Add sorting _priority index with priority and key
	foreach (  array_values( $options ) as $key => $option ) {
		$options[ $key ]['_priority'] =  array( $option['priority'], $key );
	}

	// Sort by priority and key
	uasort(  $options, 'additional_content_sort' );

	foreach ( array_values( $options ) as $key => $option ) {
		// Remove _priority sorting index.
		unset( $option['_priority'] );

		// Group options by priority
		$priority_options[ $option['priority'] ][] = $option;
	}

	return array_values( $priority_options );
}


/**
 * Callback function for usort() to sort by priority and array key.
 *
 * @since 1.0
 * @param array
 * @param array
 * @return int
 */
function additional_content_sort( $a, $b ) {
	if ( $a['_priority'][0] != $b['_priority'][0] ) {
		// sort on priority
		return $a['_priority'][0] < $b['_priority'][0] ? -1 : 1;
	} else {
		// sort on key if priority is equal.
		return $a['_priority'][1] < $b['_priority'][1] ? -1 : 1; // ASC
	}
}


/**
 * Retuns content with additional content added.
 *
 * @since 1.0
 * @param string  $content Content to add additional content for.
 * @param integer $post_id Post id.
 * @return string  Content with the additional content added.
 */
function additional_content_get_content( $content = '', $post_id = 0 ) {

	if ( !absint( $post_id ) ) {
		return $content;
	}

	$additional_content = new AC_Additional_Content_Public( absint( $post_id ) );
	$content = $additional_content->get_additional_content( (string) $content );

	return $content;
}