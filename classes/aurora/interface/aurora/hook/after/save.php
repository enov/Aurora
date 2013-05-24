<?php defined('SYSPATH') or die('No direct script access.');

/**
 * after_save hook. If your Aurora implements this interface
 * the hook will be called after saving Models/Collections
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_Hook_After_Save
{
	public function after_save($model_or_collection);
}