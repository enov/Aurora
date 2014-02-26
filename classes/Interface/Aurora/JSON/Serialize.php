<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON serialization
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_JSON_Serialize extends Interface_Aurora
{

	/**
	 * function for custom JSON serialization
	 *
	 * @param Model $model
	 * @return mixed You can return an stdClass or any type that PHP can
	 *			natively serialize, if you believe your model can be serialized,
	 *          just return the parameter.
	 *
	 */
	public function json_serialize($model);
}