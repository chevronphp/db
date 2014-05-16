<?php

require_once("tests/bootstrap.php");

use Chevron\DB\Mock;

FUnit::setup(function(){

	$dbConn = new Mock\PDOWrapper;
	FUnit::fixture("dbConn", $dbConn);

});

FUnit::test("Mock\Wrapper::put()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->put("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::insert()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->insert("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::update()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->update("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::replace()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->replace("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::on_duplicate_key()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->on_duplicate_key("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::multi_insert()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->multi_insert("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::multi_replace()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->multi_replace("", array(), array());
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::exe()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->exe("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::assoc()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->assoc("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::row()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->row("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::scalar()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->scalar("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::scalars()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->scalars("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::keypair()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->keypair("", array(), true);
	FUnit::equal($expected, $result);

});

FUnit::test("Mock\Wrapper::keyrow()", function(){

	$dbConn = FUnit::fixture("dbConn");

	$expected = 8675309;
	$dbConn->next($expected);
	$result = $dbConn->keyrow("", array(), true);
	FUnit::equal($expected, $result);

});
