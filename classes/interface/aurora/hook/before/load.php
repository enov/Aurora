<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * before_load hook. If your Aurora implements this interface
 * the hook will be called before loading Models/Collections
 *
 * [!!] For users of PHP version < 5.3.10, it seems that there is a bug
 * in *call_user_func_array*. The function is used in Aurora_Hook.
 * It does not respect **by reference** calls.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Hook_Before_Load
{

	public function before_load(&$params);
}