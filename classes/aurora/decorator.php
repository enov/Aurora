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
class Aurora_Decorator
{

	protected $au;
	public function __construct($au) {
		if (!Aurora_Type::is_aurora($au))
			throw new Kohana_Exception('Parameter must be an Aurora');
		$this->au = $au;
	}
	/**
	 * Factory method. Use this when you want to instantiate
	 * your Aurora, decorated with the Aurora_Core methods.
	 *
	 * @param string $cname
	 * @return Aurora
	 */
	public static function factory($cname) {
		$decorator = get_called_class();
		$vanilla = Au::type()->aurora($cname);

		return new $decorator(new $vanilla);
	}
	/**
	 * Get the internal, undecorated, vanilla Aurora
	 *
	 * @return Aurora
	 */
	public function vanilla(){
		return $this->au;
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
		return Au::json_decode($json, $this->au);
	}
	/**
	 * Check if your Model has an ID.
	 *
	 *     // usage
	 *     $new = $this->is_new($model);
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public function is_new($model) {
		return Au::is_new($model);
	}
	/**
	 * Check if your Model is loaded.
	 * A loaded Model has an ID
	 *
	 *     // usage
	 *     $loaded = $this->is_loaded($model);
	 *
	 * @param Model $model
	 * @return boolean
	 */
	public function is_loaded($model) {
		return Au::is_loaded($model);
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
		return Au::load($this->au, $params);
	}
	/**
	 *
	 * @param type $object
	 */
	public function save($object) {
		if (
		  !Au::type()->is_collection($object) and
		  !Au::type()->is_model($object)
		)
			$object = $this->load($object);
		Au::save($object);
	}
	/**
	 *
	 * @param type $object
	 */
	public function delete($object) {
		if (
		  !Au::type()->is_collection($object) and
		  !Au::type()->is_model($object)
		)
			$object = $this->load($object);
		Au::delete($object);
	}
	/**
	 * Magic function to call your Aurora methods.
	 * It gets triggered when you call a method other than
	 * the Aurora_Core API, that's basically when you call a
	 * method from your Aurora.
	 * 
	 * @return $this for chaining
	 */
	public function __call($name, $arguments) {
		call_user_func_array(array($this->au, $name), $arguments);
		return $this;
	}
}