<?php

use \Chevron\DB;

class NullWrapperTest extends PHPUnit_Framework_TestCase {

	/*
	 * fixtures
	 */

	function getDbConn(){
		// $driver = new DB\Drivers\MySQL;
		return new DB\NullWrapper;
	}

	function test_insert_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->insert("test_table", array(
			"test_value"  => "tenth value",
			"test_score"  => 100,
		));

		$this->assertEquals(0, $num);

	}

	function test_replace_as_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->replace("test_table", array(
			"test_value"  => "replacement fourth value",
		));

		$this->assertEquals(0, $num);

	}

	function test_replace_as_replace(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->replace("test_table", array(
			"test_value"  => "replacement fourth value",
			"test_key" => 4,
		));

		$this->assertEquals(0, $num);

	}

	function test_update(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->update("test_table", array(
			"test_value"  => "fifth value",
			"test_score"  => 50,
		), array("test_key" => 4));

		$this->assertEquals(0, $num);

	}

	function test_on_duplicate_key_as_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->on_duplicate_key("test_table", array(
			"test_value"  => "tenth value",
			"test_score"  => 100,
		), array("test_key" => 10));

		$this->assertEquals(0, $num);

	}

	function test_on_duplicate_key_as_update(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->on_duplicate_key("test_table", array(
			"test_value"  => "sixth value",
			"test_score"  => 60,
		), array("test_key" => 4));

		$this->assertEquals(0, $num);

	}

	function test_multi_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->multi_insert("test_table", array(
			array(
				"test_value"  => "tenth value",
				"test_score"  => 100,
			),
			array(
				"test_value"  => "eleventh value",
				"test_score"  => 110,
			),
		));

		$this->assertEquals(0, $num);

	}

	function test_multi_replace(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->multi_replace("test_table", array(
			array(
				"test_value"  => "seventh value",
				"test_score"  => 70,
				"test_key"    => 2,
			),
			array(
				"test_value"  => "eighth value",
				"test_score"  => 80,
				"test_key"    => 3,
			),
		));

		$this->assertEquals(0, $num);

	}

	function test_scalar(){

		$dbConn = $this->getDbConn();

		$sql = "select test_value from test_table where test_key = ?;";
		$val = $dbConn->scalar($sql, array(1));
		$this->assertEquals("", $val);

	}

	function test_scalars(){

		$dbConn = $this->getDbConn();

		$sql = "select test_value from test_table where test_key in(%s) or test_score = ?;";
		$vals = $dbConn->scalars($sql, array(array(1, 2), 60), true);
		$this->assertEquals([], $vals);

	}

	function test_row(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->row($sql, array(array(1, 2)), true);
		$expected = array(
			"test_key"   => "1",
			0            => "1",
			"test_value" => "first value",
			1            => "first value",
			"test_score" => "10",
			2            => "10",
		);
		$this->assertEquals([], $vals);

	}

	function test_keyrow(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->keyrow($sql, array(array(1, 2)), true);
		$expected = array(
			1 => array(
				"test_key"   => "1",
				0            => "1",
				"test_value" => "first value",
				1            => "first value",
				"test_score" => "10",
				2            => "10",
			),
			2 => array(
				"test_key"   => "2",
				0            => "2",
				"test_value" => "second value",
				1            => "second value",
				"test_score" => "20",
				2            => "20",
			),
		);
		$this->assertEquals([], $vals);

	}

	function test_keyrows(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->keyrows($sql, array(array(1, 2)), true);
		$expected = array(
			1 => array(
				array(
					"test_key"   => "1",
					0            => "1",
					"test_value" => "first value",
					1            => "first value",
					"test_score" => "10",
					2            => "10",
				),
			),
			2 => array(
				array(
					"test_key"   => "2",
					0            => "2",
					"test_value" => "second value",
					1            => "second value",
					"test_score" => "20",
					2            => "20",
				),
			),
		);
		$this->assertEquals([], $vals);

	}

	function test_keypair(){

		$dbConn = $this->getDbConn();

		$sql = "select test_key, test_score from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->keypair($sql, array(array(1, 2)), true);
		$expected = array(
			"1" => "10",
			"2" => "20",
		);
		$this->assertEquals([], $vals);

	}

	function test_assoc(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->assoc($sql, array(array(1, 2)), true);
		$expected = array(
			array(
				"test_key"   => "1",
				"test_value" => "first value",
				"test_score" => "10",
			),
			array(
				"test_key"   => "2",
				"test_value" => "second value",
				"test_score" => "20",
			),
		);
		$this->assertEquals([], $vals);

	}

	function test_exe(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->exe($sql, array(array(1, 2)), true);

		if($vals InstanceOf IteratorIterator){
			$this->assertTrue(true);
		}else{
			$this->assertEquals([], $vals);
		}

	}

}





