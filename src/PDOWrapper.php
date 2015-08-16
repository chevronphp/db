<?php

namespace Chevron\DB;

use \Chevron\DB\Interfaces;
use \Chevron\DB\Traits;
use \Psr\Log;
use Chevron\DB\Exceptions\DBException;

/**
 * A DB wrapper class offering some helpful shortcut methods
 *
 * For documentation, consult the Interface (__DIR__ . "/PDOWrapperInterface.php")
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class PDOWrapper implements Interfaces\PDOWrapperInterface {

	use Log\LoggerAwareTrait;

	use Traits\QueryBuilderTrait;
	use Traits\WriteQueriesTrait;
	use Traits\ReadQueriesTrait;
	use Traits\RetryAwareTrait;
	use Traits\ExeQueryTrait;

	/**
	 * a connected PDO object
	 */
	protected $conn;

	/**
	 * the vendor spcific driver to use when building queries.
	 */
	protected $driver;

	/**
	 * set the PDO connection
	 */
	function setConnection(\PDO $pdo){
		$this->conn = $pdo;
		$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * set the vendor specifiec query builder
	 */
	function setDriver(Interfaces\DriverInterface $driver = null){
		$this->driver = $driver;
	}

	function setWritable($bool){
		$this->isWritable = (bool)$bool;
	}

	/**
	 * catch method calls meant for PDO
	 * @param string $name The method being called
	 * @param array $args The args passed
	 * @return string|bool
	 */
	function __call($name, $args){
		return call_user_func_array([$this->conn, $name], $args);
	}

	/**
	 * passes info to a logger before throwing the passed PDOException
	 * @param \PDOException $e
	 * @param array $context an array of additional information
	 * @return
	 */
	function logError(\PDOException $e, array $context = []){

		$error = [
			"message"   => $e->getMessage(),
			"code"      => $e->getCode(),
			"file"      => $e->getFile(),
			"line"      => $e->getLine(),
		];

		$error = $error + $context;

		if($this->logger InstanceOf Log\LoggerInterface){
			$this->logger->error($e->getCode(), $error);
		}

		throw new DBException($e->getMessage(), $e->getLine(), $e, $error);

	}

}

