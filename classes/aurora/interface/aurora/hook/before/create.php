<?php defined('SYSPATH') or die('No direct script access.');

/**
 * before_create hook. If your Aurora implements this interface
 * the hook will be called before creating Models
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_Hook_Before_Create
{
	public function before_create($model);
}