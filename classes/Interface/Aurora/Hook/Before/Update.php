<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * before_update hook. If your Aurora implements this interface
 * the hook will be called before updating Models
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Hook_Before_Update
{

	public function before_update($model);
}