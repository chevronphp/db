<?php

use \Chevron\DB;

class SQLiteDriverTest extends PHPUnit_Framework_TestCase {

	/*
	 * fixtures
	 */

	function getDbConn($writable = true){
		$dbConn = new \PDO(TEST_DB_SQLITE_DSN);
		$dbConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$inst = new DB\PDOWrapper;
		$inst->setConnection($dbConn);
		$inst->setWritable($writable);
		$inst->setDriver(new DB\Drivers\SQLiteDriver);

		return $inst;
	}

	function getMethodIn(){
		$dbConn = $this->getDbConn();
		$in = new ReflectionMethod($dbConn, "in");
		$in->setAccessible(true);
		return $in;
	}

	function getMethodFilterData(){
		$dbConn = $this->getDbConn();
		$filterData = new ReflectionMethod($dbConn, "filterData");
		$filterData->setAccessible(true);
		return $filterData;
	}

	function getMethodFilterMultiData(){
		$dbConn = $this->getDbConn();
		$filterMultiData = new ReflectionMethod($dbConn, "filterMultiData");
		$filterMultiData->setAccessible(true);
		return $filterMultiData;
	}

	function getMethodParenPairs(){
		$dbConn = $this->getDbConn();
		$parenPairs = new ReflectionMethod($dbConn, "parenPairs");
		$parenPairs->setAccessible(true);
		return $parenPairs;
	}

	function getMethodEqualPairs(){
		$dbConn = $this->getDbConn();
		$equalPairs = new ReflectionMethod($dbConn, "equalPairs");
		$equalPairs->setAccessible(true);
		return $equalPairs;
	}

	function getMethodMapColumns(){
		$dbConn = $this->getDbConn();
		$mapColumns = new ReflectionMethod($dbConn, "mapColumns");
		$mapColumns->setAccessible(true);
		return $mapColumns;
	}

	/*
	 * tests
	 */

	function setUp(){

		$dbConn = $this->getDbConn();

		$drop_table = "DROP TABLE IF EXISTS `test_table`;";

		$create_table = "
			CREATE TABLE `test_table` (
			  `test_key` INTEGER PRIMARY KEY AUTOINCREMENT,
			  `test_value` VARCHAR(255) DEFAULT NULL,
			  `test_score` MEDIUMINT DEFAULT 0
			);
		";

		$populate_table = "
			INSERT INTO `test_table` (test_value, test_score) VALUES
			('first value', 10), ('second value', 20), ('third value', 30),
			('fourth value', 40), ('fifth value', 50), ('sixth value', 60),
			('seventh value', 70), ('eight value', 80), ('ninth value', 90);
		";

		$dbConn->exec($drop_table);
		$dbConn->exec($create_table);
		$dbConn->exec($populate_table);

	}

	function test_insert_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->insert("test_table", array(
			"test_value"  => "tenth value",
			"test_score"  => 100,
		));

		$this->assertEquals(1, $num);

	}

	/**
	 * @expectedException \PDOException
	 */
	function test_replace_as_insert_exception(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->replace("test_table", array(
			"test_not_value"  => "replacement fourth value",
		));

		$this->assertEquals(1, $num);

	}

	function test_replace_as_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->replace("test_table", array(
			"test_value"  => "replacement fourth value",
		));

		$this->assertEquals(1, $num);

	}

	function test_replace_as_replace(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->replace("test_table", array(
			"test_value"  => "replacement fourth value",
			"test_key" => 4,
		));

		// sqlite doesn't count replace as delete + insert
		$this->assertEquals(1, $num);

	}

	function test_update(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->update("test_table", array(
			"test_value"  => "fifth value",
			"test_score"  => 50,
		), array("test_key" => 4));

		$this->assertEquals(1, $num);

	}

	/**
	 * @expectedException \PDOException
	 */
	function test_on_duplicate_key_as_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->on_duplicate_key("test_table", array(
			"test_value"  => "tenth value",
			"test_score"  => 100,
		), array("test_key" => 10));

		$this->assertEquals(1, $num);

	}

	/**
	 * @expectedException \PDOException
	 */
	function test_on_duplicate_key_as_update(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->on_duplicate_key("test_table", array(
			"test_value"  => "sixth value",
			"test_score"  => 60,
		), array("test_key" => 4));

		$this->assertEquals(2, $num);

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

		$this->assertEquals(2, $num);

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

		// sqlite doesn't count replace as delete + insert
		$this->assertEquals(2, $num);

	}

	function test_scalar(){

		$dbConn = $this->getDbConn();

		$sql = "select test_value from test_table where test_key = ?;";
		$val = $dbConn->scalar($sql, array(1));
		$this->assertEquals("first value", $val);

	}

	function test_scalars(){

		$dbConn = $this->getDbConn();

		$sql = "select test_value from test_table where test_key in(%s) or test_score = ?;";
		$vals = $dbConn->scalars($sql, array(array(1, 2), 60), true);
		$this->assertEquals(array("first value", "second value", "sixth value"), $vals);

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
		$this->assertEquals($expected, $vals);

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
		$this->assertEquals($expected, $vals);

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
		$this->assertEquals($expected, $vals);

	}

	function test_keypair(){

		$dbConn = $this->getDbConn();

		$sql = "select test_key, test_score from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->keypair($sql, array(array(1, 2)), true);
		$expected = array(
			"1" => "10",
			"2" => "20",
		);
		$this->assertEquals($expected, $vals);

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
		$this->assertEquals($expected, $vals);

	}

	function test_exe(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->exe($sql, array(array(1, 2)), true);

		if($vals InstanceOf Traversable || is_array($vals)){
			$this->assertTrue(true);
		}else{
			$this->assertTrue(false);
		}

	}

	/**
	 * Traits\QueryHelperTrait::in() arrays with keys
	 */
	function test_Trait_in(){

		$dbConn = $this->getDbConn();

		$sql = "select * from test_table where test_key in(%s) order by test_key;";
		$vals = $dbConn->row($sql, array(array("one" => "1", "two" => "2")), true);
		$expected = array(
			"test_key"   => "1",
			0            => "1",
			"test_value" => "first value",
			1            => "first value",
			"test_score" => "10",
			2            => "10",
		);
		$this->assertEquals($expected, $vals);

	}

}





