<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the write only shortcut methods
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
interface WriteQueriesInterface {

	/**
	 * Capture INSERT queries. As with all of the "PUT helpers"
	 * the array $map can take an array(true, "FUNC") where func is an unescaped
	 * value, usually a SQL function.
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @return int
	 */
	function insert($table, array $map);

	/**
	 * Capture UPDATE queries. As with all of the "PUT helpers"
	 * the array $map can take an array(true, "FUNC") where func is an unescaped
	 * value, usually a SQL function.
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @param array $where An array of columns => values
	 * @return int
	 */
	function update($table, array $map, array $where = array());

	/**
	 * Execute a REPLACE query. As with all of the "PUT helpers" the array $map
	 * can take an array(true, "FUNC") where func is an unescaped value, usually
	 * a SQL function.
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @return int
	 */
	function replace($table, array $map);

	/**
	 * Execute an INSERT ... ON DUPLICATE KEY query. As with all of the "PUT
	 * helpers" the array $map can take an array(true, "FUNC") where func is an
	 * unescaped value, usually a SQL function. This helper will take care of
	 * combining and seperating the values
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @param array $where An array of columns => values
	 * @return int
	 */
	function on_duplicate_key($table, array $map, array $where);

	/**
	 * Execute an INSERT query with mulitples rows. As with all of the "PUT
	 * helpers" the array $map can take an array(true, "FUNC") where func is an
	 * unescaped value, usually a SQL function.
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @return int
	 */
	function multi_insert($table, array $map);

	/**
	 * Execute a REPLACE query with mulitples rows. As with all of the "PUT
	 * helpers" the array $map can take an array(true, "FUNC") where func is an
	 * unescaped value, usually a SQL function.
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @return int
	 */
	function multi_replace($table, array $map);

}