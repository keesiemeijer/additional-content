<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Misc_Tests extends WP_UnitTestCase {

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new Test_Utils( $this->factory );
	}


	function test_output() {

		// Create a post.
		$post_id = $this->factory->post->create();
		$meta = $this->utils->additional1;

		// Add additional post meta to the post.
		update_post_meta( $post_id, '_ac_additional_content', array( $meta ) );
		$meta['additional_content'] = 'new_content';

		// The following functions should not output anything.
		ob_start();

		$sort     = sort_by_priority( array( $meta ) );
		$string   = is_empty_string( 'string' );
		$defaults = get_defaults();
		$update   = update_additional_meta( $post_id, array( $meta ) );

		// Uses the AC_Public class.
		$content  = get_content( 'content', $post_id );

		// post-new screen
		set_current_screen( 'post-new.php' );
		$this->utils->setup_post_screen();

		$classes  = metabox_classes();
		$text     = metabox_text();
		$label    = label_text( array( $meta ) );

		$out = ob_get_clean();

		$this->assertEmpty( $out );
	}
}