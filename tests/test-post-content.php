<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Post_Content_Tests extends WP_UnitTestCase {

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new Test_Utils( $this->factory );
	}


	function test_post_content() {

		// create a post
		$post_id = $this->factory->post->create();

		$meta = array(
			$this->utils->additional1,
			$this->utils->additional2
		);

		//set additional post meta
		update_post_meta( $post_id, '_ac_additional_content', $meta );

		// go to the single post page
		$this->go_to( get_permalink( $post_id ) );

		// get the post content
		ob_start();
		the_post();
		the_content();
		$content = ob_get_clean();

		$expected = '
		<p>World!</p>
		<p>Post content 1</p>
		<p>Hello</p>';

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}

}