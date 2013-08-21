<?php

defined('SYSPATH') or die('No direct script access.');

class Aurora_Au extends Aurora_Core
{

	protected static $type, $prop, $rflx, $db, $json, $hook;

	/**
	 * Shortcut to Aurora_Type for method chaining.
	 *
	 * usage:
	 *
	 * 		Au::type()->is_collection($col) ? 'yes' : 'no';
	 *
	 * @return Aurora_Type
	 */
	public static function type() {
		return
		  static::$type ?
		  static::$type :
		  static::$type = new Aurora_Type();
	}

	/**
	 * Shortcut to Aurora_Property for method chaining.
	 *
	 * usage:
	 *
	 * 		$getters = Au::prop()->getters($model);
	 *
	 * @return Aurora_Property
	 */
	public static function prop() {
		return
		  static::$prop ?
		  static::$prop :
		  static::$prop = new Aurora_Property();
	}

	/**
	 * Shortcut to Aurora_Reflection for method chaining.
	 *
	 * usage:
	 *
	 *
	 *
	 * @return Aurora_Reflection
	 */
	public static function rflx() {
		return
		  static::$rflx ?
		  static::$rflx :
		  static::$rflx = new Aurora_Reflection();
	}

	/**
	 * Shortcut to Aurora_Database for method chaining.
	 *
	 * usage:
	 *
	 *
	 *
	 * @return Aurora_Database
	 */
	public static function db() {
		return
		  static::$db ?
		  static::$db :
		  static::$db = new Aurora_Database();
	}

	/**
	 * Shortcut to Aurora_JSON for method chaining.
	 *
	 * usage:
	 *
	 *
	 *
	 * @return Aurora_Database
	 */
	public static function json() {
		return
		  static::$json ?
		  static::$json :
		  static::$json = new Aurora_JSON();
	}

	/**
	 * Shortcut to Aurora_Hook for method chaining.
	 *
	 * usage:
	 *
	 *
	 *
	 * @return Aurora_Hook
	 */
	public static function hook() {
		return
		  static::$hook ?
		  static::$hook :
		  static::$hook = new Aurora_Hook();
	}

}

