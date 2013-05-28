<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Interface for custom JSON decoding.
 *
 * Note that you can also create a View in the views folder
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Interface_Aurora_JSON_Decode
{
	public function json_decode($json_str);
}