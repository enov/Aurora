<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON decoding.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_JSON_Deserialize
{

	/**
	 *
	 * @param stdClass/array $json a stdClass or an array of stdClass
	 * @return Model/Collection Deserialized Model or Collection
	 */
	public function json_deserialize($json);
}