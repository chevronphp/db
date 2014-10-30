<?php

use Chevron\DB;

class StubWrapperTest extends PHPUnit_Framework_TestCase {

	function getInst(){
		return new DB\StubWrapper;
	}

	function test_interface(){
		$mock = $this->getInst();
		$this->assertInstanceOf("Chevron\\DB\\Interfaces\\PDOWrapperInterface", $mock);
	}

	function test_readPush(){
		$mock = $this->getInst();
		$expected = ["this" => "that"];

		// purely for coverage
		$methods = [
			"exe",
			"assoc",
			"row",
			"keyrow",
			"keyrows",
			"scalar",
			"scalars",
			"keypair",
		];

		foreach($methods as $method){
			$mock->push($expected);
			$result = $mock->$method("query", []);
			$this->assertEquals($expected, $result);
		}
	}

	function test_writePush(){
		$mock = $this->getInst();
		$expected = ["this" => "that"];

		// purely for coverage
		$methods = [
			"put",
			"insert",
			"update",
			"replace",
			"on_duplicate_key",
			"multi_insert",
			"multi_replace",
		];

		foreach($methods as $method){
			$mock->push($expected);
			$result = $mock->$method("table", [], []);
			$this->assertEquals($expected, $result);
		}
	}

}