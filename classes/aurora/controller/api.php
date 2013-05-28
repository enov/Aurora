<?php

/**
 * Abstract Controller class for RESTful controller mapping. Supports GET, PUT,
 * POST, and DELETE. By default, these methods will be mapped to these actions:
 *
 * GET
 * : Mapped to the "index" action, lists all objects
 *
 * POST
 * : Mapped to the "create" action, creates a new object
 *
 * PUT
 * : Mapped to the "update" action, update an existing object
 *
 * DELETE
 * : Mapped to the "delete" action, delete an existing object
 *
 * Additional methods can be supported by adding the method and action to
 * the `$_action_map` property.
 *
 * [!!] Using this class within a website will require heavy modification,
 * due to most web browsers only supporting the GET and POST methods.
 * Generally, this class should only be used for web services and APIs.
 *
 * @package Aurora
 * @category API
 * @author Samuel Demirdjian
 * @copyright (c) 2013 Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 */
class Aurora_Controller_API extends Controller_REST
{
	public function action_index() {
		$cname = Au::type()->cname($this);
		$id = $this->request->param('id', NULL);
		$m_or_c = Au::load($cname, $id);
		$view = Au::json_encode($m_or_c);
		$this->response->body($view->render());
	}
	public function action_create() {
		$cname = Au::type()->cname($this);
		$m = Au::json_decode($cname, $this->request->body());
		Au::save($m);
		$view = Au::json_encode($m);
		$this->response->body($view);
	}
	public function action_update() {
		$cname = Au::type()->cname($this);
		$m = Au::json_decode($cname, $this->request->body());
		Au::save($m);
		$view = Au::json_encode($m);
		$this->response->body($view);
	}
	public function action_delete() {
		$cname = Au::type()->cname($this);
		$id = $this->request->param('id', NULL);
		if (is_null($id))
			throw new HTTP_Exception_404('No Model ID provided for deletion');
		$m = Au::load($cname, $id);
		if ($m == FALSE)
			throw new HTTP_Exception_404('Model does not exists');
		Au::delete($m);
		$view = Au::json_encode($m);
		$this->response->body($view);
	}
	public function after() {
		$this->response->headers('Content-type', 'application/json');
		parent::after();
	}

}
