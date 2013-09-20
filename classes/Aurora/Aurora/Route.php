<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * The Aurora Routing class. Defines REST API URL and Reverse Routing
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Route
{

	/**
	 * Define routing scheme.
	 * To be used in init.php of this module
	 *
	 * @param string $uri
	 * @return array/boolean
	 */
	public static function route($uri) {
		$pieces = explode('/', $uri);
		// if $uri does not start with 'api/' return false
		$api = array_shift($pieces);
		if ($api != 'api')
			return false;
		// Get the last piece of the uri and test if it contains a numeric ID
		$last = array_pop($pieces);
		if (Valid::digit($last)) {
			$id = $last;
			$controller = array_pop($pieces);
		} else {
			$id = NULL;
			$controller = $last;
		}
		// construct $directory and common_name
		$directory = $api . ($pieces ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces) : '' );
		// See if exists a Controller of the form
		// Controller_API_Common_Name
		if (Kohana::find_file('classes/controller', $directory . DIRECTORY_SEPARATOR . $controller)) {
			return array(
				'directory' => $directory,
				'controller' => $controller,
				'id' => $id,
			);
		} else {
			// no luck, return false
			return false;
		}
	}

	/**
	 * Reverse Routing
	 * Get the uri from cname
	 *
	 * @return string URI of Model/Collection/Aurora/cname
	 */
	public static function reverse($object) {
		$cname = Aurora_Type::cname($object);
		return 'api/' . str_replace(
			'_', '/', strtolower($cname)
		);
	}

}