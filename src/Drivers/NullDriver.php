<?php

namespace Chevron\DB\Drivers;

use \Chevron\DB\Interfaces;

// use this for typehinted injection
class NullDriver implements Interfaces\DriverInterface {

	function makeInsertQuery($table, $columns, $tokens){
		//noop
	}

	function makeUpdateQuery($table, $column_map, $conditional_map){
		//noop
	}

	function makeReplaceQuery($table, $columns, $tokens){
		//noop
	}

	function makeOnDuplicateKeyQuery($table, $column_map, $conditional_map){
		//noop
	}

	function shouldRetry(\PDOStatement $statement){
		//noop
	}

}