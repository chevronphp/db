<?php

namespace Chevron\DB\Traits;
/**
 * Implements a few query building helper methods
 *
 * @package Chevron\PDO
 * @author Jon Henderson
 */
trait QueryHelperTrait {

	/**
	 * method to pre-parse a query to inject a variable number of "?" tokens into
	 * the string based on a variable number of pices of data
	 * @param string $query The string of the query
	 * @param array $map The data map to parse
	 * @return array
	 */
	protected function in($query, array $map){

		$iter1 = new \ArrayIterator($map);
		$final = $replacements = array();
		foreach( $iter1 as $key => $value ){
			if(is_array($value)){
				$replacements[] = rtrim(str_repeat("?, ", count($value)), ", ");
			}
			$final = array_merge($final, (array)$value);
		}
		$_SQL = vsprintf($query, $replacements);
		return array( $_SQL, $final );
	}

	/**
	 * method to parse the map of data into a flat array for use with prepared
	 * statements--this is intended for non IN queries
	 * @return array
	 */
	protected function filterData(){

		$final = array();
		$maps = func_get_args();
		foreach($maps as $map){
			foreach($map as $key => $value){
				if( is_scalar($value) ){
					$final[] = $value;
				}
			}
		}
		return $final;
	}

	/**
	 * method to parse a map of maps of data into a flat array for use with prepared
	 * statements--this is intended for use with IN clauses
	 * @param array $rows An array of arrays
	 * @return array
	 */
	protected function filterMultiData(array $rows){

		$final = array();
		foreach($rows as $row){
			$tmp = $this->filterData($row);
			$final = array_merge($final, $tmp);
		}
		return $final;
	}

	/**
	 * method to parse an array of data into tokens, organized by the parenthetical
	 * syntax
	 * @param array $map The data
	 * @param int $multiple How many times to repeat the paren pairs
	 * @return array
	 */
	protected function parenPairs(array $map, $multiple){

		$tmp = $this->mapColumns($map);
		$columns = array_keys($tmp);
		$tokens  = array_values($tmp);

		$columns = sprintf("(`%s`)", implode("`, `", $columns));
		$tokens  = sprintf("(%s)",   implode(", ",   $tokens));

		if($multiple){
			$tokens = rtrim(str_repeat("{$tokens},", $multiple), ",");
		}

		return array( $columns, $tokens );
	}

	/**
	 * method to parse an array of data into tokens, organized by the equals
	 * syntax
	 * @param array $map The data
	 * @param string $sep The seperator to use
	 * @return string
	 */
	protected function equalPairs(array $map, $sep = ", "){

		$tmp = $this->mapColumns($map);
		$columns = array_keys($tmp);
		$tokens  = array_values($tmp);

		$count = count($columns);
		for( $i = 0; $i < $count; ++$i ){
			$temp[] = "`{$columns[$i]}` = {$tokens[$i]}";
		}

		return implode($sep, $temp);
	}

	/**
	 * method to take an array of data and count/filter it to determine how many given
	 * data points ought to be passed as tokens vs injected (unescaped) into the
	 * query
	 * @param array $map The data
	 * @return array
	 */
	protected function mapColumns(array $map){

		$columns = $tokens = array();
		foreach($map as $key => $value){
			if(is_array($value)){
				// check for bool switch
				if(array_key_exists(0, $value)){
					if($value[0] !== true) continue;

					$columns[$key] = $key;
					$tokens[$key]  = $value[1];

				}else{
					// if another array recurse
					$tmp = $this->mapColumns($value);
					$columns = array_merge($columns, array_keys($tmp));
					$tokens  = array_merge($tokens, array_values($tmp));
				}
			}else{
				if(is_null($value)) continue;
				// catch non-null scalars
				$columns[$key] = $key;
				$tokens[$key]  = "?";
			}
		}
		// because $columns will inevitably contain duplicate values, once the
		// two arrays are combined, they will collapse/uniquify. #darkcorner
		return array_combine($columns, $tokens);
	}
}