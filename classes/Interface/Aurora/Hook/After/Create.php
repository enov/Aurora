<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * after_create hook. If your Aurora implements this interface
 * the hook will be called after creating Models
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Hook_After_Create
{

	public function after_create($model);
}