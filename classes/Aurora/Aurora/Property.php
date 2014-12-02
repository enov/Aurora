<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * A set of functions to manage getters and setters
 * and make them act like standard properties
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Property
{

	protected static $cache_prop;
	protected static $cache_pkey;

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
		if ($dir != 'get' AND $dir != 'set')
			throw new Kohana_Exception('direction should be either set or get');
		// get the classname of the model
		$classname = Aurora_Type::classname($model);
		// test to see if we have it cached
		if (isset(static::$cache_prop[$dir][$classname]))
			return static::$cache_prop[$dir][$classname];
		// init the $properties array
		$properties = array();
		$pattern = "/^{$dir}_/";
		// Loop over the class methods
		foreach (get_class_methods($classname) as $method) {
			$property = preg_replace($pattern, '', $method, 1, $count);
			// if preg_replace successful, this is a getter/setter method
			if ($count) {
				$typehint = ($dir === 'set') ? Aurora_Reflection::typehint($classname, $method) : NULL;
				$properties[$property] = array(
					'type' => 'method',
					'name' => $property,
					'hint' => $typehint,
				);
			}
		}
		// Loop over the class properties (will override methods)
		foreach (get_class_vars($classname) as $property => $value) {
			$properties[$property] = array(
				'type' => 'property',
				'name' => $property,
				'hint' => NULL,
			);
		}
		// cache and return
		return static::$cache_prop[$dir][$classname] = $properties;
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
		if ($properties[$property]['type'] === 'property') {
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
		if ($properties[$property]['type'] === 'property') {
			return $model->$property = $value;
		} else { //if ($properties[$property][$type] === 'method')
			$method = 'set_' . $property;
			return $model->$method($value);
		}
	}

	/**
	 * A function to get the DESCRIPTION of the ID
	 * PROPERTY or GETTER of the Model
	 *
	 * @param Model $model
	 */
	public static function pkey_property($model) {
		$classname = Aurora_Type::classname($model);
		if (isset(static::$cache_pkey[$classname])) {
			$pkey = static::$cache_pkey[$classname];
		} else {
			$au = Aurora_Type::aurora($model);
			$pkey = Aurora_Database::pkey($au);
			static::$cache_pkey[$classname] = $pkey;
		}
		$all_props = static::getters($model);
		return $all_props[$pkey];
	}

	/**
	 * Get the VALUE of the ID of the Model.
	 *
	 * @uses Aurora_Property::get
	 * @uses Aurora_Type::aurora
	 * @uses Aurora_Database::pkey
	 * @param Model $model
	 * @return int/mixed
	 */
	public static function get_pkey($model) {
		$classname = get_class($model);
		if (isset(static::$cache_pkey[$classname])) {
			$pkey = static::$cache_pkey[$classname];
		} else {
			$au = Aurora_Type::aurora($model);
			$pkey = Aurora_Database::pkey($au);
			static::$cache_pkey[$classname] = $pkey;
		}
		if (isset($model->$pkey))
			return $model->$pkey;
		else {
			$method = 'get_' . $pkey;
			// do we need to test??? performance??
			if (method_exists($model, $method) AND is_callable(array($model, $method)))
				return $model->$method();
		}
		throw new Kohana_Exception('Primary key not defined in model');
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
		$au = Aurora_Core::factory($model);
		$pkey = Aurora_Database::pkey($au);

		$properties = static::setters($model);
		if (array_key_exists($pkey, $properties)) {

			// test for type of setter
			if ($properties[$pkey]['type'] === 'property') {
				return $model->$pkey = $value;
			} else { //if ($properties[$property][$type] === 'method')
				$method = 'set_' . $pkey;
				return $model->$method($value);
			}
		} else if ($force) {
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
		}
		// Man, where's your ID?
		throw $e;
	}
	/**
	 * old version of the above function.
	 * Will keep it handy here to test and benchmark.
	 * This implementation seems to be slow because it relies on static::set
	 * and !!! catches the exception !!! before forcing
	 */
	public static function _old_set_pkey($model, $value, $force = TRUE) {
		$au = Aurora_Type::aurora($model);
		$pkey = Aurora_Database::pkey($au);
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
	/**
	 *
	 * @param mixed $object
	 * @param string $property
	 * @return type
	 */
	public function &ref($object, $property) {
		$value = & Closure::bind(function & () use ($property) {
			  return $this->$property;
		  }, $object, $object)->__invoke();
		return $value;
	}
}
