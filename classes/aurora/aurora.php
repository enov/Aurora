<?php

class Aurora_Aurora
{
	protected static function get_default_representation($object, $format = 'json') {
		if ($object instanceof Model) {
			return "represent/$format/model";
		} else if ($object instanceof Collection) {
			return "represent/$format/collection/";
		}
	}
	public static function json_encode($object) {
		// Set the mode of the representation
		if (Aurora_Type::is_model($object)) {
			$mode = 'model';
		} else if (Aurora_Type::is_collection($object)) {
			$mode = 'collection';
		} else {
			throw new View_Exception("Variable $object not an instance of Model or Collection");
		}
		// Find the custom view file for object representation
		// if custom file is not set use default
		$classname = Aurora_Type::classname($object);
		$file = str_replace('_', '/', strtolower($classname));
		if (!Kohana::find_file('views', $file))
			$file = self::get_default_representation($object, 'json');
		// Prepare the data to pass to the view
		$data = array($mode => $object);
		// Create the View and return it
		return View::factory($file, $data);
	}
	public static function json_decode($common_name, $json) {
		$json = json_decode($json);
		return (is_array($json)) ?
		  // if JSON is array return Collection
		  Aurora_StdClass::to_collection($json, Aurora_Type::collection($common_name)) :
		  // otherwise (if it is of type stdClass) return Model
		  Aurora_StdClass::to_model($json, Aurora_Type::model($common_name));
	}
	public static function is_new($model) {
		return (bool) Aurora_Property::get_pkey($model);
	}
	public static function factory($classname, $type = NULL) {
		if (!empty($type))
			$classname = Aurora_Type::$type($classname);
		return new $classname();
	}
	public static function fetch($model, $id) {
		// Get the Aurora_ class for this model
		$au = Aurora_Type::aurora($model);
		// Run select query
		$result = Aurora_Database::select($au, $id);
		if (!$result->count())
			return false;
		$au::db_to_model($model, $result[0]);
		return $model;
	}
	public static function find($collection, $filter) {
		// Get the Aurora_ class for this model
		$au = Aurora_Type::aurora($model);
		// Run select query
		$result = Aurora_Database::select($au, $id);
		if (!$result->count())
			return false;
		$au::db_to_model($collection, $result[0]);
		return $collection;
	}
	public static function save($model) {
		if (static::is_new($model)) {
			static::create($model);
		} else {
			static::update($model);
		}
		return $model;
	}
	public static function delete($model) {
		// Test if $model is_new and throw exception if TRUE
		if (static::is_new($model))
			throw new Kohana_Exception('Can not delete a new Model.');
		// Get the Aurora_ class for this model
		$au = Aurora_Type::aurora($model);
		// Get the $pk (the ID) of the $model
		$pk = Aurora_Property::get_pkey($model);
		// Run the delete query
		return Aurora_Database::delete($au, $pk);
	}
	protected static function create($model) {
		// Get the Aurora_ class for this model
		$au = Aurora_Type::aurora($model);
		// Get the $row array from Aurora_ to be inserted
		$row = $au::db_from_model($model);
		// Run the insert query
		$result = Aurora_Database::insert($au, $row);
		if ($result) {
			$inserted_id = $result[0];
			Aurora_Property::set_pkey($model, $inserted_id);
		}
		return $model;
	}
	protected static function update($model) {
		// Get the Aurora_ class for this model
		$au = Aurora_Type::aurora($model);
		// Get the $row array from Aurora_ to be inserted
		$row = $au::db_from_model($model);
		$pk = Aurora_Property::get_pkey($model);
		// Run the update query
		Aurora_Database::update($au, $row, $pk);
		return $model;
	}
}