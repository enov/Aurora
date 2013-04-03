<?php

class Aurora_Reflection
{

	protected static $rflx_cache = array();
	protected static function rflx_class($obj) {
		$classname = is_string($obj) ? $obj : static::name_class($obj);
		if (key_exists($classname, static::$rflx_cache))
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
		$rflx_class	 = static::rflx_class($class);
		$rflx_method = $rflx_class->getMethod($method);
		$parameters	 = $rflx_method->getParameters();
		$param		 = $parameters[0];
		return is_null($param->getClass()) ? NULL : $param->getClass()->getName();
	}
}