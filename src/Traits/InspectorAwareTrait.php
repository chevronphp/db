<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Exceptions\DBException;
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

}