<?php

namespace Chevron\DB\Drivers;

use \Chevron\DB\Interfaces;
/**
 * vendor specific driver
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class MySQLDriver implements Interfaces\DriverInterface {

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being inserted
	 * @return string
	 */
	function makeInsertQuery($table, $columns, $tokens){
		return sprintf("INSERT INTO `%s` %s VALUES %s;", $table, $columns, $tokens);
	}

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $column_map The "col = ?" pairs
	 * @param string $conditional_map The conditional clause
	 * @return string
	 */
	function makeUpdateQuery($table, $column_map, $conditional_map){
		return sprintf("UPDATE `%s` SET %s WHERE %s;", $table, $column_map, $conditional_map);
	}

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being replaced
	 * @return string
	 */
	function makeReplaceQuery($table, $columns, $tokens){
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
	function makeOnDuplicateKeyQuery($table, $column_map, $conditional_map){
		return sprintf("INSERT INTO `%s` SET %s, %s ON DUPLICATE KEY UPDATE %s;", $table, $column_map, $conditional_map, $column_map);
	}

}