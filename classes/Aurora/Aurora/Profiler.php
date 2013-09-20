<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helper class to work with Kohana Profiler
 *
 * @package Aurora
 * @category Profiler
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
class Aurora_Aurora_Profiler
{

	/**
	 * @var string The profiling category under which benchmarks will appear
	 */
	protected static $category = 'Aurora';

	/**
	 * Add a profiling mark and start counter
	 *
	 * @param Aurora $aurora
	 * @param string $function
	 * @return string benchmark id in Profiler
	 */
	public static function start($aurora, $function) {
		if (empty($aurora) OR empty($function))
			return FALSE;
		$name = Aurora_Type::cname($aurora) . '::' . $function;
		$benchmark =
		  (Kohana::$profiling === TRUE) ?
		  Profiler::start(static::$category, $name) :
		  FALSE;
		return $benchmark;
	}

	/**
	 * Stop a profiling mark
	 *
	 * @param string $benchmark
	 */
	public static function stop($benchmark) {
		if (!empty($benchmark)) {
			// Stop the benchmark
			Profiler::stop($benchmark);
		}
	}

	/**
	 * Delete a profiling mark
	 *
	 * @param string $benchmark
	 */
	public static function delete($benchmark) {
		if (!empty($benchmark)) {
			// Delete the benchmark
			Profiler::delete($benchmark);
		}
	}

}