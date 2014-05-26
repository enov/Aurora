<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * A set of functions to manage reflection
 * on Models
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Reflection
{

	protected static $rflx_cache = array();

	protected static function rflx_class($obj) {
		$classname = is_string($obj) ? $obj : Aurora_Type::classname($obj);
		if (isset(static::$rflx_cache[$classname]))
			return static::$rflx_cache[$classname];
		else
			return static::$rflx_cache[$classname] = new ReflectionClass($obj);
	}

	/**
	 * Get the typehint of the first parameter
	 * for the specified method
	 *
	 * @param string $class
	 * @param string $method
	 * @return mixed the name of the class
	 */
	public static function typehint($class, $method) {
		$rflx_class = static::rflx_class($class);
		$rflx_method = $rflx_class->getMethod($method);
		$parameters = $rflx_method->getParameters();
		$param = $parameters[0];
		return is_null($param->getClass()) ? NULL : $param->getClass()->getName();
	}

	/**
	 * Get the typehint of the first parameter
	 * for the specified method
	 *
	 * @param string $class
	 * @param string $method
	 * @return mixed the name of the class
	 */
	public static function constructor_has_parameter($class) {
		$rflx_class = static::rflx_class($class);
		// get constructor
		$rflx_method = $rflx_class->getConstructor();
		if (empty($rflx_method))
			return FALSE;
		// get parameters
		$parameters = $rflx_method->getParameters();
		if (empty($parameters))
			return FALSE;
		// return TRUE
		return TRUE;
	}

}