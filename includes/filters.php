<?php
namespace keesiemeijer\Additional_Content;
/**
 * Filters
 *
 * @package     Additional Content
 * @subpackage  Filters
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_embed;

/**
 * Concept for applying plugin filters for the_content. 
 * Copied from the Whistles plugin by Justin Tadlock.
 * https://github.com/justintadlock/whistles
 * 
 * Use the same default filters as 'the_content' with a little more flexibility.
 */
add_filter( 'the_additional_content', array( $wp_embed, 'run_shortcode' ),   5 );
add_filter( 'the_additional_content', array( $wp_embed, 'autoembed'     ),   5 );
add_filter( 'the_additional_content',                   'wptexturize',       10 );
add_filter( 'the_additional_content',                   'convert_smilies',   15 );
add_filter( 'the_additional_content',                   'convert_chars',     20 );
add_filter( 'the_additional_content',                   'wpautop',           25 );
add_filter( 'the_additional_content',                   'shortcode_unautop', 30 );
add_filter( 'the_additional_content',                   'do_shortcode',      35 );