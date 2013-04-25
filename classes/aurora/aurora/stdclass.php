<?php defined('SYSPATH') or die('No direct script access.');
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
		$pattern_get = '/^get_/';
		$properties	 = array();
		// TODO: something like Aurora_Property to encapsulate logic
		// maybe something similar and more generic Aurora_PKey?
		$methods	 = get_class_methods($model);
		foreach ($methods as $method) {
			if (preg_match($pattern_get, $method))
				$properties[]	 = $method;
		}
		$properties		 = array_merge($properties, get_class_vars(Aurora_Type::classname($model)));


		$std = new stdClass();
		foreach ($properties as $prop) {
			$std_prop = preg_replace($pattern_get, '', $prop);
			if (Aurora_Type::is_model($model->$prop)) {
				$std->$std_prop = static::from_model($model->$prop);
			} else if (Aurora_Type::is_collection ($model->$prop)) {
				$std->$std_prop = $model->$prop->to_stdArray();
			} else if ($model->$prop instanceof DateTime) {
				$std->$std_prop = Date::format_iso8601($model->$prop);
			} else {
				$std->$std_prop = $model->$prop;
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
		if (is_string($model))
			$model				 = static::factory($model);
		$model_properties	 = get_object_vars($model);
		foreach ($stdObj as $prop) {
			// if $prop = id force set id
			static::set_pk($model, $stdObj->$prop);
			// TODO: if from_stdProp_ exists in $aurora, use it
			// if property exists in model set it
			if (isset($model_properties[$prop])) {
				$model->$prop	 = $stdObj->$prop;
				continue;
			}
			// if setter exists in model
			// have its type hint, cast, then set
			$setter			 = 'set_' . $prop;
			if (method_exists($model, $setter) && is_callable(array($model, $setter))) {
				$typehint = Aurora_Reflection::typehint($model, $setter);
				if (is_null($typehint)) {
					$model->$setter($stdObj->$prop);
					continue;
				} else if (is_subclass_of($typehint, 'Collection')) {
					$model->$setter(static::to_collection($stdObj->$prop, $typehint));
					continue;
				} else { // must be a model, do we need to test anything here?
					$model->$setter(static::to_model($stdObj->$prop, $typehint));
					continue;
				}
			}
			// TODO: test for PSR-2 style setters
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
	public static function from_collection(Collection $collection) {
		return array_map(
		  // apply from_model
		  function($m) {
			  return static::from_model($m);
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