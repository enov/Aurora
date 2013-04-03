<?php

/**
 *
 */
class Aurora_Type
{
	public static function classname($object) {
		return is_string($object) ? $object : get_class($object);
	}
	public static function common($object) {
		return preg_replace(array('/^Model_/', '/^Collection_/', '/^Aurora_/'), '', static::classname($object));
	}
	public static function model($object) {
		return 'Model_' . static::common($object);
	}
	public static function collection($object) {
		return 'Collection_' . static::common($object);
	}
	public static function aurora($object) {
		return 'Aurora_' . static::common($object);
	}
	public static function is_model($object) {
		$pattern_model = '/^Model_/';
		return preg_match($pattern_model, Aurora_Type::classname($object));
	}
	public static function is_collection($object) {
		$pattern_collection = '/^Collection_/';
		return
		  preg_match($pattern_collection, Aurora_Type::classname($object)) AND
		  $object instanceof Collection;
	}
}