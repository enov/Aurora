<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * after_update hook. If your Aurora implements this interface
 * the hook will be called after updating Models
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Hook_After_Update
{

	public function after_update($model);
}