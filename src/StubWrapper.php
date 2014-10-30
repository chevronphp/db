<?php

namespace Chevron\DB;

use Chevron\DB\Interfaces;
use Psr\Log;

class StubWrapper implements Interfaces\PDOWrapperInterface {

	function setConnection(\PDO $pdo){
		// noop
	}

	function setDriver(Interfaces\DriverInterface $driver){
		// noop
	}

	function logError(\PDOException $e, array $context = []){
		// noop
	}

	function exe($query, array $map = array(), $in = false){ return $this->unshift(); }

	function assoc($query, array $map = array(), $in = false){ return $this->unshift(); }

	function row($query, array $map = array(), $in = false){ return $this->unshift(); }

	function keyrow($query, array $map = array(), $in = false){ return $this->unshift(); }

	function keyrows($query, array $map = array(), $in = false){ return $this->unshift(); }

	function scalar($query, array $map = array(), $in = false){ return $this->unshift(); }

	function scalars($query, array $map = array(), $in = false){ return $this->unshift(); }

	function keypair($query, array $map = array(), $in = false){ return $this->unshift(); }

	function insert($table, array $map){ return $this->unshift(); }

	function update($table, array $map, array $where = array()){ return $this->unshift(); }

	function replace($table, array $map){ return $this->unshift(); }

	function on_duplicate_key($table, array $map, array $where){ return $this->unshift(); }

	function multi_insert($table, array $map){ return $this->unshift(); }

	function multi_replace($table, array $map){ return $this->unshift(); }

	protected $results;

	function push($value){
		$this->results[] = $value;
	}

	function unshift(){
		if(count($this->results)){
			return array_shift($this->results);
		}
		return null;
	}
}