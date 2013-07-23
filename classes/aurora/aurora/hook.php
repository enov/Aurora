<?php

class Aurora_Aurora_Hook
{
	public static function call($aurora, $hook, &$args) {
		$interface = "Interface_Aurora_Hook_" . $hook;
		if ($aurora instanceof $interface) {
			call_user_func_array(array($aurora, $hook), array(&$args));
		}
	}
}