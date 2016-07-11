<?php

namespace Chevron\DB\Traits;

use Capstone\PDO\Exceptions\PDOException;

trait LazyPDOConnectionTrait {

	/** @var \PDO|null  */
	protected $connection;

	/** @var callable|null */
	protected $connectionFactory;

	/**
	 * @param \PDO|callable $connection
	 */
	public function setConnection( $connection ) {
		if( $connection instanceof \PDO ) {
			$this->connection = $connection;
		} elseif( is_callable($connection) ) {
			$this->connectionFactory = $connection;
		} else {
			throw new \InvalidArgumentException('Connection should either be callable or a \PDO connection');
		}
	}

	/**
	 * @return \PDO
	 */
	public function getConnection() {
		if( $this->connection instanceof \PDO ) {
			return $this->connection;
		}

		if( is_callable($this->connectionFactory) ) {
			$conn = call_user_func($this->connectionFactory);
			if( $conn instanceof \PDO ) {
				$this->connection = $conn;
				$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				return $this->connection;
			}
		}

		throw new \PDOException("Invalid PDO connection");
	}

}
