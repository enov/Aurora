<?php defined('SYSPATH') or die('No direct script access.');

/**
 * before_delete hook. If your Aurora implements this interface
 * the hook will be called before deleting Models/Collections
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_Hook_Before_Delete
{
	public function before_delete($model_or_collection);
}