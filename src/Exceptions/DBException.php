<?php

namespace Chevron\DB\Exceptions;
/**
 * vendor specific exception
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class DBException extends \PDOException {

	function __construct($message, $code = 0, \Exception $prev = null, array $DbContext = []){
		$this->message  = $message;
		$this->code     = $code;
		$this->previous = $prev;

		$this->sqlState = null;
		if(isset($DbContext["SQLSTATE"])){
			$this->sqlState = $DbContext["SQLSTATE"];
		}

		$this->driverCode = null;
		if(isset($DbContext["driver_code"])){
			$this->driverCode = $DbContext["driver_code"];
		}

		$this->driverMsg = null;
		if(isset($DbContext["driver_msg"])){
			$this->driverMsg = $DbContext["driver_msg"];
		}

		$this->errorCode = null;
		if(isset($DbContext["error_code"])){
			$this->errorCode = $DbContext["error_code"];
		}

		$this->queryString = null;
		if(isset($DbContext["query_string"])){
			$this->queryString = $DbContext["query_string"];
		}

		$this->paramCount = null;
		if(isset($DbContext["param_count"])){
			$this->paramCount = $DbContext["param_count"];
		}


	}

	function getSqlState(){
		return $this->sqlState;
	}

	function getDriverCode(){
		return $this->driverCode;
	}

	function getDriverMsg(){
		return $this->driverMsg;
	}

	function getErrorCode(){
		return $this->errorCode;
	}

	function getQueryString(){
		return $this->queryString;
	}

	function getParamCount(){
		return $this->paramCount;
	}


}
