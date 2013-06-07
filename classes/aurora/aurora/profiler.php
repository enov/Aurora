<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class to work with Kohana Profiler
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Aurora_Profiler
{

	protected static $category = 'Aurora';
	public static function start($aurora, $function) {
		$name = Aurora_Type::cname($aurora) . '::' . $function;
		$benchmark =
		  (Kohana::$profiling === TRUE) ?
		  Profiler::start(static::$category, $name) :
		  FALSE;
		return $benchmark;
	}
	public static function stop($benchmark) {
		if (!empty($benchmark)) {
			// Stop the benchmark
			Profiler::stop($benchmark);
		}
	}
	public static function delete($benchmark) {
		if (!empty($benchmark)) {
			// Delete the benchmark
			Profiler::delete($benchmark);
		}
	}
}