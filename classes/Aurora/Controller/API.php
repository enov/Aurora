<?php

/**
 * Controller class for RESTful controller mapping. Supports GET, PUT,
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
 * @package Aurora
 * @category RESTful API
 * @author Samuel Demirdjian
 * @copyright (c) 2013 Samuel Demirdjian
 * @license http://enov.mit-license.org MIT
 */
class Aurora_Controller_API extends Controller
{

	public function action_index() {
		$cname = $this->cname();
		$id = $this->request->param('id', NULL);
		$m_or_c = Au::load($cname, $id);
		$json_str = Au::json_encode($m_or_c);
		$this->response->body($json_str);
	}

	public function action_create() {
		$cname = $this->cname();
		$m = Au::json_decode($this->request->body(), $cname);
		if (Au::is_loaded($m))
			throw new HTTP_Exception_500("Model should be new.");
		Au::save($m);
		$json_str = Au::json_encode($m);
		$this->response->body($json_str);
	}

	public function action_update() {
		$cname = $this->cname();
		$m = Au::json_decode($this->request->body(), $cname);
		if (Au::is_new($m))
			throw new HTTP_Exception_500("Model should be loaded.");
		Au::save($m);
		$json_str = Au::json_encode($m);
		$this->response->body($json_str);
	}

	public function action_delete() {
		$cname = $this->cname();
		$id = $this->request->param('id', NULL);
		if (is_null($id))
			throw new HTTP_Exception_404('No Model ID provided for deletion');
		$m = Au::load($cname, $id);
		if ($m == FALSE)
			throw new HTTP_Exception_404('Model does not exists');
		Au::delete($m);
		$json_str = Au::json_encode($m);
		$this->response->body($json_str);
	}

	public function cname() {
		return $this->request->param('cname') ? : Au::type()->cname($this);
	}

	public function after() {
		$this->response->headers('Content-type', 'application/json');
		if (in_array(Arr::get($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', $this->request->method()), array(
			  HTTP_Request::PUT,
			  HTTP_Request::POST,
			  HTTP_Request::DELETE)))
			$this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
		parent::after();
	}

}
