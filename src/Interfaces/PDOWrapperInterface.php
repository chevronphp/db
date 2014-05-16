<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the functionality of the Chevron\PDO\MySQL\Wrapper class
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
Interface PDOWrapperInterface {
	/**
	 * Capture both INSERT and UPDATE queries and dispatch accordingly
	 *
	 * @param string $table The table name to act on
	 * @param array $map An array of columns => values
	 * @param array $where An array of columns => values
	 * @return int
	 */
	function put($table, array $map, array $where = array());

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

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns a raw result Iterator.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return IteratorIterator
	 */
	function exe($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s).
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return array
	 */
	function assoc($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns the first row of the result set.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return array
	 */
	function row($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns the first row of the result set.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return array
	 */
	function keyrow($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns an indexed mulii dimentional array
	 * indexed first by the first column in the query then automatically indexed
	 * after that. Essentially, it will return all the rows associated with
	 * the first reqeusted column.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return array
	 */
	function keyrows($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns the first value of the first row
	 * of the result set.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return scalar
	 */
	function scalar($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns an array of the first values of ever
	 * row in the result set
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return array
	 */
	function scalars($query, array $map = array(), $in = false);

	/**
	 * Execute an SQL query with the provided data $map where $map is an array
	 * of column => value pairs. The optional $in is used to denote the use of
	 * WHERE IN() clauses and will parse the $map for arrays, adding the correct
	 * number of tokens to the query before execution. The query string itself
	 * should use the %s placeholder for the location of the multi-token
	 * replacement(s). This method returns an array where the $key is the first
	 * value of the given row and the $value is the second value of the given
	 * row.
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to use in execution
	 * @param bool $in A flag to parse the query for WHERE IN clauses
	 * @return IteratorIterator
	 */
	function keypair($query, array $map = array(), $in = false);

}

