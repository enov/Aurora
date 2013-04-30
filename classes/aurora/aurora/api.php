<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 */
class Aurora_Aurora_API
{
	protected static $url = '/api/';
	protected static $path = 'classes/controller/api';
	public static function url($object) {
		$common_name = Aurora_Type::cname($object);
		$url = str_replace('_', '/', strtolower($common_name));
		return static::$url . $url;
	}
	public static function backbone() {
		// list Controller_Api files using Kohana::list_files
		$list_files = Kohana::list_files(static::$path);
		// flatten the array
		$files = Arr::flatten($list_files);
		// Remove absolute part + $path
		$cnames = array_map("static::file_to_cname", $files);
		// loop through all classes' common names
		$arrViews = array();
		foreach ($cnames as $path => $common_name) {
			$arrViews[] = View::factory('backbone/model')->set('model', Au::factory($common_name, 'model'));
			$arrViews[] = View::factory('backbone/collection')->set('collection', Au::factory($common_name, 'collection'));
		}
		return implode('', $arrViews);
	}
	protected static function flatten(array $array) {
		$return = array();
		array_walk_recursive($array, function($a) use (&$return) {
			  $return[] = $a;
		  });
		return $return;
	}
	protected static function file_to_cname($file) {
		list(, $cname) = explode(static::$path, $file);
		// Remove the extension
		$cname = substr($cname, 1, - strlen(EXT));
		// Convert slashes to underscores
		$cname = str_replace(DIRECTORY_SEPARATOR, '_', strtolower($cname));
		// return
		return $cname;
	}
}