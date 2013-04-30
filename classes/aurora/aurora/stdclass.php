<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Utility class to convert Models and Controllers
 * from and to objects of type stdClass
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Aurora_StdClass
{
	/**
	 * Converts a model to stdClass
	 * takes care of getters
	 *
	 * @param Model $model
	 * @return stdClass
	 */
	public static function from_model($model) {
		if (!Aurora_Type::is_model($model))
			throw new Kohana_Exception('Invalid model');
		// Get the list of properties
		$props = Aurora_Property::getters($model);
		// loop through the list of props
		foreach ($props as $prop => $arrProp) {
			// get the value
			$value = Aurora_Property::get($model, $prop);
			// if $value is a Model
			if (Aurora_Type::is_model($value)) {
				$std->$std_prop = static::from_model($value);
			}
			// if $value is a Collection
			else if (Aurora_Type::is_collection($value)) {
				$std->$std_prop = static::from_collection($value);
			}
			// Special care for DateTime
			// -- is this generally acceptable?
			else if ($value instanceof DateTime) {
				$std->$std_prop = Date::format_iso8601($value);
			}
			// if scalar?
			else {
				$std->$std_prop = $value;
			}
		}
		return $std;
	}
	/**
	 * Converts an stdClass to the specified Model
	 *
	 * @param stdClass $stdObj
	 * @param Model/string $model
	 * @return Model
	 */
	public static function to_model($stdObj, $model) {
		$model	 = (is_string($model)) ? Aurora_Core::factory($model) : $model;
		$au		 = Aurora_Type::aurora($model);
		// Get the list of properties
		$props	 = Aurora_Property::setters($model);
		// loop through the list of props
		foreach ($props as $prop => $arrProp) {
			// test if property exists
			if (!property_exists($stdObj, $prop))
				continue;
			// get the value
			$value = $stdObj->$prop;
			// if property is primary key
			if ($prop == Aurora_Database::pkey($au)) {
				Aurora_Property::set_pkey($model, $value);
				continue;
			}
			// if property is setter
			if ($arrProp['type'] == 'method') {
				$setter = 'set_' . $prop;
				$typehint = Aurora_Reflection::typehint($model, $setter);
				if (Aurora_Type::is_collection($typehint)) {
					$value = static::to_collection($value, $typehint);
				} else if (Aurora_Type::is_model($typehint)) {
					$value = static::to_model($value, $typehint);
				}
			}
			Aurora_Property::set($model, $prop, $value);
		}
		return $model;
	}
	/**
	 * Converts a collection (an object of type Collection)
	 * to an array of stdClass
	 *
	 * @param Collection $collection
	 * @return array
	 */
	public static function from_collection(Aurora_Collection $collection) {
		$static = get_called_class();
		return array_map(
		  // apply from_model
		  function($m) {
			  return $static::from_model($m);
		  },
		  // on each element of the internal array
		  $collection->to_array()
		);
	}
	/**
	 * Converts an array of stdClass to
	 * the specified collection
	 *
	 * @param array $std_array
	 * @param Collection/string $collection
	 * @return Collection
	 */
	public static function to_collection(array $std_array, $collection) {
		if (is_string($collection))
			$collection		 = static::factory($collection);
		/* @var $collection Collection */
		$collection->clear();
		$model_classname = Aurora_Type::model($collection->classname());
		foreach ($std_array as $stdObj) {
			$m = static::to_model($stdObj, $model_classname);
			$collection->add($m);
		}
		return $collection;
	}
}