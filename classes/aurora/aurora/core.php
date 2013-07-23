<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * The Aurora class. Publishes the main API.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Aurora_Core
{
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
				$object = static::json_serialize($object);
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
			$result = static::json_deserialize($json_obj, $type);
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
	 * Get a json_encodable object from Model or Collection
	 * Delegates to Aurora if custom implementation exists
	 *
	 * usage:
	 *
	 *     // Get the JSON object of type stdClass or array of stdClass
	 *     $json_obj = AU::json_serialize($model);
	 *     // encode to string
	 *     $json_str = json_encode($json_obj);
	 *     // output
	 *     $this->response->body($json_str);
	 *
	 * @param Model/Collection $object
	 * @return mixed a json_encodable object, probably a stdClass
	 * @throws InvalidArgumentException if $object is not a model or collection
	 */
	public static function json_serialize($object) {
		// start profiling
		$benchmark = Aurora_Profiler::start($object, __FUNCTION__);

		try {
			// Set the mode of the serialization
			if (Aurora_Type::is_model($object)) {
				$mode = 'from_model';
			} else if (Aurora_Type::is_collection($object)) {
				$mode = 'from_collection';
			} else {
				throw new InvalidArgumentException("Argument not an instance of Model or Collection");
			}

			// Get the Aurora_ class for this object
			$au = static::factory($object, 'aurora');
			// test if it implements json_serialize
			if ($au instanceof Interface_Aurora_JSON_Serialize)
				return $au->json_serialize($object);


			// Return an stdClass or an array of stdClass
			$std = Aurora_StdClass::$mode($object);
		} catch (Exception $e) {
			Aurora_Profiler::delete($benchmark);
			throw $e;
		}

		// end profiling
		Aurora_Profiler::stop($benchmark);

		// return
		return $std;
	}
	/**
	 * Convert from JSON to Model or Collection
	 *
	 *     // for example, if $json_string = '{ id: 3, ... }';
	 *     // Decode from JSON string
	 *     $json = json_decode($json_string);
	 *
	 *     // OR just convert to $object of type Model or Collection
	 *     $object = AU::json_deserialize("Calendar_Event", $json);
	 *
	 * @param stdClass/array $json The JSON object of type stdClass or array of stdClass
	 * @param string/Aurora $type The type to serialize into the $json object
	 * @return Model/Collection
	 */
	public static function json_deserialize($json, $type) {
		// start profiling
		$benchmark = Aurora_Profiler::start($type, __FUNCTION__);

		try {

			// Get the Aurora_ class for this object
			$au = Aurora_Type::is_aurora($type) ? $type : static::factory($type, 'aurora');
			// test if it implements json_decode
			if ($au instanceof Interface_Aurora_JSON_Deserialize)
				return $au->json_deserialize($json);

			// process: convert json to model or collection
			$result = (is_array($json)) ?
			  // if JSON is array return Collection
			  Aurora_StdClass::to_collection($json, Aurora_Type::collection($type)) :
			  // otherwise (if it is of type stdClass) return Model
			  Aurora_StdClass::to_model($json, Aurora_Type::model($type));
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
	public static function factory($classname, $type = NULL) {
		if (empty($classname))
			throw new InvalidArgumentException('Invalid classname');
		if (!empty($type))
			$classname = Aurora_Type::$type($classname);
		return new $classname();
	}
	/**
	 * Load a model or collection from database
	 * using Aurora
	 *
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
			// run before hook if exists
			Aurora_Hook::call($au, 'before_load', $params);
			// find out mode:
			// whether this function will load a model or a collection
			if (is_null($params))
				$mode = 'collection';
			if (is_scalar($params))
				$mode = 'model';
			if (Aurora_Type::is_model($object))
				$mode = 'model';
			if (Aurora_Type::is_collection($object))
				$mode = 'collection';
			// Run select query
			$rowset = Aurora_Database::select($au, $params);
			$count = count($rowset);

			if (empty($mode)) {
				$mode = ($count == 1) ? 'model' : 'collection';
			}

			if ($mode == 'model') {
				if (!$count)
				// should we Aurora_Hook::call($au, 'after_load', FALSE)? if no result?
					return false;
				$model = is_object($object) ? $object : static::factory($object, 'model');
				$au->db_retrieve($model, $rowset[0]);
				Aurora_Hook::call($au, 'after_load', $model);
				$result = $model;
			} else {
				/* @var $collection Aurora_Collection */
				$collection = is_object($object) ? $object : static::factory($object, 'collection');
				$model_name = Aurora_Type::model($object);
				$array = & $collection->to_array();
				$row_pkey = Aurora_Database::row_pkey($au);
				foreach ($rowset as $row) {
					$pkey = $row[$row_pkey];
					if (isset($array[$pkey]))
						$model = $array[$pkey];
					else {
						$model = new $model_name;
						$array[$pkey] = $model;
					}
					$au->db_retrieve($model, $row);
				}
				// run after hook if exists
				Aurora_Hook::call($au, 'after_load', $collection);
				$result = $collection;
			}
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
