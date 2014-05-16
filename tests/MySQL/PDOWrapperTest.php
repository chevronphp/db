<?php

require_once("tests/bootstrap.php");

use Chevron\PDO\MySQL;

/*
 * fixtures
 */

$dbConn = new \Chevron\DB\MySQL\PDOWrapper(TEST_DB_DSN, TEST_DB_USERNAME, TEST_DB_PASSWORD);
$dbConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
FUnit::fixture("dbConn", $dbConn);

$in = new ReflectionMethod($dbConn, "in");
$in->setAccessible(true);
FUnit::fixture("in", $in);

$filterData = new ReflectionMethod($dbConn, "filterData");
$filterData->setAccessible(true);
FUnit::fixture("filterData", $filterData);

$filterMultiData = new ReflectionMethod($dbConn, "filterMultiData");
$filterMultiData->setAccessible(true);
FUnit::fixture("filterMultiData", $filterMultiData);

$parenPairs = new ReflectionMethod($dbConn, "parenPairs");
$parenPairs->setAccessible(true);
FUnit::fixture("parenPairs", $parenPairs);

$equalPairs = new ReflectionMethod($dbConn, "equalPairs");
$equalPairs->setAccessible(true);
FUnit::fixture("equalPairs", $equalPairs);

$mapColumns = new ReflectionMethod($dbConn, "mapColumns");
$mapColumns->setAccessible(true);
FUnit::fixture("mapColumns", $mapColumns);

/*
 * tests
 */

FUnit::setup(function(){

	$dbConn = FUnit::fixture("dbConn");

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

});

FUnit::test("MySQL\Wrapper::insert() a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->insert("test_table", array(
		"test_value"  => "tenth value",
		"test_score"  => 100,
	));

	FUnit::equal(1, $num);

});

FUnit::test("MySQL\Wrapper::replace() (as insert) a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->replace("test_table", array(
		"test_value"  => "replacement fourth value",
	));

	FUnit::equal(1, $num);

});

FUnit::test("MySQL\Wrapper::replace() (as replace) a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->replace("test_table", array(
		"test_value"  => "replacement fourth value",
		"test_key" => 4,
	));

	FUnit::equal(2, $num);

});

FUnit::test("MySQL\Wrapper::update() a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->replace("test_table", array(
		"test_value"  => "fifth value",
		"test_score"  => 50,
	), array("test_key" => 4));

	FUnit::equal(1, $num);

});

FUnit::test("MySQL\Wrapper::on_duplicate_key() (as insert) a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->replace("test_table", array(
		"test_value"  => "tenth value",
		"test_score"  => 100,
	), array("test_key" => 10));

	FUnit::equal(1, $num);

});

FUnit::test("MySQL\Wrapper::on_duplicate_key() (as update) a row", function(){

	$dbConn = FUnit::fixture("dbConn");

	$num = $dbConn->replace("test_table", array(
		"test_value"  => "sixth value",
		"test_score"  => 60,
	), array("test_key" => 4));

	FUnit::equal(1, $num);

});

FUnit::test("MySQL\Wrapper::multi_insert() two rows", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	FUnit::equal(2, $num);

});

FUnit::test("MySQL\Wrapper::multi_replace() two rows", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	FUnit::equal(4, $num);

});

FUnit::test("MySQL\Wrapper::scalar()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$sql = "select test_value from test_table where test_key = ?;";
	$val = $dbConn->scalar($sql, array(1));
	FUnit::equal("first value", $val);

});

FUnit::test("MySQL\Wrapper::scalars()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$sql = "select test_value from test_table where test_key in(%s) or test_score = ?;";
	$vals = $dbConn->scalars($sql, array(array(1, 2), 60), true);
	FUnit::equal(array("first value", "second value", "sixth value"), $vals);

});

FUnit::test("MySQL\Wrapper::row()", function(){

	$dbConn = FUnit::fixture("dbConn");

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
	FUnit::equal($expected, $vals);

});

FUnit::test("MySQL\Wrapper::keyrow()", function(){

	$dbConn = FUnit::fixture("dbConn");

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
	FUnit::equal($expected, $vals);

});

FUnit::test("MySQL\Wrapper::keyrows() a row", function(){

	$dbConn = FUnit::fixture("dbConn");

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
	FUnit::equal($expected, $vals);

});

FUnit::test("MySQL\Wrapper::keypair()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$sql = "select test_key, test_score from test_table where test_key in(%s) order by test_key;";
	$vals = $dbConn->keypair($sql, array(array(1, 2)), true);
	$expected = array(
		"1" => "10",
		"2" => "20",
	);
	FUnit::equal($expected, $vals);

});

FUnit::test("MySQL\Wrapper::assoc()", function(){

	$dbConn = FUnit::fixture("dbConn");

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
	FUnit::equal($expected, $vals);

});

FUnit::test("MySQL\Wrapper::exe()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$sql = "select * from test_table where test_key in(%s) order by test_key;";
	$vals = $dbConn->exe($sql, array(array(1, 2)), true);

	if($vals InstanceOf IteratorIterator){
		FUnit::ok(1);
	}else{
		FUnit::equal($expected, $vals);
	}

});

