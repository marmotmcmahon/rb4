<?php

class BaseTest extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'RB4_Show_Seasonal_Block') );
	}
	
	function test_get_instance() {
		$this->assertTrue( rb4_show_seasonal_block() instanceof RB4_Show_Seasonal_Block );
	}
}
