<?php

namespace Chevron\DB\MySQL;

use \Chevron\DB\Interfaces;
use \Chevron\DB\Traits;
use \Chevron\DB\Exceptions\DBException;
/**
 * A DB wrapper class offering some helpful shortcut methods
 *
 * For documentation, consult the Interface (__DIR__ . "/PDOWrapperInterface.php")
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class PDOWrapper extends \PDO implements Interfaces\PDOWrapperInterface {

	use Traits\QueryHelperTrait;

	/**
	 * the number of times to retry after a mysql error
	 */
	protected $numRetries = 5;

	/**
	 * a lambda to execute before executing a query, usefule for debugging
	 */
	protected $inspector;

	/**
	 * method to set the number of retries after an error
	 * @param int $num The number of retries
	 * @return
	 */
	function setNumRetries($num){
		$this->numRetries = (int)$num;
	}

	/**
	 * Method to set a lambda as an inspector pre query, The callback will be passed
	 * three params: PDO $this, string $query, array $data
	 * @param callable $func
	 * @return
	 */
	function setInspector(callable $func){
		$this->inspector = $func;
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function put($table, array $map, array $where = array()){
		if( $where ){
			return $this->update($table, $map, $where);
		}else{
			return $this->insert($table, $map);
		}
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function insert($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, 0);
		$query = sprintf("INSERT INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
		$data = $this->filterData($map);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function update($table, array $map, array $where = array()){

		$column_map      = $this->equalPairs($map, ", ");
		$conditional_map = $this->equalPairs($where, " and ");
		$query = sprintf("UPDATE `%s` SET %s WHERE %s;", $table, $column_map, $conditional_map);
		$data = $this->filterData($map, $where);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function replace($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, 0);
		$query = sprintf("REPLACE INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
		$data  = $this->filterData($map);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function on_duplicate_key($table, array $map, array $where){

		$column_map      = $this->equalPairs($map, ", ");
		$conditional_map = $this->equalPairs($where, ", ");
		$query = sprintf("INSERT INTO `%s` SET %s, %s ON DUPLICATE KEY UPDATE %s;", $table, $column_map, $conditional_map, $column_map);
		$data  = $this->filterData($map, $where, $map);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function multi_insert($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, count($map));
		$query = sprintf("INSERT INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
		$data  = $this->filterMultiData($map);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function multi_replace($table, array $map){

		list($columns, $tokens) = $this->parenPairs($map, count($map));
		$query = sprintf("REPLACE INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
		$data  = $this->filterMultiData($map);
		return $this->exe_return_count($query, $data);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function exe($query, array $map = array(), $in = false){

		return $this->exe_return_result($query, $map, $in);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function assoc($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in, \PDO::FETCH_ASSOC);
		return iterator_to_array($result) ?: array();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function row($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		foreach($result as $row){ return $row; }
		return array();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function scalar($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		foreach($result as $row){ return $row[0]; }
		return null;
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function scalars($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		$final = array();
		foreach($result as $row){ $final[] = $row[0]; }
		return $final;
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keypair($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]] = $row[1];
		}
		return $final ?: array();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keyrow($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]] = $row;
		}
		return $final ?: array();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keyrows($query, array $map = array(), $in = false){

		$result = $this->exe_return_result($query, $map, $in);
		$final = array();
		foreach($result as $row){
			$final[$row[0]][] = $row;
		}
		return $final ?: array();
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
	protected function exe_return_count($query, array $data){

		if(is_callable($this->inspector)){
			call_user_func($this->inspector, $this, $query, $data);
		}

		$statement = $this->prepare($query);
		// if( !($query InstanceOf \PDOStatement ) ){}

		$retry = $this->numRetries;
		while( $retry-- ){
			try{
				$success = $statement->execute($data);
			}catch(\Exception $e){
				throw new DBException($this->fError($statement, $data));
			}

			if( $success ){
				return $statement->rowCount();
			}

			// deadlock
			if( $statement->errorCode() == "40001" ){ continue; }

			throw new DBException($this->fError($statement));
		}
		throw new DBException("Query Failed after 5 attempts:\n\n{$query}");
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
	protected function exe_return_result($query, array $map, $in, $fetch = \PDO::FETCH_BOTH){

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

	/**
	 * Beautifies an error message to display
	 * @param \PDOException $obj
	 * @param bool $rtn A flag to toggle an exit or return
	 * @return mixed
	 */
	protected function fError(\PDOStatement $obj, $data = null, $rtn = true){

		$err   = $obj->errorInfo();
		$err[] = $obj->queryString;

		$str =  "The DB dropped an error:\n\n" .
				"### SQLSTATE // Error Code ###\n" .
				"      %s // %s\n\n" .
				"### Error Message ###\n" .
				"      %s\n\n" .
				"### Query ###\n" .
				"      %s\n\n";

		if($data){
			$err[] = count($data);
			$str .= "### Parameter Count ###\n".
					"      %s";
		}

		$str = vsprintf($str, $err);

		if( $rtn ) return $str;

		printf($str);
		exit();
	}
}

