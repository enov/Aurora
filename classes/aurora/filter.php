<?php

class Aurora_Filter {
	public $filter;
	public function filter($query) {
		$closure = $this->filter;
		return $closure($query);
	}
}