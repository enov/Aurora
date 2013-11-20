<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * A set of helper functions for strait-forward properties-to-columns mappings.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 *
 */
trait Trait_Aurora_Data_Map
{

	/**
	 * Strait-forward columns-to-properties mapping.
	 *
	 * Usage (you must use this inside `db_retrieve`):
	 *
	 *     public function db_retrieve ($model, $row) {
	 *         // Do some strait-forward mappings here
	 *         $this->map_retrieve(
	 *             $model,
	 *             $row,
	 *             ['id', 'name', 'label', 'description']
	 *         );
	 *
	 *         // Do some non-strait-forward mapping below.
	 *
	 *         // Delegate retrieving the row to Aurora_Brand
	 *         $model->set_brand(
	 *             Au::factory('Brand')
	 *             ->db_retrieve(Model::factory('Brand'), $row)
	 *         );
	 *     }
	 *
	 *
	 * @return void
	 */
	protected function map_retrieve($model, $row, array $props) {
		$this->prefix_table_dot = isset($this->prefix_table_dot) ?
		  $this->prefix_table_dot :
		  Au::db()->prefix_table_dot($this);
		$tbldot = $this->prefix_table_dot;
		foreach ($props as $prop) {
			// *************************
			// *** Implementation #1 ***
			// *************************
			// this is a ~50% faster implemenation than #2 below
			// but does not take care of ID being usually protected
			// it's better used when Aurora extends the Model
			if (in_array($prop, get_object_vars($model), TRUE)) {
				$model->$prop = $row[$tbldot . $prop];
			} else {
				$setter = 'set_' . $prop;
				$model->$setter($row[$tbldot . $prop]);
			}
			continue; // stops here, Implementation #2 unreachable
			// *************************
			// *** Implementation #2 ***
			// *************************
			// set property values through Aurora_Property
			if ($prop === Au::db()->pkey($this)) {
				Au::prop()->set_pkey($model, $row[$tbldot . $prop]);
			} else {
				Au::prop()->set($model, $prop, $row[$tbldot . $prop]);
			}
		}
		return;
	}

	/**
	 * Strait forward properties-to-columns mappings
	 *
	 * Usage (you must use this inside `db_persist`):
	 *
	 *     public function db_persist($model) {
	 *         // Do some strait-forward mappings here
	 *         return $this->map_persist(
	 *             $model,
	 *             ['id', 'name', 'label', 'description']
	 *         ) +
	 *         // Continue with non strait-forward mappings
	 *         array(
	 *             'brand_id' => $model->brand->id,
	 *         );
	 *     }
	 *
	 *
	 * @return void
	 */
	protected function map_persist($model, array $props) {
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

}
