<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Functions_Tests extends WP_UnitTestCase {

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new Test_Utils( $this->factory );
	}

	function test_sort_by_priority() {

		$options    = array();
		$priorities = array( 15, 10, 10, 9, 14, 15);
		$defaults   = get_defaults();

		// Create options with (numeric) content and priorities
		for ($i=0; $i < count( $priorities ); $i++) {

			$options[] = array_merge( $defaults, array(
				'content' => $i, 'priority' => $priorities[ $i ] )
			);
		}

		// sort options by priority
		$priorities = sort_by_priority( $options );

		$content = '';

		// get content from options
		foreach ( $priorities as $priority ) {
			foreach ( $priority as $option ) {
				$content .= $option['content'] . ',';
			}
		}

		$this->assertEquals( '3,1,2,4,0,5',  trim( $content, ',' ) );
	}

}