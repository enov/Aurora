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
 * @license http://license.enov.ws/mit MIT
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
		return isset($aurora::$config) ? $aurora::$config : 'pdo';
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
		return isset($aurora::$table) ? $aurora::$table : strtolower(Aurora_Type::common($aurora)) . 's';
	}
	/**
	 * returns the column name of the
	 * primary key defined in the table
	 * should be overriden if not 'id'
	 *
	 * @return string
	 */
	public static function pkey($aurora) {
		return isset($aurora::$pkey) ? $aurora::$pkey : 'id';
	}
	/**
	 * Main Query (DB View) to be used by the
	 * Model and corresponding Collection
	 *
	 * @return Database_Query_Builder_Select
	 */
	public static function db_view($aurora) {
		return
		  is_callable("$aurora::db_view") ?
		  $aurora::db_view() :
		  DB::select()->from(static::table($aurora));
	}
	/**
	 * DATABASE CRUD OPERATIONS
	 */
	public static function select($aurora, $param) {
		// prepare variables
		$table	 = static::table($aurora);
		$config	 = static::config($aurora);
		$query	 = static::db_view($aurora);
		// prepare parameters
		if (!empty($param)) {
			if (is_scalar($param))
				$param = array(static::pkey($aurora) => $param);
			foreach ($param as $column => $value) {
				if (is_scalar($value))
					$query = $query->where($table . '.' . $column, '=', $value);
				else if (is_callable($value))
					$value($query);
			}
		}
		return $query->execute($config);
	}
	public static function insert($aurora, $row) {
		// prepare variables
		$table	 = static::table($aurora);
		$config	 = static::config($aurora);
		// run insert
		return DB::insert($table)->columns(array_keys($row))->values(array_values($row))->execute($config);
	}
	public static function update($aurora, $row, $pk) {
		// prepare variables
		$table	 = static::table($aurora);
		$pkey	 = static::pkey($aurora);
		$config	 = static::config($aurora);
		// run update
		return DB::update($table)->set($row)->where($pkey, '=', $pk)->execute($config);
	}
	public static function delete($aurora, $pk) {
		// prepare variables
		$table	 = static::table($aurora);
		$pkey	 = static::pkey($aurora);
		$config	 = static::config($aurora);
		// run delete
		return DB::delete($table)->where($pkey, '=', $pk)->execute($config);
	}
}
