<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Save_Metabox_Tests extends WP_UnitTestCase {

	private $additional1 = array(
		'additional_content' => 'Hello',
		'append'             => 'on',
		'priority'           => 12,
	);

	private $additional2 = array(
		'additional_content' => 'World!',
		'prepend'            => 'on',
		'priority'           => 10,
	);


	public static function setUpBeforeClass() {
		// The unction that includes the metabox file is only included in post.php and post-new.php
		set_current_screen( 'post-new.php' );
	}


	function setup_post() {

		// set current user
		$user = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user );

		// includes the metaboxes.php file
		metabox_includes();

		// create a post
		$post_id = $this->factory->post->create();

		return $post_id;
	}


	function get_post_data( $post_id ) {
		return array(
			'post_title' => 'Post title',
			'content' => 'Post content',
			'post_ID' => $post_id,
			'post_type' => 'post',
			'ac_additional_content_nonce' => wp_create_nonce( 'ac_additional_content_nonce' ),
		);
	}


	function test_empty_additional_content() {

		$post_id = $this->setup_post();
		$post_data = $this->get_post_data( $post_id );

		// set additional post meta
		update_post_meta( $post_id, '_ac_additional_content', array( $this->additional1 ) );
		
		// remove additional content.
		$this->additional1['additional_content'] = '';
		$post_data['ac_additional_content'][] = $this->additional1;
		
		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$this->assertEmpty( $additional );
	}


	function test_wrong_format_additional_content() {

		$post_id = $this->setup_post();
		$post_data = $this->get_post_data( $post_id );

		// set additional post meta with correct format
		update_post_meta( $post_id, '_ac_additional_content', array( $this->additional1 ) );
		
		// Wrong format
		$post_data['ac_additional_content'] = 'string';
		
		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$this->assertEmpty( $additional );
	}


	function test_order_by_priority() {

		$post_id = $this->setup_post();
		$post_data = $this->get_post_data( $post_id );

		$post_data['ac_additional_content'][] = $this->additional1;
		$post_data['ac_additional_content'][] = $this->additional2;

		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$this->additional1['prepend'] = '';
		$this->additional2['append']  = '';

		// additional2 has a lower priority and should come first
		$expected = array( $this->additional2, $this->additional1 );

		$this->assertNotEmpty( $additional );
		$this->assertEquals( $expected, $additional );
	}
}