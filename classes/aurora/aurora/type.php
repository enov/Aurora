<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Utility class for finding related Models, Collections, "Aurora_" classes
 * @TODO as well as Controllers.
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
		return is_string($object) ? $object : get_class($object);
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
		return preg_replace(
		  array('/^Model_/', '/^Collection_/', '/^Aurora_/'), '', static::classname($object)
		);
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
	 * Test if $object is a Model
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_aurora($object) {
		$pattern = '/^Aurora_/';
		return
		  preg_match($pattern, Aurora_Type::classname($object)) and
		  $object instanceof Aurora_Interface;
	}
	/**
	 * Test if $object is a Model
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_model($object) {
		$pattern = '/^Model_/';
		return preg_match($pattern, Aurora_Type::classname($object));
	}
	/**
	 * Test if $object is a Collection
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public static function is_collection($object) {
		$pattern = '/^Collection_/';
		return
		  preg_match($pattern, Aurora_Type::classname($object)) AND
		  $object instanceof Aurora_Collection;
	}
}