<?php

namespace Chevron\DB;

use Chevron\DB\Interfaces;
use Psr\Log;

class NullWrapper implements Interfaces\PDOWrapperInterface {

	function setConnection(\PDO $pdo){
		// noop
	}

	function setDriver(Interfaces\DriverInterface $driver){
		// noop
	}

	function logError(\PDOException $e, array $context = []){
		// noop
	}

	function exe($query, array $map = array(), $in = false){ return []; }

	function assoc($query, array $map = array(), $in = false){ return []; }

	function row($query, array $map = array(), $in = false){ return []; }

	function keyrow($query, array $map = array(), $in = false){ return []; }

	function keyrows($query, array $map = array(), $in = false){ return []; }

	function scalar($query, array $map = array(), $in = false){ return ""; }

	function scalars($query, array $map = array(), $in = false){ return []; }

	function keypair($query, array $map = array(), $in = false){ return []; }



	function put($table, array $map, array $where = array()){ return 0; }

	function insert($table, array $map){ return 0; }

	function update($table, array $map, array $where = array()){ return 0; }

	function replace($table, array $map){ return 0; }

	function on_duplicate_key($table, array $map, array $where){ return 0; }

	function multi_insert($table, array $map){ return 0; }

	function multi_replace($table, array $map){ return 0; }

}