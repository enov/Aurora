<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Manual data persisting interface. The method is called
 * on database write. "Aurora_" classes should implement this
 * alone, if persisting alone is needed.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_Database_Persist extends Interface_Aurora
{

	/**
	 * @param Model $model Model to persist
	 * @return array resulting array to use in DB::insert or DB::update
	 */
	public function db_persist($model);

}

