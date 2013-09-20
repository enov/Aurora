<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Main class to interface with the database.
 * It uses the Aurora_ classes.
 * The DB CRUD operations are unaware of models.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Database
{

	/**
	 * returns the database config group
	 *
	 * @return string
	 */
	public static function config($aurora) {
		return isset($aurora->config) ? $aurora->config : 'default';
	}

	/**
	 * returns the database config group
	 *
	 * @return string
	 */
	public static function transactional($aurora) {
		return isset($aurora->transactional) ? (bool) $aurora->transactional : TRUE;
	}

	/**
	 * returns the name of the table
	 * associated with this model
	 * should be overriden in case of
	 * nomenclature does not follow convention:
	 * Model Name:      =>    Table Name:
	 * Model_Patient    =>    patients
	 *
	 * @return string
	 */
	public static function table($aurora) {
		return isset($aurora->table) ? $aurora->table : strtolower(Aurora_Type::cname($aurora)) . 's';
	}

	/**
	 * returns the column name of the
	 * primary key defined in the table
	 * should be overriden if not 'id'
	 *
	 * @return string
	 */
	public static function pkey($aurora) {
		return isset($aurora->pkey) ? $aurora->pkey : 'id';
	}

	/**
	 * Returns the key/index of the id in the database result row
	 *
	 * @return string
	 */
	public static function row_pkey($aurora) {
		// prepare variables
		$table = static::table($aurora);
		$pkey = static::pkey($aurora);
		$config = static::config($aurora);
		// load database config
		$config = Kohana::$config->load("database")->get($config);
		// Getting the var
		$fetch_table_names = Arr::path($config, 'connection.fetch_table_names', FALSE);
		return $fetch_table_names ? $table . '.' . $pkey : $pkey;
	}

	/**
	 * Main Query (DB View) to be used by the
	 * Model and corresponding Collection
	 *
	 * @return Database_Query_Builder_Select
	 */
	public static function qview($aurora) {

		if (isset($aurora->qview))
			return $aurora->qview;
		else
			return is_callable(array($aurora, 'qview')) ?
			  $aurora->qview() :
			  DB::select()->from(static::table($aurora));
	}

	/**
	 * DATABASE CRUD OPERATIONS
	 */
	public static function select($aurora, $param) {
		$config = static::config($aurora);
		// prepare variables
		$query = static::build_query($aurora, $param);
		// execute
		return $query->execute($config);
	}

	protected static function build_query($aurora, $param) {
		$table = static::table($aurora);
		$query = static::qview($aurora);
		$pkey = static::pkey($aurora);
		// if param empty return $query
		if (empty($param))
			return $query;
		// if param is just a scalar value, we will consider it an ID
		if (is_scalar($param))
			return $query->where($table . '.' . $pkey, '=', $param);
		// if param is an array of scalars, we will consider an array of IDs
		if (
		  (is_array($param)) AND
		  ($param === array_filter($param, "is_scalar")) AND
		  (array_keys($param) === array_filter(array_keys($param), 'is_int'))
		) {
			$param = array_values($param);
			return $query->where($table . '.' . $pkey, 'IN', $param);
		}
		// if param is callable, call it by passing query as argument
		if (is_callable($param))
			return $param($query);
		// if param is an array
		if (is_array($param)) {
			foreach ($param as $column => $value) {
				if (is_scalar($value))
					$query = $query->where($table . '.' . $column, '=', $value);
				else if (
				  (is_array($value)) AND
				  ($value === array_filter($value, "is_scalar")) AND
				  (array_keys($value) === array_filter(array_keys($param), 'is_int'))
				)
					$query = $query->where($table . '.' . $column, 'IN', $value);
				else if (is_callable($value))
					$value($query);
			}
			return $query;
		}
		return $query;
	}

	public static function insert($aurora, $row) {
		// prepare variables
		$table = static::table($aurora);
		$config = static::config($aurora);
		// run insert
		return DB::insert($table)->columns(array_keys($row))->values(array_values($row))->execute($config);
	}

	public static function update($aurora, $row, $pk) {
		// prepare variables
		$table = static::table($aurora);
		$pkey = static::pkey($aurora);
		$config = static::config($aurora);
		// run update
		return DB::update($table)->set($row)->where($pkey, '=', $pk)->execute($config);
	}

	public static function delete($aurora, $pk) {
		// prepare variables
		$table = static::table($aurora);
		$pkey = static::pkey($aurora);
		$config = static::config($aurora);
		// prepare ids to handle multiple deletes
		$IDs = is_scalar($pk) ? array($pk) : $pk;
		// run delete
		return DB::delete($table)->where($pkey, 'IN', $IDs)->execute($config);
	}

	/**
	 * DATABASE TRANSACTIONS
	 *
	 * ------ note ------
	 *
	 * should we use?
	 *
	 * DB::expr('BEGIN')->execute($config);
	 *
	 * for code consistency with DB above? or probably such expressions lead to
	 * database engine specific SQL queries?
	 *
	 * ------ end note ------
	 */

	/**
	 * START TRANSACTION
	 *
	 * @param Aurora $aurora
	 */
	public static function begin($aurora) {
		// prepare variables
		$transactional = static::transactional($aurora);
		$config = static::config($aurora);
		// start transaction
		return
		  ($transactional) ?
		  Database::instance($config)->begin() :
		  FALSE;
	}

	/**
	 * COMMIT TRANSACTION
	 *
	 * @param Aurora $aurora
	 */
	public static function commit($aurora) {
		// prepare variables
		$transactional = static::transactional($aurora);
		$config = static::config($aurora);
		// commit transaction
		return
		  ($transactional) ?
		  Database::instance($config)->commit() :
		  FALSE;
	}

	/**
	 * ROLLBACK TRANSACTION
	 *
	 * @param Aurora $aurora
	 */
	public static function rollback($aurora) {
		// prepare variables
		$transactional = static::transactional($aurora);
		$config = static::config($aurora);
		// rollback transaction
		return
		  ($transactional) ?
		  Database::instance($config)->rollback() :
		  FALSE;
	}

}
