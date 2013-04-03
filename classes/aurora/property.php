<?php

/**
 * A set of functions to manage the VALUE of the
 * primary key, i.e. ID of your Models.
 *
 * @package Aurora
 * @author Samuel Demirdjian <s@enov.ws>
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Property
{
	const PATTERN_GET = 'get_';
	const PATTERN_SET = 'set_';

	protected static $cache_getters;
	protected static $cache_setters;
	/**
	 * Get the list of all public properties
	 * as well as the getters of your model
	 *
	 *
	 * @param Model $model
	 * @return array Returns an array of array('type' => 'property', 'name' => 'address')
	 */
	public static function getters($model) {
		$classname = Aurora_Type::classname($model);
		if (isset(static::$cache_getters[$classname]))
			return static::$cache_getters[$classname];
		// init the $properties array
		$properties = array();
		$methods = get_class_methods($model);
		foreach ($methods as $method) {
			if (preg_match(static::PATTERN_GET, $method))
				$properties[] = $method;
		}
		return static::$cache_getters[$classname] = array_merge($properties, get_class_vars($classname));
	}
	/**
	 * Get the list of all public properties
	 * as well as the setters of your model
	 *
	 *
	 * @param Model $model
	 * @return array Returns an array of array('type' => 'property', 'name' => 'address')
	 */
	public static function setters($model) {

	}
	/**
	 *
	 */
	public static function get($model, $property) {

	}
	/**
	 * A function to get the value of the ID
	 * of the Model. Will look for the property
	 * at first, then for a getter.
	 *
	 * @param Model $model
	 * @return int/mixed
	 */
	public static function get_pkey($model) {
		$au = Aurora_Type::aurora($model);
		$pkey = Aurora_Database::pkey($au);
		$public_props = get_class_vars(Aurora_Type::classname($model));
		$getter = 'get_' . $pkey;
		// 1. Test if a public ID field exists
		if (in_array($pkey, $public_props)) {
			return $model->$pkey;
		} else
		// 2. Test if a public getter for the ID exists
		if (method_exists($model, $getter) && is_callable(array($model, $getter))) {
			return $model->$getter;
		}
		// NO public id? probably something wrong
		throw new Kohana_Exception('Model without a public ID');
	}
	/**
	 * A function to set the value of the ID
	 * of the Model. It will force set the value
	 * via Reflection, in case the
	 * property "id" or the setter "set_id"
	 * are not visible (private / protected)
	 * in the current scope
	 *
	 * @param Model $model
	 * @param mixed $value The value you want to set for id
	 * @return int/mixed
	 */
	public static function set_pkey($model, $value) {
		$au = Aurora_Type::aurora($model);
		$pkey = Aurora_Database::pkey($au);
		$public_props = get_class_vars(Aurora_Type::classname($model));
		$setter = 'set_' . $pkey;
		// 1. Test if a public ID field exists
		if (in_array($pkey, $public_props)) {
			return $model->$pkey = $value;
		} else
		// 2. Test if a public setter for the ID exists
		if (method_exists($model, $setter) AND is_callable(array($model, $setter))) {
			return $model->$setter($value);
		} else
		// ---- PS: if you want for the protected setter method
		// ---- to take precedence over the protected property
		// ---- just refactor your protected property $id to $_id
		// ---- to jump to 4. and avoid "entering" this third if
		// 3. Test if a protected ID field exists
		if (property_exists(Aurora_Type::model($model), $pkey)) {
			$property = new ReflectionProperty(Aurora_Type::classname($model), $pkey);
			$property->setAccessible(TRUE);
			return $property->setValue($model, $value);
		} else
		// 4. Test if a private/protected setter for the ID exists
		if (method_exists($model, $setter)) { // AND ! is_callable(array($model, $setter))
			$method = new ReflectionMethod(Aurora_Type::classname($model), $setter);
			$method->setAccessible(TRUE);
			return $method->invokeArgs($model, array($value));
		}
		// Man, where's your ID?
		throw new Kohana_Exception("Model without ID, or misconfigured pkey in $au!");
	}
}