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
	 * @var array REST types
	 */
	protected static $_action_map = array
		(
		HTTP_Request::GET => 'index',
		HTTP_Request::PUT => 'update',
		HTTP_Request::POST => 'create',
		HTTP_Request::DELETE => 'delete',
	);

	/**
	 * Define routing scheme.
	 * To be used in init.php of this module
	 *
	 * @param string $uri
	 * @return array/boolean
	 */
	public static function map($route, $params, $request) {
		// find the controller
		$params = static::map_path($route, $params, $request);
		// find the action
		if ($params !== FALSE)
			$params = static::map_method($route, $params, $request);
		// return
		return $params;
	}

	/**
	 * Maps `<path>` (or `$params['path']`) to the respective controller.
	 * Also, allows config routing
	 *
	 * Usage: In your bootstrap.php set the following route:
	 *
	 *     Route::set('rest-api', 'api/<path>', array('path' => '.*'))
	 *       ->filter(array('Aurora_Route', 'map_path'));
	 *
	 * @param Route $route
	 * @param array $params
	 * @param Request $request
	 * @return array
	 * @throws Kohana_Exception
	 */
	public static function map_path($route, $params, $request) {
		// test if `<path>` exists in params
		if (!isset($params['path']))
			return false;
		// explode path to pieces
		$pieces = explode('/', $params['path']);
		// Get the last piece of the uri and test if it contains a numeric ID
		$last = array_pop($pieces);
		if (Valid::digit($last)) {
			$id = $last;
			$controller = ucfirst(array_pop($pieces));
		} else {
			$id = NULL;
			$controller = ucfirst($last);
		}
		// construct $directory and cname
		$directory = 'API';
		$common_name = '';
		foreach ($pieces as $folder) {
			$directory .= DIRECTORY_SEPARATOR . ucfirst($folder);
			$common_name = ucfirst($folder) . '_';
		}
		$common_name .= $controller;
		// Test for the existance of a controller `Controller_API_Common_Name`
		$file = 'Controller'
		  . DIRECTORY_SEPARATOR . $directory
		  . DIRECTORY_SEPARATOR . $controller;
		if (Kohana::find_file('classes', $file)) {
			$params['directory'] = $directory;
			$params['controller'] = $controller;
			$params['id'] = $id;
			return $params;
		}
		// if no `Controller_API_Common_Name` test if routing via config exists
		if (in_array($common_name, (array) Kohana::$config->load('routes.api'))) {
			$params['directory'] = NULL;
			$params['controller'] = 'API';
			$params['cname'] = $common_name;
			$params['id'] = $id;
			return $params;
		}
		// throw new HTTP_Exception_404('Could not find a resource for ' . $common_name) ?
		// it's not a good idea to throw exceptions from parsing routes
		// because it would interfere with the work of other routes
		return FALSE;
	}

	/**
	 * Maps $request->method() to the controller action
	 * Supports GET, PUT, POST, and DELETE.
	 * By default, these methods will be mapped to these actions:
	 *
	 * GET
	 * : Mapped to the "index" action, lists all objects
	 *
	 * POST
	 * : Mapped to the "create" action, creates a new object
	 *
	 * PUT
	 * : Mapped to the "update" action, update an existing object
	 *
	 * DELETE
	 * : Mapped to the "delete" action, delete an existing object
	 *
	 * Additional methods can be supported by adding the method and action to
	 * the `$_action_map` property.
	 *
	 * @param Route $route
	 * @param array $params
	 * @param Request $request
	 * @return array
	 * @throws Kohana_Exception
	 * @see Aurora_Controller_API
	 */
	public static function map_method($route, $params, $request) {
		// get the method from request
		$method = $request->method();
		// throw 'not allowed' http exception if method not available
		if (!isset(static::$_action_map[$method]))
			throw HTTP_Exception::factory(405)->allowed(array_keys(static::$_action_map));
		// set the action
		$params['action'] = static::$_action_map[$method];
		//return $params
		return $params;
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
