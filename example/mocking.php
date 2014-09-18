<?php

namespace Chevron\DB;

require "vendor/autoload.php";

$mock = new DbStub(new Mocks\MockPdo, new Drivers\NullDriver);

if($mock InstanceOf Interfaces\PDOWrapperInterface){
	echo "nailed it\n";
}else{
	echo "try again\n";
}

$expected = ["this" => "that"];

$mock->push($expected);

$result = $mock->multi_replace("table", []);

if($expected === $result){
	echo "nailed it x2\n";
}else{
	echo "try again\n";
}






