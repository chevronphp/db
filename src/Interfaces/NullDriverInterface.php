<?php

namespace Chevron\DB\Interfaces;
/**
 * An interface defining the functionality required by a driver
 *
 * @package Chevron\PDO\MySQL
 * @author Jon Henderson
 */
interface NullDriverInterface extends DriverInterface {

	function isNullDriver();

}