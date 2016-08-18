<?php
namespace keesiemeijer\Additional_Content;

/**
 * Public
 *
 * @package     Additional Content
 * @subpackage  Classes/AC_Additional_Content_Public
 * @copyright   Copyright (c) 2015, Kees Meijer
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to add the additional content in single post pages.
 */
class AC_Public {

	/**
	 * Additional content options.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $options = array();

	/**
	 * Additional content options single post pages.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $options_singular = array();


	public function __construct( $post_id = 0 ) {

		if ( !$post_id ) {
			$this->setup_single_options();
		} else {
			// Setup additional content for a post id.
			$this->setup_options( $post_id );
		}
	}


	/**
	 * Sets up the additional content for single post pages.
	 *
	 * @since 1.0
	 * @return void
	 */
	private function setup_single_options() {

		$singular = is_single() || is_page();

		/**
		 * Setup additional content for singular post pages content or not.
		 *
		 * @since 1.0
		 * @param bool    $singular True when is_singular() returns true.
		 */
		$singular = apply_filters( 'additional_content_add_content', $singular );

		if ( $singular ) {
			$this->setup_options();
		}
	}

	/**
	 * Sets up the additional content options.
	 *
	 * @since 1.0
	 * @param integer $post_id (Optional) Post ID. If provided no filters arre added to the_content
	 * @return void
	 */
	private function setup_options( $post_id = 0 ) {

		$this->options          = array();
		$post_id                = absint( $post_id );
		$add_filters            = true;

		if ( $post_id ) {
			// Don't add filters.
			$add_filters = false;
		} else {
			$post_id = get_the_ID();
		}

		if ( empty( $post_id ) ) {
			return;
		}

		// Get the additional content options from the database.
		$options = get_post_meta( $post_id, '_ac_additional_content', true );

		if ( !( !empty( $options ) && is_array( $options ) ) ) {
			return;
		}

		$this->options = $this->validate_options( $options );

		if ( empty( $this->options ) ) {
			return;
		}

		// Get the priorities.
		$priorities = array_unique( wp_list_pluck( $this->options, 'priority' ) );

		// Sort and group options by priority.
		$this->options = sort_by_priority( $this->options );

		if ( $add_filters ) {

			// store singular options
			$this->options_singular = $this->options;
			$this->options          = array();

			// Add a filter to the_content for every priority.
			foreach ( $priorities as $priority ) {
				add_filter( 'the_content', array( $this, 'the_content' ), $priority );
			}
		}
	}


	/**
	 * Returns validated additional content options.
	 *
	 * @since 1.0
	 * @param array   $options Additional content options.
	 * @return array  Sanitized options.
	 */
	private function validate_options( $options = array() ) {

		$sanitized_options = array();
		$defaults = get_defaults();

		// Sanitize and validate the options.
		foreach ( $options as $option ) {

			if ( !is_array( $option ) ) {
				continue;
			}

			$option  = array_merge( $defaults, $option );

			// Continue if both prepend and append are empty.
			if ( empty( $option['prepend'] ) && empty( $option['append'] ) ) {
				continue;
			}

			// Continue if additional_content is empty.
			if ( is_empty_string( $option['additional_content'] ) ) {
				continue;
			}

			// Use the same filters as the filters applied to the_content.
			// This prevents filter recursion.
			$option['additional_content'] = apply_filters( 'the_additional_content', $option['additional_content'] );

			// Add the option after validation.
			$sanitized_options[] = $option;
		}

		return $sanitized_options;
	}


	/**
	 * Adds additional content to the current post content.
	 *
	 * @since 1.0
	 * @param string  $content Post content.
	 * @return string Post content with additional content.
	 */
	public function the_content( $content ) {

		// Check if we're inside the main loop in a single post page.
		if ( !( is_singular() && in_the_loop() && is_main_query() ) ) {
			return $content;
		}

		return $this->process_additional_content( $content, true );
	}


	/**
	 * Returns additional content if options are setup for a post id.
	 *
	 * @since 1.0
	 * @param string  $content Post content.
	 * @return string Content with additional content from all priorities added.
	 */
	public function get_additional_content( $content = '' ) {

		// check if options where set up.
		if ( empty( $this->options ) ) {
			return $content;
		}

		foreach ( (array) $this->options as $priority ) {

			if ( !array( $priority ) ) {
				continue;
			}

			foreach ( $priority as $option ) {
				$content = $this->process_additional_content( $content );
			}
		}

		return $content;
	}


	/**
	 * Prepends or appends content for a priority.
	 *
	 * @since 1.0
	 * @param string  $content Post content.
	 * @return string Post content with additional content from a priority added.
	 */
	public function process_additional_content( $content, $singular = false ) {

		$options = $singular ? $this->options_singular : $this->options;

		// The priority options are grouped by priority.
		// Check if a first (priority) option exists.
		if ( !( isset( $options[0] ) && !empty( $options[0] ) ) ) {
			return  $content;
		}

		$prepend  = '';

		// Loop through the options for a priority.
		foreach ( $options[0] as $option ) {

			$additional_content = $option['additional_content'];

			if ( 'on' === $option['append'] ) {
				$content .= $additional_content;
			}

			if ( 'on' === $option['prepend'] ) {
				$prepend = $additional_content . $prepend;
			}
		}

		$content = $prepend . $content;

		// Remove the first priority option group after processing.
		if ( $singular ) {
			array_shift( $this->options_singular );
		} else {
			array_shift( $this->options );
		}

		return $content;
	}

}