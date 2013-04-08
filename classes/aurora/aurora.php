<?php

class Aurora_Aurora
{
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
		$classname		 = Aurora_Type::classname($object);
		$custom_view	 = str_replace('_', '/', strtolower($classname));
		$default_view	 = "aurora/json/$mode";
		$file			 = Kohana::find_file('views', $custom_view) ? $custom_view : $default_view;
		// Prepare the data to pass to the view
		$data			 = array($mode => $object);
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
	public static function load($class_name, $params) {
		if (is_scalar($params))
			$mode	 = 'model';
		if (Aurora_Type::is_model($class_name))
			$mode	 = 'model';
		if (Aurora_Type::is_collection($class_name))
			$mode	 = 'collection';
		// Get the Aurora_ class for this model
		$au		 = Aurora_Type::aurora($class_name);
		// Run select query
		$result	 = Aurora_Database::select($au, $params);
		$count	 = count($result);
		if (!$count) {
			return false;
		} else if (empty($mode)) {
			$mode	 = ($count	 = 1) ? 'model' : 'collection';
		}

		if ($mode == 'model') {
			$model = is_object($class_name) ? $class_name : static::factory($class_name, 'model');
			$au::db_to_model($model, $result[0]);
			return $model;
		} else {
			$collection = is_object($class_name) ? $class_name : static::factory($class_name, 'collection');
			foreach ($result as $row) {
				$model = static::factory($class_name, 'model');
				$au::db_to_model($model, $row);
				$collection->add($model);
			}
			return $collection;
		}
	}
	public static function save($object) {
		// deep save by looping through the collection
		if (Aurora_Type::is_collection($object)) {
			foreach ($object as $model) {
				static::save($model);
			}
		}
		// test if the model is new
		if (static::is_new($object)) {
			// if it's new create in the database
			static::create($object);
		} else {
			// if it has an ID update model in database
			static::update($object);
		}
		return $object;
	}
	public static function delete($object) {
		// deep delete by looping through the collection
		if (Aurora_Type::is_collection($object)) {
			foreach ($object as $model) {
				static::delete($model);
			}
		}
		// Test if $model is_new and throw exception if TRUE
		if (static::is_new($object))
			throw new Kohana_Exception('Can not delete a new Model.');
		// Get the Aurora_ class for this model
		$au	 = Aurora_Type::aurora($object);
		// Get the $pk (the ID) of the $model
		$pk	 = Aurora_Property::get_pkey($object);
		// Run the delete query
		return Aurora_Database::delete($au, $pk);
	}
	protected static function create($model) {
		// Get the Aurora_ class for this model
		$au		 = Aurora_Type::aurora($model);
		// Get the $row array from Aurora_ to be inserted
		$row	 = $au::db_from_model($model);
		// Run the insert query
		$result	 = Aurora_Database::insert($au, $row);
		if ($result) {
			$inserted_id = $result[0];
			Aurora_Property::set_pkey($model, $inserted_id);
		}
		return $model;
	}
	protected static function update($model) {
		// Get the Aurora_ class for this model
		$au	 = Aurora_Type::aurora($model);
		// Get the $row array from Aurora_ to be inserted
		$row = $au::db_from_model($model);
		$pk	 = Aurora_Property::get_pkey($model);
		// Run the update query
		Aurora_Database::update($au, $row, $pk);
		return $model;
	}
}