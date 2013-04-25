<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 */
class Aurora_Aurora_Filter {
	public $filter;
	public function filter($query) {
		$closure = $this->filter;
		return $closure($query);
	}
}