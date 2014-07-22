<?php

use \Chevron\DB;

class MySQLPDOWrapperTest extends PHPUnit_Framework_TestCase {

	/*
	 * fixtures
	 */

	function getDbConn(){
		$dbConn = new \PDO(TEST_DB_MYSQL_DSN, TEST_DB_USERNAME, TEST_DB_PASSWORD);
		$dbConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		// $driver = new DB\Drivers\MySQL;
		return new DB\PDOWrapper($dbConn, new DB\Drivers\MySQLDriver);
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
			  `test_key` int(11) NOT NULL AUTO_INCREMENT,
			  `test_value` varchar(255) DEFAULT NULL,
			  `test_score` int(11) NULL DEFAULT NULL,
			  PRIMARY KEY (`test_key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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

		$this->assertEquals(2, $num);

	}

	function test_update(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->update("test_table", array(
			"test_value"  => "fifth value",
			"test_score"  => 50,
		), array("test_key" => 4));

		$this->assertEquals(1, $num);

	}

	function test_on_duplicate_key_as_insert(){

		$dbConn = $this->getDbConn();

		$num = $dbConn->on_duplicate_key("test_table", array(
			"test_value"  => "tenth value",
			"test_score"  => 100,
		), array("test_key" => 10));

		$this->assertEquals(1, $num);

	}

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

		$this->assertEquals(4, $num);

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

		if($vals InstanceOf IteratorIterator){
			$this->assertTrue(true);
		}else{
			$this->assertEquals($expected, $vals);
		}

	}

	/*
	 * Tests for the Query Helper Trait. Variations need to be tested
	 */

	function test_in(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodIn();

		$query = "select * from table where col1 = ? and col2 in (%s);";
		$data  = array("string", array(5, 6));

		$result = $method->invokeArgs($dbConn, array($query, $data));
		list( $query, $data ) = $result;

		$expected_query = "select * from table where col1 = ? and col2 in (?, ?);";
		$expected_data  = array("string", 5, 6);

		$this->assertEquals($query, $expected_query, "query");
		$this->assertEquals($data,  $expected_data,  "data");
	}

	function test_equalPairs_comma(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodEqualPairs();

		$data = array("col1" => "val3", "col2" => "val4");

		$expected_comma = "`col1` = ?, `col2` = ?";
		$result = $method->invokeArgs($dbConn, array($data));
		$this->assertEquals($result, $expected_comma);
	}

	function test_equalPairs_and(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodEqualPairs();

		$data = array("col1" => "val3", "col2" => "val4");

		$expected_and   = "`col1` = ? and `col2` = ?";
		$result = $method->invokeArgs($dbConn, array($data, " and "));
		$this->assertEquals($result, $expected_and);
	}

	function test_mapColumns(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodMapColumns();

		$data = array("col1" => "val3", "col2" => "val4", "col3" => array(true, "NOW()"));

		$result = $method->invokeArgs($dbConn, array($data));
		$columns = array_keys($result);
		$tokens  = array_values($result);

		$expected_columns = array("col1", "col2", "col3");
		$expected_tokens  = array("?", "?", "NOW()");

		$this->assertEquals($columns, $expected_columns, "columns ");
		$this->assertEquals($tokens,  $expected_tokens,  "tokens");
	}

	function test_parenPairs(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodParenPairs();

		$data = array(
			array("col1" => "val1", "col2" => "val2"),
			array("col1" => "val3", "col2" => "val4"),
		);

		$expected_columns = "(`col1`, `col2`)";
		$expected_tokens  = "(?, ?)";

		$result = $method->invokeArgs($dbConn, array($data, 0));
		list( $columns, $tokens ) = $result;

		$this->assertEquals($columns, $expected_columns, "columns");
		$this->assertEquals($tokens,  $expected_tokens,  "tokens");
	}

	function test_parenPairs_mulitple(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodParenPairs();

		$data = array(
			array("col1" => "val1", "col2" => "val2"),
			array("col1" => "val3", "col2" => "val4"),
		);

		$expected_columns = "(`col1`, `col2`)";
		$expected_tokens  = "(?, ?),(?, ?),(?, ?)";

		$result = $method->invokeArgs($dbConn, array($data, 3));
		list( $columns, $tokens ) = $result;

		$this->assertEquals($columns, $expected_columns, "columns");
		$this->assertEquals($tokens,  $expected_tokens,  "tokens");
	}

	function test_filterData(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodFilterData();

		$data = array(
			"col1" => "val3",
			"col2" => "val4",
			"col3" => array(true, "NOW()")
		);

		$result = $method->invokeArgs($dbConn, array($data));

		$expected_values  = array("val3", "val4");

		$this->assertEquals($result, $expected_values);
	}

	function test_filterData_multiple_args(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodFilterData();

		$data = array(
			"import_q"    => array(true, "NOW()"),
			"refresh_q"   => array(true, "NULL"),
			"last_status" => "0",
			"comments"    => "Import Queued on %s.",
			"job_name"    => "",
		);

		$where = array("myon_id" => "asdf-asdf-asdf-asdf");

		$result = $method->invokeArgs($dbConn, array($data, $where, $data));

		$expected_values  = array(
			"0",
			"Import Queued on %s.",
			"",
			"asdf-asdf-asdf-asdf",
			"0",
			"Import Queued on %s.",
			"",
		);

		$this->assertEquals($expected_values, $result);
	}

	function test_filterMultiData(){

		$dbConn = $this->getDbConn();
		$method = $this->getMethodFilterMultiData();

		$data = array(
			array(
				"import_q"    => array(true, "NOW()"),
				"refresh_q"   => array(true, "NULL"),
				"last_status" => "0",
				"comments"    => "Import Queued on %s.",
				"job_name"    => "1",
			),
			array(
				"import_q"    => array(true, "NOW()"),
				"refresh_q"   => array(true, "NULL"),
				"last_status" => "0",
				"comments"    => "Import Queued on %s.",
				"job_name"    => "2",
			),
		);

		$result = $method->invokeArgs($dbConn, array($data));

		$expected_values  = array(
			"0",
			"Import Queued on %s.",
			"1",
			"0",
			"Import Queued on %s.",
			"2",
		);

		$this->assertEquals($expected_values, $result);
	}

	/**
	 * map_columns needs to accomodate a number of different strctures. there are
	 * many test necessary to ensure that it does
	 */



	/**
	 * protected MySQL\Wrapper::mapColumns() for col => val
	 */
	function test_mapColumns_1(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => "val",
			"col2" => "val",
			"col3" => "val",
			"col4" => "val",
		);

		$columns = array(
			"col1",
			"col2",
			"col3",
			"col4",
		);

		$tokens = array(
			"?",
			"?",
			"?",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for col => array(true, val)
	 */
	function test_mapColumns_2(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => array(true, "val"),
			"col2" => array(true, "val"),
			"col3" => array(true, "val"),
			"col4" => array(true, "val"),
		);

		$columns = array(
			"col1",
			"col2",
			"col3",
			"col4",
		);

		$tokens = array(
			"val",
			"val",
			"val",
			"val",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are last
	 */
	function test_mapColumns_3(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => "val",
			"col2" => "val",
			"col3" => array(true, "val"),
			"col4" => array(true, "val"),
		);

		$columns = array(
			"col1",
			"col2",
			"col3",
			"col4",
		);

		$tokens = array(
			"?",
			"?",
			"val",
			"val",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are first
	 */
	function test_mapColumns_4(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => array(true, "val"),
			"col2" => array(true, "val"),
			"col3" => "val",
			"col4" => "val",
		);

		$columns = array(
			"col1",
			"col2",
			"col3",
			"col4",
		);

		$tokens = array(
			"val",
			"val",
			"?",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are in the middle
	 */
	function test_mapColumns_5(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => "val",
			"col2" => array(true, "val"),
			"col3" => array(true, "val"),
			"col4" => "val",
		);

		$columns = array(
			"col1",
			"col2",
			"col3",
			"col4",
		);

		$tokens = array(
			"?",
			"val",
			"val",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => val)
	 */
	function test_mapColumns_6(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => "val"),
			array("col1" => "val"),
			array("col1" => "val"),
			array("col1" => "val"),
		);

		$columns = array(
			"col1",
		);

		$tokens = array(
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => array(true, val))
	 */
	function test_mapColumns_7(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => array(true, "val")),
			array("col1" => array(true, "val")),
			array("col1" => array(true, "val")),
			array("col1" => array(true, "val")),
		);

		$columns = array(
			"col1",
		);

		$tokens = array(
			"val",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => array(true, val), col => array(true, val))
	 */
	function test_mapColumns_8(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => array(true, "val"), "col2" => array(true, "val")),
			array("col1" => array(true, "val"), "col2" => array(true, "val")),
			array("col1" => array(true, "val"), "col2" => array(true, "val")),
			array("col1" => array(true, "val"), "col2" => array(true, "val")),
		);

		$columns = array(
			"col1",
			"col2",
		);

		$tokens = array(
			"val",
			"val",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => val, col => val)
	 */
	function test_mapColumns_9(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => "val", "col2" => "val"),
			array("col1" => "val", "col2" => "val"),
			array("col1" => "val", "col2" => "val"),
			array("col1" => "val", "col2" => "val"),
		);

		$columns = array(
			"col1",
			"col2",
		);

		$tokens = array(
			"?",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => val, col => array(true, val)) where arrays are second
	 */
	function test_mapColumns_10(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => "val", "col2" => array(true, "val")),
			array("col1" => "val", "col2" => array(true, "val")),
			array("col1" => "val", "col2" => array(true, "val")),
			array("col1" => "val", "col2" => array(true, "val")),
		);

		$columns = array(
			"col1",
			"col2",
		);

		$tokens = array(
			"?",
			"val",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => val, col => array(true, val)) where arrays are first
	 */
	function test_mapColumns_11(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => array(true, "val"), "col2" => "val"),
			array("col1" => array(true, "val"), "col2" => "val"),
			array("col1" => array(true, "val"), "col2" => "val"),
			array("col1" => array(true, "val"), "col2" => "val"),
		);

		$columns = array(
			"col1",
			"col2",
		);

		$tokens = array(
			"val",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for col => val with a NULL value
	 */
	function test_mapColumns_12(){

		$dbConn = $this->getDbConn();

		$a = array(
			"col1" => "val",
			"col2" => null,
			"col3" => "val",
			"col4" => "val",
		);

		$columns = array(
			"col1",
			"col3",
			"col4",
		);

		$tokens = array(
			"?",
			"?",
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

	}

	/**
	 * protected MySQL\Wrapper::mapColumns() for array(col => val, col => val) with a NULL value
	 */
	function test_mapColumns_13(){

		$dbConn = $this->getDbConn();

		$a = array(
			array("col1" => "val", "col2" => null),
			array("col1" => "val", "col2" => null),
			array("col1" => "val", "col2" => null),
			array("col1" => "val", "col2" => null),
		);

		$columns = array(
			"col1",
		);

		$tokens = array(
			"?",
		);

		$method = $this->getMethodMapColumns();

		$result  = $method->invokeArgs($dbConn, array($a));
		$c = array_keys($result);
		$t = array_values($result);

		$this->assertEquals($columns, $c, "columns");
		$this->assertEquals($tokens, $t, "tokens");

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





