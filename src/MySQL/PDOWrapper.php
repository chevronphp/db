<?php

namespace Chevron\DB\MySQL;

use \Chevron\DB\Interfaces;
use \Chevron\DB\Traits;

/**
 * A DB wrapper class offering some helpful shortcut methods
 *
 * For documentation, consult the Interface (__DIR__ . "/PDOWrapperInterface.php")
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class PDOWrapper extends \PDO implements Interfaces\PDOWrapperInterface {

	use Traits\QueryBuilderTrait;
	use Traits\WriteQueriesTrait;
	use Traits\ReadQueriesTrait;

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
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being inserted
	 * @return string
	 */
	protected function makeInsertQuery($table, $columns, $tokens){
		return sprintf("INSERT INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
	}

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $column_map The "col = ?" pairs
	 * @param string $conditional_map The conditional clause
	 * @return string
	 */
	protected function makeUpdateQuery($table, $column_map, $conditional_map){
		return sprintf("UPDATE `%s` SET %s WHERE %s;", $table, $column_map, $conditional_map);
	}

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being replaced
	 * @return string
	 */
	protected function makeReplaceQuery($table, $columns, $tokens){
		return sprintf("REPLACE INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
	}

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $column_map The "col = ?" pairs
	 * @param string $conditional_map The conditional clause
	 * @param string $column_map The columns being updated on key collision
	 * @return string
	 */
	protected function makeOnDuplicateKeyQuery($table, $column_map, $conditional_map, $column_map){
		return sprintf("INSERT INTO `%s` SET %s, %s ON DUPLICATE KEY UPDATE %s;", $table, $column_map, $conditional_map, $column_map);
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

