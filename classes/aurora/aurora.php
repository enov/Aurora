<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * The Aurora Decorator. Decorates your Auroras with the main API.
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 */
class Aurora_Aurora
{

	protected $au;
	public function __construct($au) {
		if (!Aurora_Type::is_aurora($au))
			throw new Kohana_Exception('Parameter must be of type Aurora');
		$this->au = $au;
	}
	public static function factory($cname) {
		$decorator_class = get_called_class();
		$aurora_class = Au::type()->aurora($cname);

		return new $decorator_class(new $aurora_class);
	}
	/**
	 * Get a Kohana View with the JSON representation
	 * of your model or Collection
	 *
	 *     // Get the JSON view
	 *     $view = $au->json_encode($model);
	 *
	 *     // render the view
	 *     $this->response->body($view->render());
	 *
	 * @param Model/Collection $object
	 * @return View a Kohana View with JSON rendered object
	 * @throws View_Exception
	 */
	public function json_encode($object) {
		return Au::json_encode($object);
	}
	/**
	 * Convert from JSON to Model or Collection
	 *
	 *     // for example, if $json_string = '{ id: 3, ... }';
	 *     // Get the model from JSON string
	 *     $model = $au->json_decode($json_string);
	 *
	 *     // else if $json_string = '[{ id: 3, ... }, ...]';
	 *     // get the collection from JSON string
	 *     $collection = $au->json_decode($json_string);
	 *
	 *     // OR just convert to $object
	 *     $object = $au->json_decode($json_string);
	 *     // then test
	 *     Aurora_Type::is_model($object); // is_collection($object)
	 *
	 * @param string $common_name
	 * @param string $json The JSON string to convert from
	 * @return Model/Collection
	 */
	public function json_decode($json) {
		Au::json_decode($this->au, $json);
	}
	/**
	 * Check if your Model has an ID.
	 *
	 *     // usage
	 *     $is_new = $au->is_new($model);
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public function is_new($model) {
		return Au::is_new($model);
	}
	/**
	 * Load a model or collection from database
	 * using Aurora
	 *
	 * @param string/aurora $object
	 * @param scalar/array/callable $params
	 * @return Model/Collection
	 */
	public function load($params = NULL) {
		Au::load($this->au, $params);
	}
	public function save($object) {
		if (
		  !Au::type()->is_collection($object) and
		  !Au::type()->is_model($object)
		)
			$object = $this->load($object);
		Au::save($object);
	}
	public static function delete($object) {
		if (
		  !Au::type()->is_collection($object) and
		  !Au::type()->is_model($object)
		)
			$object = $this->load($object);
		Au::delete($object);
	}
}