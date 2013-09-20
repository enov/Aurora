<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * after_load hook. If your Aurora implements this interface
 * the hook will be called after loading Models/Collections
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Hook_After_Load
{

	public function after_load($model_or_collection);
}