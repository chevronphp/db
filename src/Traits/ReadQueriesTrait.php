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

		if($in){
			// this syntax (returning an array with two values) is a little more
			// esoteric than i'd prefer ... but it works
			list( $query, $map ) = $this->in( $query, $map );
		}

		// redundant for IN queries since the data is already flat
		$data = $this->filterData($map);

		if(is_callable($this->inspector)){
			call_user_func($this->inspector, $this, $query, $data);
		}

		$statement = $this->prepare($query);
		// if( !($query InstanceOf \PDOStatement ) ){}

		$statement->setFetchMode($fetch);

		$retry = $this->numRetries;
		while( $retry-- ){
			try{
				$success = $statement->execute($data);
			}catch(\Exception $e){
				throw new DBException($this->fError($statement, $data));
			}

			if( $success ){
				if( $statement->columnCount() ){
					// only queries that return a result set should have a column count
					return new \IteratorIterator($statement);
				}
				throw new DBException("Successful query returned falsey column count");
			}

			// deadlock
			if( $statement->errorCode() == "40001" ){ continue; }

			throw new DBException($this->fError($statement));
		}

		throw new DBException("Query Failed after 5 attempts:\n\n{$query}");

	}

}