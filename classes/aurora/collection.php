<?php

/**
 * Generic Collection class
 *
 * @package Aurora
 * @author Samuel Demirdjian
 * @copyright (c) 2013, Samuel Demirdjian
 * @license http://license.enov.ws/mit MIT
 *
 * @see http://aheimlich.dreamhosters.com/generic-collections/Collection.phps
 * @see http://codeutopia.net/code/library/CU/Collection.php
 *
 */
abstract class Aurora_Collection implements Countable, IteratorAggregate, ArrayAccess
{

	/**
	 *
	 * @var string hold the type of model
	 */
	protected $_modelclass;
	/**
	 * @var array store internal data
	 */
	protected $_collection = array();
	/**
	 * @var array description of the id property of the models
	 */
	protected $_pkey_property;
	/**
	 * Create a new collection instance.
	 *
	 *     $col = Collection::factory($name);
	 *
	 * @param   string   collection name
	 * @return  Collection
	 */
	public static function factory($name) {
		// Add the collection prefix
		$class = 'Collection_' . $name;

		return new $class;
	}
	/**
	 * Get Model from Collection given ID
	 *
	 * @param type $id
	 * @return Model/NULL
	 */
	public function get($id) {
		if (empty($this->_pkey_property))
			$this->_pkey_property = Aurora_Property::pkey_property($this->modelclass());
		// to speed up "getting" the model, test if the model at offset $id
		// is the one we are looking for
		if (array_key_exists($id, $this->_collection)) {
			$model = $this->_collection[$id];
			if (Aurora_Property::get_pkey($model) === $id)
				return $model;
		}
		$pkey_prop = $this->_pkey_property['name'];
		$pkey_method = 'get_' . $this->_pkey_property['name'];
		foreach ($this->_collection as $offset => $model) {
			if ($this->_pkey_property['type'] == 'property') {
				if ($model->$pkey_prop === $id)
					return $model;
			} else {
				if ($model->$pkey_method() === $id)
					return $model;
			}
		}
		return NULL;
	}
	/**
	 * Add a value into the collection
	 * @param Model $model
	 * @throws InvalidArgumentException when wrong type
	 */
	public function add($model) {
		// add to collection
		return $this->offsetSet(NULL, $model);
	}
	/**
	 * Remove a model from the collection
	 *
	 * @param integer $id id of model to remove
	 */
	public function remove($id) {
		$model = $this->get($id);
		if (empty($model))
			return FALSE;
		$offset = array_search($model, $this->_collection, TRUE);
		return $this->offsetUnset($offset);
	}
	/**
	 * Determine if index exists
	 * @param integer $index
	 * @return boolean
	 */
	public function exists($id) {
		$model = $this->get($id);
		return isset($model);
	}
	/**
	 * Return count of items in collection
	 * Implements countable
	 * @return integer
	 */
	public function count() {
		return count($this->_collection);
	}
	/**
	 * Get the class name of the Model related to this Collection
	 */
	public function modelclass() {
		// lazy load instead of initializing in the constructor (performance hit?)
		if (empty($this->_modelclass))
			$this->_modelclass = Aurora_Type::model($this);
		return $this->_modelclass;
	}
	/**
	 * Determine if this value can be added to this collection
	 * @param string $value
	 * @return boolean
	 */
	public function valid_type($value) {
		// lazy load instead of initializing in the constructor (performance hit?)
		if (empty($this->_modelclass))
			$this->_modelclass = $this->modelclass();
		// instanceof works on interfaces as well as classes.
		// It also checks the entire inheritance chain
		return $value instanceof $this->_modelclass;
	}
	/**
	 * Return an iterator
	 * Implements IteratorAggregate
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator($this->_collection);
	}
	/**
	 * Set offset to value
	 * Implements ArrayAccess
	 * @see set
	 * @see http://codeutopia.net/blog/2008/09/17/generic-collections-in-php/ By Craig on Nov 15, 2011
	 * @param integer $offset
	 * @param mixed $value
	 * @return mixed the value set
	 */
	public function offsetSet($offset, $value) {
		if (!$this->valid_type($value))
			throw new InvalidArgumentException('Trying to add a value of wrong type');
		if (!isset($offset)) {
			$this->_collection[] = $value;
		} else {
			$this->_collection[$offset] = $value;
		}
		return $value;
	}
	/**
	 * Unset offset
	 * Implements ArrayAccess
	 * @see remove
	 * @param integer $offset
	 */
	public function offsetUnset($offset) {
		unset($this->_collection[$offset]);
		return TRUE;
	}
	/**
	 * get an offset's value
	 * Implements ArrayAccess
	 * @see get
	 * @param integer $offset
	 * @return Model
	 */
	public function offsetGet($offset) {
		return $this->_collection[$offset];
	}
	/**
	 * Determine if offset exists
	 * Implements ArrayAccess
	 * @see exists
	 * @param integer $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return isset($this->_collection[$offset]);
	}
	/**
	 * Clear out the collection
	 */
	public function clear() {
		// reset the internal array
		$this->_collection = array();
		return $this;
	}
	/**
	 * Get the underlying array
	 * @return array
	 */
	public function &to_array() {
		return $this->_collection;
	}
}