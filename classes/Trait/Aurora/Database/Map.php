<?php

defined('SYSPATH') or die('No direct script access.');

/**
 *
 */
trait Trait_Aurora_Database_Map
{
	protected function db_retrieve_map($model, $row, array $props) {
		if (!isset($this->_prefix_table_dot)) {
			$config = Kohana::$config->load("database")->get(Au::db()->config($this));
			// Getting the var
			$fetch_table_names = Arr::path($config, 'connection.fetch_table_names', FALSE);

			$this->_prefix_table_dot = $fetch_table_names ? Au::db()->table($this) . '.' : '';
		}
		foreach ($props as $prop) {
			if (in_array($prop, get_object_vars($model))) {
				$model->$prop = $row[$this->_prefix_table_dot . $prop];
			} else {
				$setter = 'set_' . $prop;
				$model->$setter($row[$this->_prefix_table_dot . $prop]);
			}
		}
		return;
	}
	protected function db_persist_map($model, array $props) {
		$arr_map = array();
		foreach ($props as $prop) {
			if (isset($model->$prop)) {
				$arr_map[$prop] = $model->$prop;
			} else {
				$getter = 'get_' . $prop;
				$arr_map[$prop] = $model->$getter();
			}
		}
		return $arr_map;
	}
	/**
	 * Get the row we need for this model
	 * out of all the rows
	 *
	 * @return array
	 */
	public function db_extract_row(array $row, $db_table_search, $db_table_replace = '', $unset = FALSE) {
		$db_table_replace = $db_table_replace ? : static::db_table();
		$pattern = "/^" . $db_table_search . "\./";
		$db_row = array();
		foreach ($row as $key => $value) {
			if (preg_match($pattern, $key)) {
				$db_row[preg_replace($pattern, $db_table_replace . '.', $key)] = $value;
				if ($unset) {
					unset($row[$key]);
				}
			}
		}
		return $db_row;
	}
}
