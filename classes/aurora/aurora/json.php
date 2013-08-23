<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * The Aurora JSON class. Serializes and deserializes Models and Collections
 * to stdClass or array of stdClass, to be ready for JSON encoding/decoding
 *
 * Note that the words "serialize" and "deserialize" in Aurora, is in context
 * of JSON, and has nothing to do with string conversion for storage. It rather
 * means to convert to and from and object that is JSON encodable.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_JSON
{

	/**
	 * Get a JSON encodable object from Model or Collection
	 * Delegates job to your Aurora if custom implementation exists
	 *
	 * usage:
	 *
	 *     // Get the JSON object of type stdClass or array of stdClass
	 *     $json_obj = AU::json()->serialize($model);
	 *     // encode to string
	 *     $json_str = json_encode($json_obj);
	 *     // output
	 *     $this->response->body($json_str);
	 *
	 * @param Model/Collection $object
	 * @return mixed a json_encodable object, probably a stdClass
	 * @throws InvalidArgumentException if $object is not a model or collection
	 */
	public static function serialize($object) {
		if (
		  !(
		  ($mode_model = Aurora_Type::is_model($object)) OR
		  (Aurora_Type::is_collection($object))
		  )
		) {
			throw new InvalidArgumentException("Argument not an instance of Model or Collection");
		}
		// Get the Aurora_ class for this object
		$au = Aurora_Core::factory($object, 'aurora');
		// if $object is a Model
		if ($mode_model) {
			$result = static::from_model($object, $au);
			// if $object is a Collection
		} else {
			$result = static::from_collection($object, $au);
		}
		// return
		return $result;
	}

	/**
	 * Convert from JSON to Model or Collection
	 *
	 *     // for example, if $json_string = '{ id: 3, ... }';
	 *     // Decode from JSON string
	 *     $json = json_decode($json_string);
	 *
	 *     // OR just convert to $object of type Model or Collection
	 *     $object = AU::json()->deserialize("Calendar_Event", $json);
	 *
	 * @param stdClass/array $json The JSON object of type stdClass or array of stdClass
	 * @param string/Aurora $type The type to serialize into the $json object
	 * @return Model/Collection
	 */
	public static function deserialize($json, $type) {
		// Get the Aurora_ class for this object
		$au = Aurora_Type::is_aurora($type) ? $type : Aurora_Core::factory($type, 'aurora');
		// test if $json is an array
		if (is_array($json))
			$result = static::to_collection($json, $type, $au);
		else
			$result = static::to_model($json, $type, $au);
		// return
		return $result;
	}

	/**
	 * Converts a model to something that is JSON serializable
	 * basically an object of type stdClass
	 * takes care of getters and converts them to standard properties
	 *
	 * @param Model $model
	 * @return stdClass
	 */
	public static function from_model($model, $aurora = NULL) {
		// test for argument. Do we need this? Performance?
		if (!Aurora_Type::is_model($model))
			throw new InvalidArgumentException('Can not proceed conversion. Not a valid Model');
		// create Aurora if empty
		if (empty($aurora))
			$aurora = Au::factory ($model);
		// test if it implements json_serialize
		if ($aurora instanceof Interface_Aurora_JSON_Serialize)
			return $aurora->json_serialize($model);
		// Return an stdClass
		if ($model instanceof JsonSerializable)
			return $model->jsonSerialize();
		// Get the list of properties
		$props = Aurora_Property::getters($model);
		// Create the stdClass object
		$std = new stdClass();
		// loop through the list of props
		foreach ($props as $prop => $arrProp) {
			// get the value
			$value = Aurora_Property::get($model, $prop);
			// if $value is a Model
			if (Aurora_Type::is_model($value)) {
				$std->$prop = static::from_model($value, $aurora);
			}
			// if $value is a Collection
			else if (Aurora_Type::is_collection($value)) {
				$std->$prop = static::from_collection($value, $aurora);
			}
			// if $value is JsonSerializable
			else if ($value instanceof JsonSerializable) {
				$std->$prop = $value->jsonSerialize();
			}
			// Special care for DateTime. Is this generally acceptable?
			else if ($value instanceof DateTime) {
				$std->$prop = $value->format(DATE_ISO8601);
			}
			// if scalar?
			else {
				$std->$prop = $value;
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
	public static function to_model($stdObj, $model, $aurora = NULL) {
		$model = Aurora_Type::is_model($model) ? $model : Aurora_Core::factory($model, 'model');
		// create Aurora if empty
		if (empty($aurora))
			$aurora = Au::factory ($model);
		// use custom json_deserialize if implemented
		if ($aurora instanceof Interface_Aurora_JSON_Deserialize)
			return $aurora->json_deserialize($stdObj);
		// set primary key
		$pkey = Aurora_Database::pkey($aurora);
		if (property_exists($stdObj, $pkey)) {
			Aurora_Property::set_pkey($model, $stdObj->$pkey);
		}  // Get the list of properties
		$props = Aurora_Property::setters($model);
		// loop through the list of props
		foreach ($props as $prop => $arrProp) {
			// test if property exists
			if (!property_exists($stdObj, $prop))
				continue;
			// get the value
			$value = $stdObj->$prop;
			// if property is primary key
			if ($prop == Aurora_Database::pkey($aurora)) {
				// we already did this above
				continue;
			}
			// if property is setter
			if ($arrProp['type'] == 'method' AND $value !== NULL) {
				$setter = 'set_' . $prop;
				$typehint = Aurora_Reflection::typehint($model, $setter);
				$classname_only = TRUE;
				if (Aurora_Type::is_collection($typehint, $classname_only)) {
					$value = static::to_collection($value, $typehint, $aurora);
				} else if (Aurora_Type::is_model($typehint, $classname_only)) {
					$value = static::to_model($value, $typehint, $aurora);
				} else if ($typehint === 'DateTime') {
					$value = new DateTime($value);
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
	public static function from_collection(Aurora_Collection $collection, $aurora = NULL) {
		// create Aurora if empty
		if (empty($aurora))
			$aurora = Au::factory ($collection);
		$static = __CLASS__;
		return array_map(
		  // apply from_model
		  function ($model) use ($static, $aurora) {
			  return $static::from_model($model, $aurora);
		  },
		  // on each element of the internal array
		  array_values($collection->to_array())
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
	public static function to_collection($std_array, $collection, $aurora = NULL) {
		$collection = Aurora_Type::is_collection($collection) ? $collection : Aurora_Core::factory($collection, 'collection');
		// create Aurora if empty
		if (empty($aurora))
			$aurora = Au::factory ($collection);
		/* @var $collection Collection */
		$collection->clear();
		$model_classname = Aurora_Type::model($collection);
		foreach ($std_array as $stdObj) {
			$m = static::to_model($stdObj, $model_classname, $aurora);
			$collection->add($m);
		}
		return $collection;
	}

}