/*
 * Tests for the Query Helper Trait. Variations need to be tested
 */

FUnit::test("protected MySQL\Wrapper::in()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$method = FUnit::fixture("in");

	$query = "select * from table where col1 = ? and col2 in (%s);";
	$data  = array("string", array(5, 6));

	$result = $method->invokeArgs($dbConn, array($query, $data));
	list( $query, $data ) = $result;

	$expected_query = "select * from table where col1 = ? and col2 in (?, ?);";
	$expected_data  = array("string", 5, 6);

	FUnit::equal($query, $expected_query, "query");
	FUnit::equal($data,  $expected_data,  "data");
});

FUnit::test("protected MySQL\Wrapper::equalPairs() using seperator ','", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("equalPairs");

	$data = array("col1" => "val3", "col2" => "val4");

	$expected_comma = "`col1` = ?, `col2` = ?";
	$result = $method->invokeArgs($dbConn, array($data));
	FUnit::equal($result, $expected_comma);
});

FUnit::test("protected MySQL\Wrapper::equalPairs() using seperator 'and'", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("equalPairs");

	$data = array("col1" => "val3", "col2" => "val4");

	$expected_and   = "`col1` = ? and `col2` = ?";
	$result = $method->invokeArgs($dbConn, array($data, " and "));
	FUnit::equal($result, $expected_and);
});

FUnit::test("protected MySQL\Wrapper::mapColumns()", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("mapColumns");

	$data = array("col1" => "val3", "col2" => "val4", "col3" => array(true, "NOW()"));

	$result = $method->invokeArgs($dbConn, array($data));
	$columns = array_keys($result);
	$tokens  = array_values($result);

	$expected_columns = array("col1", "col2", "col3");
	$expected_tokens  = array("?", "?", "NOW()");

	FUnit::equal($columns, $expected_columns, "columns ");
	FUnit::equal($tokens,  $expected_tokens,  "tokens");
});

FUnit::test("protected MySQL\Wrapper::parenPairs()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$method = FUnit::fixture("parenPairs");

	$data = array(
		array("col1" => "val1", "col2" => "val2"),
		array("col1" => "val3", "col2" => "val4"),
	);

	$expected_columns = "(`col1`, `col2`)";
	$expected_tokens  = "(?, ?)";

	$result = $method->invokeArgs($dbConn, array($data, 0));
	list( $columns, $tokens ) = $result;

	FUnit::equal($columns, $expected_columns, "columns");
	FUnit::equal($tokens,  $expected_tokens,  "tokens");
});

FUnit::test("protected MySQL\Wrapper::parenPairs() multiple pairs", function(){

	$dbConn = FUnit::fixture("dbConn");

	$method = FUnit::fixture("parenPairs");

	$data = array(
		array("col1" => "val1", "col2" => "val2"),
		array("col1" => "val3", "col2" => "val4"),
	);

	$expected_columns = "(`col1`, `col2`)";
	$expected_tokens  = "(?, ?),(?, ?),(?, ?)";

	$result = $method->invokeArgs($dbConn, array($data, 3));
	list( $columns, $tokens ) = $result;

	FUnit::equal($columns, $expected_columns, "columns");
	FUnit::equal($tokens,  $expected_tokens,  "tokens");
});

FUnit::test("protected MySQL\Wrapper::filterData()", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("filterData");

	$data = array(
		"col1" => "val3",
		"col2" => "val4",
		"col3" => array(true, "NOW()")
	);

	$result = $method->invokeArgs($dbConn, array($data));

	$expected_values  = array("val3", "val4");

	FUnit::equal($result, $expected_values);
});

FUnit::test("protected MySQL\Wrapper::filterData() -- multiple args", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("filterData");

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

	FUnit::equal($expected_values, $result);
});

FUnit::test("protected MySQL\Wrapper::filterMultiData()", function(){

	$dbConn = FUnit::fixture("dbConn");
	$method = FUnit::fixture("filterMultiData");

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

	FUnit::equal($expected_values, $result);
});

/**
 * map_columns needs to accomodate a number of different strctures. there are
 * many test necessary to ensure that it does
 */

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => val", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => array(true, val)", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are last", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are first", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => val, col => array(true, val) where arrays are in the middle", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => val)", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => array(true, val))", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => array(true, val), col => array(true, val))", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => val, col => val)", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => val, col => array(true, val)) where arrays are second", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => val, col => array(true, val)) where arrays are first", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for col => val with a NULL value", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("protected MySQL\Wrapper::mapColumns() for array(col => val, col => val) with a NULL value", function(){

	$dbConn = FUnit::fixture("dbConn");

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

	$method = FUnit::fixture("mapColumns");

	$result  = $method->invokeArgs($dbConn, array($a));
	$c = array_keys($result);
	$t = array_values($result);

	FUnit::equal($columns, $c, "columns");
	FUnit::equal($tokens, $t, "tokens");

});

FUnit::test("Traits\QueryHelperTrait::in() arrays with keys", function(){

	$dbConn = FUnit::fixture("dbConn");

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
	FUnit::equal($expected, $vals);

});





