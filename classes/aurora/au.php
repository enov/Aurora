<?php

defined('SYSPATH') or die('No direct script access.');

class Aurora_Au extends Aurora_Core
{
	protected static $type, $prop, $rflx, $db, $bbjs, $std;
	/**
	 * Shortcut to Aurora_Type for method chaining.
	 *
	 * usage:
	 * 
	 *		Au::type()->is_collection($col) ? 'yes' : 'no';
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
	 *		$getters = Au::prop()->getters($model);
	 *
	 * @return Aurora_Property
	 */
	public static function prop() {
		return
		  static::$prop ?
		  static::$prop :
		  static::$prop = new Aurora_Property();
	}
}

