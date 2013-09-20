<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * A simple Aurora hooking class.
 *
 * @package Aurora
 * @category Hook
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Hook
{

	public static function call($aurora, $hook, &$args) {
		$interface = "Interface_Aurora_Hook_" . $hook;
		if ($aurora instanceof $interface) {
			call_user_func_array(array($aurora, $hook), array(&$args));
		}
	}

}