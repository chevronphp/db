<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the read only shortcut methods
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
interface ReadQueriesInterface {

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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
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
	 * @return Traversable|array
	 */
	function keypair($query, array $map = array(), $in = false);

}