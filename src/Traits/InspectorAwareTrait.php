<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Interfaces\PDOWrapperInterface as Wrapper;
/**
 * Implements a few read only shortcut methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait InspectorAwareTrait {

	/**
	 * a lambda to execute before executing a query, usefule for debugging
	 */
	protected $inspector;

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
	 *
	 */
	protected function inspect(Wrapper $PDOWrapper, $query, array $data = null){
		if(is_callable($this->inspector)){
			return call_user_func($this->inspector, $PDOWrapper, $query, $data);
		}
	}

}