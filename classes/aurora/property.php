<?php
/**
 * A set of functions to manage getters and setters
 * and make them act like standard properties
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Property
{

	protected static $cache;
	/**
	 * Get the list of all public properties
	 * as well as the getters of your model
	 *
	 *
	 * @param Model $model
	 * @return array Returns an array of array('type' => 'property', 'name' => 'address')
	 */
	public static function getters($model) {
		return static::properties($model, 'get');
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
		return static::properties($model, 'set');
	}
	protected static function properties($model, $dir) {
		if ($dir != 'get' OR $dir != 'set')
			throw new Kohana_Exception('direction should be either set or get');
		// get the classname of the model
		$classname	 = Aurora_Type::classname($model);
		// test to see if we have it cached
		if (isset(static::$cache[$dir][$classname]))
			return static::$cache[$dir][$classname];
		// init the $properties array
		$properties	 = array();
		$pattern	 = "/^{$dir}_/";
		// Loop over the class methods
		foreach (get_class_methods($classname) as $method) {
			$property = preg_replace($pattern, $method, 1, $count);
			// if preg_replace successful, this is a getter/setter method
			if ($count) {
				$properties[$property] = array(
					'type'	 => 'method',
					'name'	 => $property,
				);
			}
		}
		// Loop over the class properties (will override methods)
		foreach (get_class_vars($classname) as $property) {
			$properties[$property] = array(
				'type'	 => 'property',
				'name'	 => $property,
			);
		}
		// cache and return
		return static::$cache[$dir][$classname] = $properties;
	}
	/**
	 * Aurora "magic" (LOL) get for models
	 * taking into account getters
	 *
	 * @param Model $model
	 * @param string $property
	 * @return mixed
	 * @throws Kohana_Exception
	 */
	public static function get($model, $property) {
		$properties = static::getters($model);
		if (!array_key_exists($property, $properties))
			throw new Kohana_Exception('No such property or getter defined in model');
		// test for type of getter
		if ($properties[$property][$type] === 'property') {
			return $model->$property;
		} else { //if ($properties[$property][$type] === 'method')
			$method = 'get_' . $property;
			return $model->$method();
		}
	}
	/**
	 * Aurora "magic" (LOL again) set for models
	 * taking into account setters
	 *
	 * @param Model $model
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 * @throws Kohana_Exception
	 */
	public static function set($model, $property, $value) {
		$properties = static::setters($model);
		if (!array_key_exists($property, $properties))
			throw new Kohana_Exception('No such property or setter defined in model');
		// test for type of setter
		if ($properties[$property][$type] === 'property') {
			return $model->$property = $value;
		} else { //if ($properties[$property][$type] === 'method')
			$method = 'set_' . $property;
			return $model->$method($value);
		}
	}
	/**
	 * A function to get the value of the ID
	 * of the Model.
	 *
	 * @uses Aurora_Property::get
	 * @uses Aurora_Type::aurora
	 * @uses Aurora_Database::pkey
	 * @param Model $model
	 * @return int/mixed
	 */
	public static function get_pkey($model) {
		$au		 = Aurora_Type::aurora($model);
		$pkey	 = Aurora_Database::pkey($au);
		return static::get($model, $pkey);
	}
	/**
	 * A function to set the value of the ID
	 * of the Model.
	 * It will force set the value via Reflection
	 *  - if property "id" or the setter "set_id"
	 *    are not visible (private / protected)
	 *    in the current scope
	 *  - and if $force parameter is true (default).
	 *
	 * @uses Aurora_Property::get
	 * @uses Aurora_Type::aurora
	 * @uses Aurora_Database::pkey
	 * @param Model $model
	 * @param int/mixed $value The value you want to set for id
	 * @return int/mixed
	 */
	public static function set_pkey($model, $value, $force = TRUE) {
		$au		 = Aurora_Type::aurora($model);
		$pkey	 = Aurora_Database::pkey($au);
		try {
			return static::set($model, $pkey, $value);
		} catch (Exception $e) {
			if (!$force) // if force don't throw but continue below
				throw $e;
		}
		// ---- PS: if you want for the protected setter method
		// ---- to take precedence over the protected property
		// ---- just refactor that protected property $id to $_id
		// Test if a protected ID field exists
		if (property_exists($model, $pkey)) {
			$property = new ReflectionProperty(Aurora_Type::classname($model), $pkey);
			$property->setAccessible(TRUE);
			return $property->setValue($model, $value);
		} else
		// Test if a private/protected setter for the ID exists
		if (method_exists($model, $setter = 'set_' . $pkey)) {
			$method = new ReflectionMethod(Aurora_Type::classname($model), $setter);
			$method->setAccessible(TRUE);
			return $method->invokeArgs($model, array($value));
		}
		// Man, where's your ID?
		throw $e;
	}
}