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
	protected $_valueType;
	/**
	 * @var array store internal data
	 */
	protected $_collection = array();
	/**
	 * @var bool true if collection contains unloaded (new) models
	 */
	protected $_is_dirty = FALSE;
	/**
	 * Create a new collection instance.
	 *
	 *     $col = Collection::factory($name);
	 *
	 * @param   string   collection name
	 * @return  Collection
	 */
	public static function factory($name) {
		// Add the model prefix
		$class = 'Collection_' . $name;

		return new $class;
	}
	/**
	 * Calculates offset given $id
	 * @param mixed $id
	 * @return mixed
	 */
	public function get_offset($id) {
		if (empty($id))
			return NULL;
		return 's' . $id;
	}
	/**
	 * Get Model from Collection given ID
	 *
	 * @param type $id
	 */
	public function get($id) {
		$offset = $this->get_offset($id);
		if (isset($this->_collection[$offset]))
			return $this->_collection[$offset];
		if ($this->_is_dirty)
			foreach ($this->_collection as $offset => $model) {
				if (is_int($offset))
					if (Aurora_Property::get_pkey($model) === $id)
						return $model;
			}
		return NULL;
	}
	/**
	 * Add a value into the collection
	 * @param Model $model
	 * @throws InvalidArgumentException when wrong type
	 */
	public function add($model) {
		if (!$this->valid_type($model))
			throw new InvalidArgumentException('Trying to add a value of wrong type');
		// Get model id
		$id = Aurora_Property::get_pkey($model);
		// if empty $id, model is new, this collection is dirty
		if (empty($id))
			$this->_is_dirty = TRUE;
		// do not allow to add a model if collection contains one with same id
		else if ($this->exists($id))
			throw new Kohana_Exception('Model with same id already exists');
		// calculate offset
		$offset = empty($id) ? NULL : $this->get_offset($id);
		// add to collection
		return $this->offsetSet($offset, $model);
	}
	/**
	 * Remove a model from the collection
	 * @param integer $id id of model to remove
	 * @throws OutOfRangeException if index is out of range
	 */
	public function remove($id) {
		// try to unset with the $offset
		$offset = $this->get_offset($id);
		if (isset($this->_collection[$offset]))
			return $this->offsetUnset($offset);
		// only loop the hard loop if collection is dirty
		if ($this->_is_dirty)
			foreach ($this->_collection as $offset => $model) {
				if (Aurora_Property::get_pkey($model) === $id)
					return $this->offsetUnset($offset);
			}
		// return FALSE if nothing to remove
		return FALSE;
	}
	/**
	 * Determine if index exists
	 * @param integer $index
	 * @return boolean
	 */
	public function exists($id) {
		return isset($this->get($id));
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
	 * Determine if this value can be added to this collection
	 * @param string $value
	 * @return boolean
	 */
	public function valid_type($value) {
		// lazy load instead of initializing in the constructor (performance hit?)
		if (empty($this->_valueType))
			$this->_valueType = Aurora_Type::model($this);
		// instanceof works on interfaces as well as classes.
		// It also checks the entire inheritance chain
		return $value instanceof $this->_valueType;
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
	 * @return mixed
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
		// empty the internal array
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