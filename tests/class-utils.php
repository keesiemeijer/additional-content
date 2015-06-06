<?php
namespace keesiemeijer\Additional_Content;

class Test_Utils {

	private $factory;

	public $additional1 = array(
		'additional_content' => 'Hello',
		'append'             => 'on',
		'priority'           => 12,
	);

	public $additional2 = array(
		'additional_content' => 'World!',
		'prepend'            => 'on',
		'priority'           => 10,
	);

	function __construct( $factory = null ) {
		$this->factory = $factory;
	}

	function setup_post_screen() {

		// set current user
		$user = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user );

		// includes the metaboxes.php file
		metabox_includes();
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

}