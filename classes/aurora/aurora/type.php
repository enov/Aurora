<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Utility class for finding related Models, Collections, "Aurora_" classes
 *
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Aurora_Type
{
	/**
	 * Get the classname of $object
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function classname($object) {
		if (is_object($object))
			return get_class($object);
		if (is_string($object) AND class_exists($object))
			return $object;
		return FALSE;
	}
	/**
	 * Get the common name from $object, a common name is the name of the concept
	 * that we are modeling.
	 * for ex: Person is the common name of Model_Person as well as
	 * Collection_Person and Aurora_Person
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function cname($object) {
		$classname = static::classname($object) ? : (string) $object;
		$cname = preg_replace(
		  array(
			'/^Model_/',
			'/^Collection_/',
			'/^Aurora_/',
			'/^Controller_API_/',
		  ), '', $classname
		);
		return
		  (class_exists('Aurora_' . $cname)) ?
		  $cname :
		  FALSE;
	}
	/**
	 * Get the classname of the Model related to the $object.
	 * for ex: if $object is of type Collection_Person, the function returns
	 * Model_Person.
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function model($object) {
		return 'Model_' . static::cname($object);
	}
	/**
	 * Get the classname of the Collection related to the $object.
	 * for ex: if $object is of type Aurora_Person, the function returns
	 * Collection_Person.
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function collection($object) {
		return 'Collection_' . static::cname($object);
	}
	/**
	 * Get the classname of the Aurora related to the $object.
	 * for ex: if $object is of type Model_Person, the function returns
	 * Aurora_Person.
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function aurora($object) {
		return 'Aurora_' . static::cname($object);
	}
	/**
	 * Get the classname of the Controller_API related to the $object.
	 * for ex: if $object is of type Model_Person, the function returns
	 * Controller_API_Person.
	 *
	 * @param mixed $object
	 * @return string
	 */
	public static function controller_api($object) {
		return 'Controller_API_' . static::cname($object);
	}
	/**
	 * Test if $object is a Model
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_aurora($object, $classname_only = false) {
		$pattern = '/^Aurora_/';
		if ($classname_only)
			return (bool) preg_match($pattern, static::classname($object));
		else
			return
			  preg_match($pattern, static::classname($object)) AND
			  $object instanceof Interface_Aurora_Database;
	}
	/**
	 * Test if $object is a Model
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_model($object, $classname_only = false) {
		$pattern = '/^Model_/';
		if ($classname_only)
			return (bool) preg_match($pattern, static::classname($object));
		else
			return
			  preg_match($pattern, static::classname($object)) AND
			  is_object($object);
	}
	/**
	 * Test if $object is a Collection
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_collection($object, $classname_only = false) {
		$pattern = '/^Collection_/';
		if ($classname_only)
			return (bool) preg_match($pattern, static::classname($object));
		else
			return
			  preg_match($pattern, static::classname($object)) AND
			  $object instanceof Aurora_Collection;
	}
	/**
	 * Test if $object is a Controller_API
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_controller_api($object, $classname_only = false) {
		$pattern = '/^Controller_API_/';
		if ($classname_only)
			return (bool) preg_match($pattern, static::classname($object));
		else
			return
			  preg_match($pattern, static::classname($object)) AND
			  $object instanceof Controller_API;
	}
}