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
		// Set the mode of the serialization
		if (Aurora_Type::is_model($object)) {
			// Get the Aurora_ class for this object
			$au = Aurora_Core::factory($object, 'aurora');
			// test if it implements json_serialize
			if ($au instanceof Interface_Aurora_JSON_Serialize)
				$std = $au->json_serialize($object);
			// Return an stdClass
			else
				$std = Aurora_StdClass::from_model($object);
		} else if (Aurora_Type::is_collection($object)) {
			// Get the Aurora_ class for this object
			$au = Aurora_Core::factory($object, 'aurora');
			// test if it implements json_serialize
			if ($au instanceof Interface_Aurora_JSON_Serialize)
				$std = array_map(
				  // apply from_model
				  array($au, 'json_serialize'),
				  // on each element of the internal array
				  array_values($object->to_array())
				);
			// Return an array of stdClass
			else
				$std = Aurora_StdClass::from_collection($object);
		} else {
			throw new InvalidArgumentException("Argument not an instance of Model or Collection");
		}

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
	 *     $object = AU::json()->deserialize("Calendar_Event", $json);
	 *
	 * @param stdClass/array $json The JSON object of type stdClass or array of stdClass
	 * @param string/Aurora $type The type to serialize into the $json object
	 * @return Model/Collection
	 */
	public static function deserialize($json, $type) {
		// Get the Aurora_ class for this object
		$au = Aurora_Type::is_aurora($type) ? $type : Aurora_Core::factory($type, 'aurora');
		// test if it implements json_decode
		if ($au instanceof Interface_Aurora_JSON_Deserialize)
			return $au->json_deserialize($json);

		// process: convert json to model or collection
		$result = (is_array($json)) ?
		  // if JSON is array return Collection
		  Aurora_StdClass::to_collection($json, Aurora_Type::collection($type)) :
		  // otherwise (if it is of type stdClass) return Model
		  Aurora_StdClass::to_model($json, Aurora_Type::model($type));
		// return
		return $result;
	}
}