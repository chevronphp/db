<?php

namespace Chevron\DB\Traits;

trait ExeQueryTrait {

	/**
	 * method to debug (if necessary), execute a query, retrying if necessary,
	 * will prepare the query before execution, returning the result
	 *
	 * @param string $query The query to execute
	 * @param array $map The data to pass to the query
	 * @param bool $in A toggle to pre parse the query to use the IN clause
	 * @param int $fetch The fetch method
	 * @return array
	 */
	function exeQuery($query, array $map, $in = false, $fetch = \PDO::FETCH_BOTH){

		if($in){
			// this syntax (returning an array with two values) is a little more
			// esoteric than i'd prefer ... but it works
			list( $query, $map ) = $this->in( $query, $map );
		}

		// redundant for IN queries since the data is already flat
		$data = $this->filterData($map);

		$this->inspect($this, $query, $data);

		$statement = $this->prepare($query);
		// if( !($query InstanceOf \PDOStatement ) ){}

		$statement->setFetchMode($fetch);

		$i = 1;
		foreach ($data as $value) {
			$paramType = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
			$statement->bindValue($i, $value, $paramType);
			$i += 1;
		}

		$retry = $this->numRetries ?: 5;
		while( $retry-- ){
			try{
				$success = $statement->execute();
			}catch(\Exception $e){
				throw new DBException($this->printErr($statement, count($data)));
			}

			if( $success ){
				return $statement;
			}

			// let the driver decide to retry on error codes
			if($this->driver->shouldRetry( $statement->errorCode() )){
				continue;
			}

			throw new DBException($this->printErr($statement, count($data)));
		}

		throw new DBException("Query Failed after 5 attempts:\n\n{$query}");

	}


}