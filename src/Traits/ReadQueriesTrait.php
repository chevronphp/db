<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Interfaces;
use \Chevron\DB\Exceptions\DBException;
/**
 * Implements a few read only shortcut methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait ReadQueriesTrait {

	/**
	 * method to debug (if necessary), execute a query, retrying if necessary,
	 * will prepare the query before execution, returning the result
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to pass to the query
	 * @param bool $in A toggle to pre parse the query to use the IN clause
	 * @param int $fetch The fetch method
	 * @return array
	 */
	function exe($query, array $map = [], $in = false, $fetch = \PDO::FETCH_ASSOC){
		$statement = $this->read($query, $map, $in, $fetch);
		return ($statement InstanceOf \PDOStatement) ? $statement->fetchAll() : [];
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function assoc($query, array $map = [], $in = false){
		return $this->exe($query, $map, $in, \PDO::FETCH_ASSOC);
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function rows($query, array $map = [], $in = false, $fetch = \PDO::FETCH_ASSOC){
		return $this->exe($query, $map, $in, $fetch);
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function row($query, array $map = [], $in = false, $fetch = \PDO::FETCH_ASSOC){
		$result = $this->read($query, $map, $in, $fetch);
		foreach($result as $row){ return $row; }
		return [];
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function scalar($query, array $map = [], $in = false){
		$result = $this->read($query, $map, $in, \PDO::FETCH_NUM);
		foreach($result as $row){ return $row[0]; }
		return null;
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function scalars($query, array $map = [], $in = false){

		$result = $this->read($query, $map, $in, \PDO::FETCH_NUM);
		$final = [];
		foreach($result as $row){ $final[] = $row[0]; }
		return $final;
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keypair($query, array $map = [], $in = false){

		$result = $this->read($query, $map, $in, \PDO::FETCH_NUM);
		$final = [];
		foreach($result as $row){
			$final[$row[0]] = $row[1];
		}
		return $final ?: [];
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keyrow($query, array $map = [], $in = false){

		$result = $this->read($query, $map, $in, \PDO::FETCH_BOTH);
		$final = [];
		foreach($result as $row){
			$final[$row[0]] = $row;
		}
		return $final ?: [];
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keyrows($query, array $map = [], $in = false){

		$result = $this->read($query, $map, $in, \PDO::FETCH_BOTH);
		$final = [];
		foreach($result as $row){
			$final[$row[0]][] = $row;
		}
		return $final ?: [];
	}

	/**
	 * method to debug (if necessary), execute a query, retrying if necessary,
	 * will prepare the query before execution, returning the result
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to pass to the query
	 * @param bool $in A toggle to pre parse the query to use the IN clause
	 * @param int $fetch The fetch method
	 * @return array
	 */
	protected function read($query, array $map = [], $in = false, $fetch = \PDO::FETCH_BOTH){
		$statement = $this->exeQuery($query, $map, $in, $fetch);
		return ($statement InstanceOf \Traversable) ? $statement : [];
	}

}