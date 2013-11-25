<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Manual data mapping interface. Those methods are called
 * on database read and write. "Aurora_" classes should
 * implement this.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Database
{

	public function db_persist($model);

	public function db_retrieve($model, array $row);
}

