<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$response = $this->route('GET', 'login');

		$this->assertEquals(200, $response->getStatusCode());
	}

}
