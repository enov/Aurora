<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON encoding
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_JSON_Serialize
{

	/**
	 *
	 * @param Model/Collection $model
	 * @return mixed a stdClass or an array of stdClass or any type that can be
	 *         serialized, if you think your model can be serialized,
	 *         just return it
	 *
	 */
	public function json_serialize($model);
}