<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Exceptions\DBException;
/**
 * Implements a few read only shortcut methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait ReadQueriesTrait {

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function exe($query, array $map = array(), $in = false){

		return $this->exeReadQuery($query, $map, $in);
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function assoc($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in, \PDO::FETCH_ASSOC);
		return iterator_to_array($result) ?: array();
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function row($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		foreach($result as $row){ return $row; }
		return array();
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function scalar($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		foreach($result as $row){ return $row[0]; }
		return null;
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function scalars($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		$final = array();
		foreach($result as $row){ $final[] = $row[0]; }
		return $final;
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keypair($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]] = $row[1];
		}
		return $final ?: array();
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keyrow($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]] = $row;
		}
		return $final ?: array();
	}

	/**
	 * For documentation, consult the ReadQueriesInterface
	 */
	function keyrows($query, array $map = array(), $in = false){

		$result = $this->exeReadQuery($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]][] = $row;
		}
		return $final ?: array();
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
	protected function exeReadQuery($query, array $map, $in, $fetch = \PDO::FETCH_BOTH){
		$statement = $this->exeQuery($query, $map, $in, $fetch);
		if( $statement->columnCount() ){
			// only queries that return a result set should have a column count
			return new \IteratorIterator($statement);
		}
		$this->logError(new DBException("Successful query returned falsey column count"), [
			"query_string"  => $statement->queryString,
		]);
	}

}