<?php

namespace Chevron\DB\Drivers;

use \Chevron\DB\Interfaces;

// use this for typehinted injection
class NullDriver implements Interfaces\DriverInterface {

	function makeInsertQuery($table, $columns, $tokens){
		return "";
	}

	function makeUpdateQuery($table, $column_map, $conditional_map){
		return "";
	}

	function makeReplaceQuery($table, $columns, $tokens){
		return "";
	}

	function makeOnDuplicateKeyQuery($table, $column_map, $conditional_map){
		return "";
	}

	function shouldRetry(\PDOStatement $statement){
		return false;
	}

}