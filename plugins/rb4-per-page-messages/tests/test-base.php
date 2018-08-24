<?php

class BaseTest extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'rb4_per_page_messages') );
	}
	
	function test_get_instance() {
		$this->assertTrue( rb4_per_page_messages() instanceof rb4_per_page_messages );
	}
}
