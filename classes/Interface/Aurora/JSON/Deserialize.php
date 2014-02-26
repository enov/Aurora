<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON deserialization.
 *
 * @package Aurora
 * @category Interfaces
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
interface Interface_Aurora_JSON_Deserialize extends Interface_Aurora
{

	/**
	 * function for custom JSON deserialization
	 *
	 * @param stdClass $json
	 * @return Model Deserialized Model
	 */
	public function json_deserialize($json);
}