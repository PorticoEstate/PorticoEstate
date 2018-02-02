<?php


	class FirstTest extends Codeception\Test\Unit
	{
		/**
		 * @var \UnitTester
		 */
		protected $tester;

		protected function _before()
		{
		}

		protected function _after()
		{
		}

		public function testSomeFeature()
		{
			$this->assertEquals(4, 4);
		}
	}