<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Save_Metabox_Tests extends WP_UnitTestCase {

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new Test_Utils( $this->factory );
	}


	public static function setUpBeforeClass() {
		// The unction that includes the metabox file is only included in post.php and post-new.php
		set_current_screen( 'post-new.php' );
	}


	function test_empty_additional_content() {

		$this->utils->setup_post_screen();

		$post_id = $this->factory->post->create();
		$post_data = $this->utils->get_post_data( $post_id );

		// set additional post meta
		update_post_meta( $post_id, '_ac_additional_content', array( $this->utils->additional1 ) );

		// remove additional content.
		$this->utils->additional1['additional_content'] = '';
		$post_data['ac_additional_content'][] = $this->utils->additional1;

		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$this->assertEmpty( $additional );
	}


	function test_wrong_format_additional_content() {

		$this->utils->setup_post_screen();

		$post_id = $this->factory->post->create();
		$post_data = $this->utils->get_post_data( $post_id );

		// set additional post meta with correct format
		update_post_meta( $post_id, '_ac_additional_content', array( $this->utils->additional1 ) );

		// Wrong format
		$post_data['ac_additional_content'] = 'string';

		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$this->assertEmpty( $additional );
	}


	function test_order_by_priority() {

		$this->utils->setup_post_screen();

		$post_id = $this->factory->post->create();
		$post_data = $this->utils->get_post_data( $post_id );

		$additional1 = $this->utils->additional1;
		$additional2 = $this->utils->additional2;


		$post_data['ac_additional_content'][] = $additional1;
		$post_data['ac_additional_content'][] = $additional2;

		$_POST = _wp_translate_postdata( false, $post_data );

		// saves the additional content metabox data for the post
		save_metabox( $post_id );

		$additional = get_post_meta( $post_id, '_ac_additional_content', true );

		$additional1['prepend'] = '';
		$additional2['append']  = '';

		// additional2 has a lower priority and should come first
		$expected = array( $additional2, $additional1 );

		$this->assertNotEmpty( $additional );
		$this->assertEquals( $expected, $additional );
	}
}