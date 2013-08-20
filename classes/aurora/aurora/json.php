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
 * @license http://license.enov.ws/mit MIT
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
			$result = static::_serialize($object, $au);
			// if $object is a Collection
		} else {
			$callback = function ($model) use ($au) {
				  return static::_serialize($model, $au);
			  };
			$result = array_map($callback, array_values($object->to_array()));
		}
		// return
		return $result;
	}
	protected static function _serialize($model, $aurora) {
		// test if it implements json_serialize
		if ($aurora instanceof Interface_Aurora_JSON_Serialize)
			return $aurora->json_serialize($model);
		// Return an stdClass
		else if ($model instanceof JsonSerializable)
			return $model->jsonSerialize();
		else
			return Aurora_StdClass::from_model($model);
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
		if (is_array($json)) {
			// test if it implements Interface_Aurora_JSON_Deserialize
			if ($au instanceof Interface_Aurora_JSON_Deserialize)
				$result = array_map(array($au, 'json_deserialize'), $json);
			else
				$result = Aurora_StdClass::to_collection($json, Aurora_Type::collection($type));
		} else {
			// test if it implements Interface_Aurora_JSON_Deserialize
			if ($au instanceof Interface_Aurora_JSON_Deserialize)
				$result = $au->json_deserialize($json);
			else
				$result = Aurora_StdClass::to_model($json, Aurora_Type::model($type));
		}
		// return
		return $result;
	}
}