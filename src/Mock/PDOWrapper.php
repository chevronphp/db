<?php

namespace Chevron\DB\Mock;

use \Chevron\DB\Interfaces;
/**
 * This class implements the WrapperInterface but every method returns
 * whatever data you've stored in $next (via next()) in a FIFO way.
 * This should allow for the testing of DB dependent functionality by
 * eliminating a live DB.
 *
 * @package Chevron\DB
 * @author Jon Henderson
 */
class PDOWrapper implements Interfaces\PDOWrapperInterface {

	protected $next = array();

	/**
	 * Operate a FIFO stack of return values
	 * @param mixed $next The value to return
	 * @return mixed
	 */
	function next($next){
		$this->next[] = $next;
	}
	/**
	 * Enforce a FIFO stack
	 * @return mixed
	 */
	function shift(){
		return array_shift($this->next);
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function put($table, array $map, array $where = array()){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function insert($table, array $map){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function update($table, array $map, array $where = array()){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function replace($table, array $map){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function on_duplicate_key($table, array $map, array $where){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function multi_insert($table, array $map){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function multi_replace($table, array $map){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function exe($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function assoc($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function row($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function scalar($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function scalars($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keypair($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keyrow($query, array $map = array(), $in = false){
		return $this->shift();
	}

	/**
	 * For documentation, consult the PDOWrapperInterface
	 */
	function keyrows($query, array $map = array(), $in = false){
		return $this->shift();
	}

}