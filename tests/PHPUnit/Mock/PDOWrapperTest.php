<?php

use Chevron\DB\Mock;

class MockPDOWrapperTest extends PHPUnit_Framework_TestCase {

	function getDbConn(){
		return new Mock\PDOWrapper;
	}

	function test_put(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->put("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_insert(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->insert("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_update(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->update("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_replace(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->replace("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_on_duplicate_key(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->on_duplicate_key("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_multi_insert(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->multi_insert("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_multi_replace(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->multi_replace("", array(), array());
		$this->assertEquals($expected, $result);

	}

	function test_exe(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->exe("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_assoc(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->assoc("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_row(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->row("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_scalar(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->scalar("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_scalars(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->scalars("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_keypair(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->keypair("", array(), true);
		$this->assertEquals($expected, $result);

	}

	function test_keyrow(){

		$dbConn = $this->getDbConn();

		$expected = 8675309;
		$dbConn->next($expected);
		$result = $dbConn->keyrow("", array(), true);
		$this->assertEquals($expected, $result);

	}

}