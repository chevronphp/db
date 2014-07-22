<?php

namespace Chevron\DB;

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
class PDOWrapper implements Interfaces\PDOWrapperInterface {

	use Traits\QueryBuilderTrait;
	use Traits\WriteQueriesTrait;
	use Traits\ReadQueriesTrait;
	use Traits\RetryAwareTrait;
	use Traits\InspectorAwareTrait;

	/**
	 *
	 */
	protected $conn;

	/**
	 *
	 */
	protected $driver;

	/**
	 *
	 */
	function __construct(\PDO $conn, Interfaces\DriverInterface $driver){
		$this->conn = $conn;
		$this->driver = $driver;
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
	 * Beautifies an error message to display
	 * @param \PDOException $obj
	 * @param bool $rtn A flag to toggle an exit or return
	 * @return mixed
	 */
	protected function printErr(\PDOStatement $obj, $data = 0){

		$error               = $obj->errorInfo();
		$error["query"]      = $obj->queryString;
		$error["num_params"] = $data;

		if($error){
			$len = 0;
			foreach($error as $key => $value){
				if( ($l = strlen($key)) > $len){ $len = $l; }
			}
			$_error = "";
			foreach($error as $key => $value){
				$_error .= sprintf("%{$len}s => %s\n", $key, $value);
			}
		}

		return sprintf(
			"\n\nThe DB dropped an error !!! %s\n-------------------------\n%s\n\n",
			strtoupper($level), $message, $_error
		);

	}
}

