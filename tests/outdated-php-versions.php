<?php
namespace keesiemeijer\Additional_Content;
use \WP_UnitTestCase;

class Outdated_PHP_Tests extends WP_UnitTestCase {

	function test_if_plugin_installed() {
		// function keesiemeijer\Additional_Content\\get_defaults should not exist.
		$this->assertFalse( function_exists( 'get_defaults' ) );
	}

}
