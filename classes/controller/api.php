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
class Controller_API extends Controller_REST
{

	protected $expire			 = true;
	protected $authenticate	 = true;
	protected $autorize		 = true;
	/**
	 * Used to construct the respective Model_(_common_name) & Collection_(_common_name)
	 * by convention the controller should be called Controller_API_(_common_name)
	 *
	 * @var string
	 */
	protected $_common_name;
	/**
	 * Overrided krestful Restful_Controller
	 * @var array
	 */
	protected $_accept		 = array(
		'application/json'
	);
	protected $_accept_lang	 = array(
		'en_US',
		'en_GB'
	);
	protected $_accept_charset = array(
		'utf-8',
		'ISO-8859-1'
	);
	protected $_accept_strict	 = TRUE;
	public function __construct(Request $request, Response $response, array $accept = NULL, array $accept_charset = NULL, array $accept_language = NULL, array $accept_strict = NULL) {
		parent::__construct($request, $response, $accept, $accept_charset, $accept_language, $accept_strict);
		$this->_common_name = $request->param('common_name', str_ireplace('Controller_API_', '', get_called_class()));
	}
	public function action_index() {
		$id		 = $this->request->param('id', 0);
		$m_or_c	 = ($id) ?
		  Model::factory($this->_common_name)->load($id) :
		  Collection::factory($this->_common_name)->load();

		$view = Represent::object($m_or_c, $this->resolved_format());
		$this->response->body($view->render());
	}
	public function action_create() {
		$m	 = Model::factory($this->_common_name);
		// load request json data
		$o	 = json_decode($this->request->body());
		$m->from_stdClass($o);
		// save and return
		$m->save();
		$this->response->body(Represent::model($m)->render());
	}
	public function action_update() {
		$m	 = Model::factory($this->_common_name);
//		$m->load($this->request->param('id'));
//		if (!$m->loaded())
//			throw new HTTP_Exception_500('Model not loaded. Can not update');
		// load request json data
		$o	 = json_decode($this->request->body());
		$m->from_stdClass($o);
		// save and return
		$m->save();
		$this->response->body(Represent::model($m)->render());
	}
	public function action_delete() {
		$m = Model::factory($this->_common_name);
		$m->load($this->request->param('id'));
		if (!$m->loaded())
			throw new HTTP_Exception_500('Model not loaded. Can not delete');
		$m->delete();
		$this->response->body(Represent::model($m)->render());
	}
	protected function authorize() {
		if (!$this->user->can($this->request->uri(), $this->request->action()))
			throw new HTTP_Exception_403;
	}
}
