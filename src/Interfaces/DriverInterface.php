<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the functionality required by a driver
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
interface DriverInterface {
	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being inserted
	 * @return string
	 */
	function makeInsertQuery($table, $columns, $tokens);

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $column_map The "col = ?" pairs
	 * @param string $conditional_map The conditional clause
	 * @return string
	 */
	function makeUpdateQuery($table, $column_map, $conditional_map);

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $columns The columns to act on
	 * @param string $tokens The tokens for the values being replaced
	 * @return string
	 */
	function makeReplaceQuery($table, $columns, $tokens);

	/**
	 * combine the various parts to return a DB specific formatted query
	 * @param string $table The Table to act on
	 * @param string $column_map The "col = ?" pairs
	 * @param string $conditional_map The conditional clause
	 * @param string $column_map The columns being updated on key collision
	 * @return string
	 */
	function makeOnDuplicateKeyQuery($table, $column_map, $conditional_map);
}


