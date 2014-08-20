<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Exceptions\DBException;
/**
 * Implements a few write only shortcut methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait WriteQueriesTrait {

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function put($table, array $map, array $where = array()){
		if( $where ){
			return $this->update($table, $map, $where);
		}else{
			return $this->insert($table, $map);
		}
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function insert($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, 0);
		$query = $this->driver->makeInsertQuery($table, $columns, $tokens);
		$data  = $this->filterData($map);
		return $this->exeWriteQuery($query, $data);
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function update($table, array $map, array $where = array()){

		$column_map      = $this->equalPairs($map, ", ");
		$conditional_map = $this->equalPairs($where, " and ");
		$query = $this->driver->makeUpdateQuery($table, $column_map, $conditional_map);
		$data  = $this->filterData($map, $where);
		return $this->exeWriteQuery($query, $data);
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function replace($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, 0);
		$query = $this->driver->makeReplaceQuery($table, $columns, $tokens);
		$data  = $this->filterData($map);
		return $this->exeWriteQuery($query, $data);
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function on_duplicate_key($table, array $map, array $where){

		$column_map      = $this->equalPairs($map, ", ");
		$conditional_map = $this->equalPairs($where, ", ");
		$query = $this->driver->makeOnDuplicateKeyQuery($table, $column_map, $conditional_map);
		$data  = $this->filterData($map, $where, $map);
		return $this->exeWriteQuery($query, $data);
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function multi_insert($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, count($map));
		$query = $this->driver->makeInsertQuery($table, $columns, $tokens);
		$data  = $this->filterMultiData($map);
		return $this->exeWriteQuery($query, $data);
	}

	/**
	 * For documentation, consult the WriteQueriesInterface
	 */
	function multi_replace($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, count($map));
		$query = $this->driver->makeReplaceQuery($table, $columns, $tokens);
		$data  = $this->filterMultiData($map);
		return $this->exeWriteQuery($query, $data);
	}


	/**
	 * method to debug (if necessary), execute a query, retrying if necessary,
	 * will prepare the query before execution returning the number of affected
	 * rows
	 *
	 * @param string $query The query to execute
	 * @param array $data The data to pass to the query
	 * @return int
	 */
	protected function exeWriteQuery($query, array $data){

		$statement = $this->exeQuery($query, $data);
		return $statement->rowCount();
	}
}