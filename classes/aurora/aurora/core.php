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
		$test_classname_only = TRUE;
		if (
		  !Aurora_Type::is_aurora($class, $test_classname_only) AND
		  !Aurora_Type::is_collection($class, $test_classname_only)
		)
			return FALSE;
		// change the class name to model classname
		$class = Aurora_Type::model($class);
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
		// serialize Model or Colleciton into encodeable object
		$json_obj = static::json_serialize($object);
		// encode the serialized object
		return json_encode($json_obj);
	}
	/**
	 * JSON decode a JSON string into a Model or a Collection
	 *
	 * @param Model/Collection $object
	 * @return string
	 */
	public static function json_decode($json_str, $type) {
		// decode the json_str to a stdClass or array
		$json_obj = json_decode($json_str);
		// Deserialize decoded object into Model or Colleciton
		return static::json_deserialize($json_obj, $type);
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
		// Get the Aurora_ class for this object
		$au = static::factory($object, 'aurora');
		// test if it implements json_serialize
		if ($au instanceof Interface_Aurora_JSON_Serialize)
			return $au->json_serialize($object);

		// Set the mode of the serialization
		if (Aurora_Type::is_model($object)) {
			$mode = 'from_model';
		} else if (Aurora_Type::is_collection($object)) {
			$mode = 'from_collection';
		} else {
			throw new InvalidArgumentException("Variable not an instance of Model or Collection");
		}
		// Return an stdClass or an array of stdClass
		return Aurora_StdClass::$mode($object);
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
		// Get the Aurora_ class for this object
		$au = Aurora_Type::is_aurora($type) ? $type : static::factory($type, 'aurora');
		// test if it implements json_decode
		if ($au instanceof Interface_Aurora_JSON_Deserialize)
			return $au->json_deserialize($json);

		// process: convert json to model or collection
		return (is_array($json)) ?
		  // if JSON is array return Collection
		  Aurora_StdClass::to_collection($json, Aurora_Type::collection($type)) :
		  // otherwise (if it is of type stdClass) return Model
		  Aurora_StdClass::to_model($json, Aurora_Type::model($type));
	}
	/**
	 * Check if your Model has an ID.
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
	 * Factory method to create Models or
	 * Collections from the common_name
	 *
	 * @param string $classname
	 * @param string $type "model" or "collection"
	 * @return Model/Collection
	 */
	public static function factory($classname, $type = NULL) {
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
		$result = Aurora_Database::select($au, $params);
		$count = count($result);

		if (empty($mode)) {
			$mode = ($count == 1) ? 'model' : 'collection';
		}

		if ($mode == 'model') {
			if (!$count)
			// should we Aurora_Hook::call($au, 'after_load', FALSE)? if no result?
				return false;
			$model = is_object($object) ? $object : static::factory($object, 'model');
			$au->db_retrieve($model, $result[0]);
			Aurora_Hook::call($au, 'after_load', $model);
			return $model;
		} else {
			$collection = is_object($object) ? $object : static::factory($object, 'collection');
			foreach ($result as $row) {
				$model = static::factory($object, 'model');
				$au->db_retrieve($model, $row);
				$collection->add($model);
			}
			// run after hook if exists
			Aurora_Hook::call($au, 'after_load', $collection);
			return $collection;
		}
	}
	/**
	 * Save a model or a collection to the database
	 * using Aurora
	 *
	 * @param Model/Collection $object
	 * @return Model/Collection
	 */
	public static function save($object) {
		// Get the Aurora_ class for this object
		$au = static::factory($object, 'aurora');
		// run before hook if exists
		Aurora_Hook::call($au, 'before_save', $object);
		// deep save by looping through the collection
		if (Aurora_Type::is_collection($object)) {
			foreach ($object as $model) {
				static::save($model);
			}
			return $object;
		}
		// test if the model is new
		if (static::is_new($object)) {
			// if it's new create in the database
			static::create($object);
		} else {
			// if it has an ID update model in database
			static::update($object);
		}
		// run after hook if exists
		Aurora_Hook::call($au, 'after_save', $object);
		return $object;
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
		// Get the Aurora_ class for this object
		$au = static::factory($object, 'aurora');
		// run before hook if exists
		Aurora_Hook::call($au, 'before_delete', $object);
		// deep delete by looping through the collection
		if (Aurora_Type::is_collection($object)) {
			foreach ($object as $model) {
				static::delete($model);
			}
		}
		// Test if $model is_new and throw exception if TRUE
		if (static::is_new($object))
			throw new Kohana_Exception('Can not delete a new Model.');
		// Get the value $pk (the ID) of the $model
		$pk = Aurora_Property::get_pkey($object);
		// Run the delete query
		$result = Aurora_Database::delete($au, $pk);
		// run after hook if exists
		Aurora_Hook::call($au, 'after_delete', $object);
		// return result
		return $result;
	}
	/**
	 * Inserts a model to the database
	 *
	 * @param Model $model
	 * @return Model
	 */
	protected static function create($model) {
		// Get the Aurora_ class for this model
		$au = static::factory($model, 'aurora');
		// run before hook if exists
		Aurora_Hook::call($au, 'before_create', $model);
		// Get the $row array from Aurora_ to be inserted
		$row = $au->db_persist($model);
		// Run the insert query
		$result = Aurora_Database::insert($au, $row);
		if ($result) {
			$inserted_id = $result[0];
			Aurora_Property::set_pkey($model, $inserted_id);
		}
		// run after hook if exists
		Aurora_Hook::call($au, 'after_create', $model);
		// return $model
		return $model;
	}
	/**
	 * Updates a model in the database
	 *
	 * @param Model $model
	 * @return Model
	 */
	protected static function update($model) {
		// Get the Aurora_ class for this model
		$au = static::factory($model, 'aurora');
		// run before hook if exists
		Aurora_Hook::call($au, 'before_update', $model);
		// Get the $row array from Aurora_ to be inserted
		$row = $au->db_persist($model);
		// Get the value $pk (the ID) of the $model
		$pk = Aurora_Property::get_pkey($model);
		// Run the update query
		Aurora_Database::update($au, $row, $pk);
		// run after hook if exists
		Aurora_Hook::call($au, 'after_update', $model);
		// return $model
		return $model;
	}
}
