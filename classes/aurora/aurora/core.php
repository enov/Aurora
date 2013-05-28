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
	 * Get a Kohana View with the JSON representation
	 * of your model or Collection
	 *
	 *     // Get the JSON view
	 *     $view = AU::json_encode($model);
	 *
	 *     // render the view
	 *     $this->response->body($view->render());
	 *
	 * @param Model/Collection $object
	 * @return View a Kohana View with JSON rendered object
	 * @throws View_Exception
	 */
	public static function json_encode($object) {
		// Get the Aurora_ class for this object
		$au = static::factory($object, 'aurora');
		// test if it implements json_encode
		if ($au instanceof Interface_Aurora_JSON_Encode)
			return $au->json_encode($object);

		// Set the mode of the representation
		if (Aurora_Type::is_model($object)) {
			$mode = 'model';
		} else if (Aurora_Type::is_collection($object)) {
			$mode = 'collection';
		} else {
			throw new View_Exception("Variable not an instance of Model or Collection");
		}
		// Find the custom view file for object representation
		// if custom file is not set use default
		$classname = Aurora_Type::classname($object);
		$custom_view = str_replace('_', '/', strtolower($classname));
		$default_view = "aurora/json/$mode";
		$file = Kohana::find_file('views', $custom_view) ? $custom_view : $default_view;
		// Prepare the data to pass to the view
		$data = array($mode => $object);
		// Create the View and return it
		return View::factory($file, $data);
	}
	/**
	 * Convert from JSON to Model or Collection
	 *
	 *     // for example, if $json_string = '{ id: 3, ... }';
	 *     // Get the model from JSON string
	 *     $model = AU::json_decode("Calendar_Event", $json_string);
	 *
	 *     // else if $json_string = '[{ id: 3, ... }, ...]';
	 *     // get the collection from JSON string
	 *     $collection = AU::json_decode("Calendar_Event", $json_string);
	 *
	 *     // OR just convert to $object
	 *     $object = AU::json_decode("Calendar_Event", $json_string);
	 *     // then test
	 *     Aurora_Type::is_model($object); // is_collection($object)
	 *
	 * @param string $common_name
	 * @param string $json_str The JSON string to convert from
	 * @return Model/Collection
	 */
	public static function json_decode($common_name, $json_str) {
		// Get the Aurora_ class for this object
		$au = static::factory($common_name, 'aurora');
		// test if it implements json_decode
		if ($au instanceof Interface_Aurora_JSON_Decode)
			return $au->json_decode($json_str);

		// convert to JSON string to stdClass or array
		$json = json_decode($json_str);

		// process: convert json to model or collection
		return (is_array($json)) ?
		  // if JSON is array return Collection
		  Aurora_StdClass::to_collection($json, Aurora_Type::collection($common_name)) :
		  // otherwise (if it is of type stdClass) return Model
		  Aurora_StdClass::to_model($json, Aurora_Type::model($common_name));
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
