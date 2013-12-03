<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * The Aurora class. Publishes the main API.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Core
{

	// Release version and codename
	const VERSION = '1.1-beta';
	const CODENAME = 'mighty-math-powers';
	/**
	 * Compatibility with Kohana version
	 * Comma separated list of versions
	 */
	const KOMPATIBILITY = '3.3';
	/**
	 * Aurora custom auto-loader
	 *
	 * In order not to end up with lots of files
	 *
	 * Special treatment for Collections and Auroras
	 *
	 * @see Kohana_Core::auto_load
	 * @param string $class
	 */
	public static function auto_load($class) {
		// This is only for Auroras and Collections. A test before proceeding
		$pattern_aurora = '/^Aurora_/';
		$pattern_collection = '/^Collection_/';
		if (
		  !preg_match($pattern_aurora, $class) AND
		  !preg_match($pattern_collection, $class)
		)
			return FALSE;
		// change the class name to model classname
		$class = preg_replace(array($pattern_aurora, $pattern_collection), 'Model_', $class);
		// Call standard Kohana auto-loading mechanism
		Kohana::auto_load($class);
	}
	/**
	 * JSON encode a Model or a Collection
	 *
	 * @param Model/Collection $object
	 * @return string
	 */
	public static function json_encode($object) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {
			// serialize Model or Colleciton into encodeable object
			if (Aurora_Type::is_model($object) OR Aurora_Type::is_collection($object))
				$object = Aurora_JSON::serialize($object);
			// encode the serialized object
			$json_str = json_encode($object);
		} catch (Exception $e) {
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $json_str;
	}
	/**
	 * JSON decode a JSON string into a Model or a Collection
	 *
	 * @param Model/Collection $object
	 * @return string
	 */
	public static function json_decode($json_str, $type) {
		// start profiling
		$benchmark = Aurora_Profiler::start($type, __FUNCTION__);

		try {

			// decode the json_str to a stdClass or array
			$json_obj = json_decode($json_str);
			// Deserialize decoded object into Model or Colleciton
			$result = Aurora_JSON::deserialize($json_obj, $type);
		} catch (Exception $e) {
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $result;
	}
	/**
	 * Check if your Model is new.
	 * A new Model is not loaded from the database and does not have an ID
	 *
	 *     // usage
	 *     $is_new = AU::is_new($model);
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public static function is_new($model) {
		if (!Aurora_Type::is_model($model))
			throw new Kohana_Exception('Tested $model is not a Model.');
		return !Aurora_Property::get_pkey($model);
	}
	/**
	 * Check if your Model is loaded.
	 * A loaded Model has an ID
	 *
	 *     // usage
	 *     $loaded = AU::is_loaded($model);
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public static function is_loaded($model) {
		if (!Aurora_Type::is_model($model))
			throw new Kohana_Exception('Tested $model is not a Model.');
		return (bool) Aurora_Property::get_pkey($model);
	}
	/**
	 * Factory method to create Models or
	 * Collections from the common_name
	 *
	 * @param string $classname
	 * @param string $type "model" or "collection"
	 * @return Model/Collection
	 */
	public static function factory($classname, $type = 'aurora') {
		if (empty($classname))
			throw new InvalidArgumentException('Invalid classname');
		$classname = Aurora_Type::$type($classname);
		return new $classname();
	}
	/**
	 * Load a model or collection from database
	 * using Aurora
	 *
	 * @uses Aurora_Core::find_mode find out whether to return Model or Collection
	 * @param string/aurora $object
	 * @param scalar/array/callable $params
	 * @return Model/Collection
	 */
	public static function load($object, $params = NULL) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {

			// Get the Aurora_ class for this object
			$au = Aurora_Type::is_aurora($object) ? $object : static::factory($object, 'aurora');
			// find out mode:
			$mode = static::find_mode($object, $params);
			// run before hook if exists
			Aurora_Hook::call($au, 'before_load', $params);
			// Run select query
			$rowset = Aurora_Database::select($au, $params);
			$count = count($rowset);

			if ($mode == 'model') {
				if (!$count)
				// should we Aurora_Hook::call($au, 'after_load', FALSE)? if no result?
					return false;
				$model = Aurora_Type::is_model($object) ? $object : static::factory($object, 'model');
				$au->db_retrieve($model, $rowset[0]);
				$result = $model;
			} else {
				/* @var $collection Aurora_Collection */
				$collection = Aurora_Type::is_collection($object) ? $object : static::factory($object, 'collection');
				$model_name = Aurora_Type::model($object);
				$array = & $collection->to_array();
				$row_pkey = Aurora_Database::row_pkey($au);
				foreach ($rowset as $row) {
					$pkey = $row[$row_pkey];
					$model = new $model_name;
					$au->db_retrieve($model, $row);
					$array[$pkey] = $model;
				}
				$result = $collection;
			}
			// run after hook if exists
			Aurora_Hook::call($au, 'after_load', $result);
		} catch (Exception $e) {
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $result;
	}
	public static function load_experimental($object, $params = NULL) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {

			// Get the Aurora_ class for this object
			$au = Aurora_Type::is_aurora($object) ? $object : static::factory($object, 'aurora');
			// find out mode:
			$mode = static::find_mode($object, $params);
			// run before hook if exists
			Aurora_Hook::call($au, 'before_load', $params);
			// Run select query
			$rowset = Aurora_Database::select($au, $params);
			$count = count($rowset);

			if ($mode == 'model') {
				if (!$count)
				// should we Aurora_Hook::call($au, 'after_load', FALSE)? if no result?
					return false;
				$model = Aurora_Type::is_model($object) ? $object : static::factory($object, 'model');
				$au->db_retrieve($model, $rowset[0]);
				$result = $model;
			} else {
				/* @var $collection Aurora_Collection */
				$collection = Aurora_Type::is_collection($object) ? $object : static::factory($object, 'collection');
				$model_name = Aurora_Type::model($object);
				$array = & $collection->to_array();
				$row_pkey = Aurora_Database::row_pkey($au);
				foreach ($rowset as $model) {
					$pkey = $model->get_id();
					$array[$pkey] = $model;
				}
				$result = $collection;
			}
			// run after hook if exists
			Aurora_Hook::call($au, 'after_load', $result);
		} catch (Exception $e) {
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $result;
	}
	/**
	 * helper class for load function in order to find out
	 * whether to return model or collection
	 *
	 * @param type $object
	 * @param type $params
	 * @return mixed 'model', 'collection', FALSE
	 */
	protected static function find_mode($object, $params) {
		// by default one should expect a collection (many models) just like
		// when selecting from database, one expects a rowset (many rows)
		$mode = 'collection';
		// if the parameter is scalar load('Event', 1)
		// or if we are force-requesting a model
		if ((is_scalar($params)) OR (Aurora_Type::is_model($object, TRUE)))
			$mode = 'model';
		// return
		return $mode;
	}
	/**
	 * Save a model or a collection to the database
	 * using Aurora
	 *
	 * @param Model/Collection $object
	 * @return Model/Collection
	 */
	public static function save($object) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {

			// Get the Aurora_ class for this object
			$au = static::factory($object, 'aurora');
			// start transaction
			Aurora_Database::begin($au);
			// run before hook if exists
			Aurora_Hook::call($au, 'before_save', $object);
			// deep save by looping through the collection
			if (Aurora_Type::is_collection($object)) {
				foreach ($object as $model)
				// run _save for each model
					static::_save($model, $au);
			} else {
				// run _save once
				static::_save($object, $au);
			}
			// run after hook if exists
			Aurora_Hook::call($au, 'after_save', $object);
		} catch (Exception $e) {
			Aurora_Database::rollback($au);
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// commit
		Aurora_Database::commit($au);

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $object;
	}
	protected static function _save($model, $aurora) {
		return
		  // test if the model is new
		  static::is_new($model) ?
		  // if it's new create in the database
		  static::create($model, $aurora) :
		  // if it has an ID update model in database
		  static::update($model, $aurora);
	}
	/**
	 * Delete a model or a collection from the database
	 * using Aurora
	 *
	 * @param Model/Collection $object
	 * @return int? number of affected rows?
	 * @throws Kohana_Exception
	 */
	public static function delete($object) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {
			// Get the Aurora_ class for this object
			$au = static::factory($object, 'aurora');
			// start transaction
			Aurora_Database::begin($au);
			// run before hook if exists
			Aurora_Hook::call($au, 'before_delete', $object);
			// deep delete by looping through the collection
			$IDs = array();
			if (Aurora_Type::is_collection($object)) {
				foreach ($object as $model)
					$IDs[] = static::_delete($model);
			} else {
				$IDs[] = static::_delete($object);
			}
			// Run the delete query
			$result = Aurora_Database::delete($au, $IDs);
			// run after hook if exists
			Aurora_Hook::call($au, 'after_delete', $object);

			//
		} catch (Exception $e) {
			Aurora_Database::rollback($au);
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// commit
		Aurora_Database::commit($au);

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return result
		return $result;
	}
	protected static function _delete($model) {
		// Test if $model is_new and throw exception if TRUE
		if (static::is_new($model))
			throw new Kohana_Exception('Can not delete a new Model.');
		// Get the value $pk (the ID) of the $model
		return Aurora_Property::get_pkey($model);
	}
	/**
	 * Inserts a model to the database
	 *
	 * @param Model $model
	 * @return Model
	 */
	protected static function create($model, $aurora) {
		// run before hook if exists
		Aurora_Hook::call($aurora, 'before_create', $model);
		// Get the $row array from Aurora_ to be inserted
		$row = $aurora->db_persist($model);
		// Run the insert query
		$result = Aurora_Database::insert($aurora, $row);
		if ($result) {
			$inserted_id = $result[0];
			Aurora_Property::set_pkey($model, $inserted_id);
		}
		// run after hook if exists
		Aurora_Hook::call($aurora, 'after_create', $model);
		// return $model
		return $model;
	}
	/**
	 * Updates a model in the database
	 *
	 * @param Model $model
	 * @return Model
	 */
	protected static function update($model, $aurora) {
		// run before hook if exists
		Aurora_Hook::call($aurora, 'before_update', $model);
		// Get the $row array from Aurora_ to be inserted
		$row = $aurora->db_persist($model);
		// Get the value $pk (the ID) of the $model
		$pk = Aurora_Property::get_pkey($model);
		// Run the update query
		Aurora_Database::update($aurora, $row, $pk);
		// run after hook if exists
		Aurora_Hook::call($aurora, 'after_update', $model);
		// return $model
		return $model;
	}
}
