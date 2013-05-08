<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Manual ORM interface. Those methods are called
 * on database read and write. "Aurora_" classes should
 * implement this.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
interface Aurora_Aurora_Interface
{
	public function db_from_model($model);
	public function db_to_model($model, array $row);
}