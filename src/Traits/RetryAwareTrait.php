<?php

namespace Chevron\DB\Traits;

use \Chevron\DB\Exceptions\DBException;
/**
 * Implements a few read only shortcut methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait RetryAwareTrait {

	/**
	 * the number of times to retry after a mysql error
	 */
	protected $numRetries = 5;

	/**
	 * method to set the number of retries after an error
	 * @param int $num The number of retries
	 * @return
	 */
	function setNumRetries($num){
		$this->numRetries = (int)$num;
	}

}