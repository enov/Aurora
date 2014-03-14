<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Manual data retrieving interface. The method is called
 * on database read. "Aurora_" classes should implement this
 * alone, when only retrieving is needed.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Database_Retrieve extends Interface_Aurora
{

	public function db_retrieve($model, array $row);
}
