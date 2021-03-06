<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the functionality specific to write queries for a
 * specific DB
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
interface PDOWrapperInterface extends ReadQueriesInterface, WriteQueriesInterface {

	/**
	 *
	 */
	function setConnection($pdo);

	/**
	 *
	 */
	function setDriver(DriverInterface $driver);

	/**
	 *
	 */
	function logError(\PDOException $e, array $context = []);

}

