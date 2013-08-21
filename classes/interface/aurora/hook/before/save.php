<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * before_save hook. If your Aurora implements this interface
 * the hook will be called before saving Models/Collections
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_Hook_Before_Save
{

	public function before_save($model_or_collection);
}