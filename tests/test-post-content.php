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

		add_shortcode( 'shortcode_content', array( $this, 'shortcode_content' ) );
	}

	function shortcode_content( $atts ) {
		return 'shortcode content';
	}

	function test_get_content() {
		// Creates a post with content "Post content 1".
		$post_id = $this->factory->post->create();

		$meta = array(
			$this->utils->additional1,
			$this->utils->additional2
		);

		// Add additional post meta to the post.
		update_post_meta( $post_id, '_ac_additional_content', $meta );

		$content = get_content( '<p>Wonderful</p>', $post_id );

		$expected = '<p>World!</p>
		<p>Wonderful</p><p>Hello</p>';

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}


	function test_get_content_no_post_id() {

		$content = get_content( '<p>Wonderful</p>' );
		$expected = '<p>Wonderful</p>';

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}


	function test_single_post_content() {

		// Creates a post with content "Post content 1".
		$post_id = $this->factory->post->create();

		$meta = array(
			$this->utils->additional1,
			$this->utils->additional2
		);

		// Add additional post meta to the post.
		update_post_meta( $post_id, '_ac_additional_content', $meta );
		$post_content = get_post_field( 'post_content', $post_id );

		// Go to the the single post page.
		$this->go_to( get_permalink( $post_id ) );
		$this->assertTrue( is_single() );

		// Get the post content.
		ob_start();
		the_post();
		the_content();
		$content = ob_get_clean();

		$expected = '
		<p>World!</p>
		<p>' .  $post_content . '</p>
		<p>Hello</p>';

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}


	function test_home_post_content() {

		// Creates 5 posts for the home page.
		$posts = $this->factory->post->create_many( 5 );

		$post_id = $posts[2];
		$post_content = get_post( $post_id )->post_content;

		$meta = array(
			$this->utils->additional1,
			$this->utils->additional2
		);

		// Add additional post meta to the post.
		update_post_meta( $post_id, '_ac_additional_content', $meta );

		add_filter( 'the_content', array( $this, 'content_home_page' ) );

		$this->go_to( '/' );

		// Set is_home to true.
		global $wp_query;
		$wp_query->is_admin = '';
		$wp_query->is_home  = true;

		$this->assertTrue( is_home() );

		// Get the home post content.
		ob_start();
		while ( have_posts() ) {
			the_post();
			global $post;
			if ( $post->ID === $post_id ) {
				the_content();
			}
		}
		rewind_posts();
		$content = ob_get_clean();

		$expected = '
		<p>World!</p>
		<p>' . $post_content . '</p>
		<p>Hello</p>';

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );

		// Get the post content for a post that doesn't have additional meta.
		$post_id = $posts[3];
		$post_content = get_post( $post_id )->post_content;

		// Get the home post content.
		ob_start();
		while ( have_posts() ) {
			the_post();
			global $post;
			if ( $post->ID === $post_id ) {
				the_content();
			}
		}
		rewind_posts();
		$content = ob_get_clean();

		$expected = '<p>' . $post_content . '</p>';
		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}

	function test_post_additional_content_shortcode() {

		$blob =<<<BLOB
Post content

[shortcode_content]
BLOB;
		$meta = $this->utils->additional1;
		$post_id_1 = $this->factory->post->create( array( 'post_content' => $blob ) );
		$post_id_2 = $this->factory->post->create( array( 'post_content' => 'Post content' ) );

		$meta = $this->utils->additional1;
		$meta['priority'] = 10;
		$meta['additional_content'] = '[shortcode_content]';
		update_post_meta( $post_id_2, '_ac_additional_content', array( $meta ) );

		// Go to the the single post 1 page.
		$this->go_to( get_permalink( $post_id_1 ) );

		// Get the post content.
		ob_start();
		the_post();
		the_content();
		$content_1 = ob_get_clean();
		rewind_posts();

		// Go to the the single post 2 page.
		$this->go_to( get_permalink( $post_id_2 ) );

		// Get the post content.
		ob_start();
		the_post();
		the_content();
		$content_2 = ob_get_clean();

		$this->assertEquals( strip_ws( $content_1 ), strip_ws( $content_2 ) );
	}


	// Example from Github page.
	function content_home_page( $content ) {

		// Check if we're on the home page.
		if ( !is_home() ) {
			return $content;
		}

		// Check if this is the main query and inside the loop.
		if ( in_the_loop() && is_main_query() ) {

			// Check if the plugin function get_content() exists.
			if ( function_exists( 'keesiemeijer\Additional_Content\\get_content' ) ) {
				// Add the additional content to the post content with the get_content() function.

				// Content and post id are required.
				// Post id is available inside the loop.
				$content = get_content( $content, get_the_ID() );
			}
		}

		return $content;
	}
}
