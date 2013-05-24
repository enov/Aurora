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
	 * Construct a new typed collection
	 * @param string valueType collection value type
	 */
	public function __construct() {
		// process according to the class name
		$this->_valueType = Aurora_Type::model($this);
	}
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
	 * Get Model from Collection given ID
	 *
	 * @param type $id
	 */
	public function get($id) {
		foreach ($this->_collection as $model) {
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
		if ($this->exists(Aurora_Property::get_pkey($model)))
			return FALSE;
		return $this->offsetSet(NULL, $model);
	}
	/**
	 * Remove a model from the collection
	 * @param integer $id id of model to remove
	 * @throws OutOfRangeException if index is out of range
	 */
	public function remove($id) {
		foreach ($this->_collection as $offSet => $model) {
			if (Aurora_Property::get_pkey($model) === $id)
				return $this->offsetUnset($offSet);
		}
		return FALSE;
	}
	/**
	 * Determine if index exists
	 * @param integer $index
	 * @return boolean
	 */
	public function exists($id) {
		return !is_null($this->get($id));
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
		// instanceof works on interfaces as well as classes.
		// It also checks the entire inheritance chain
		return $value instanceof $this->_valueType;
	}
	/**
	 * An alias of valid_type that exist for historical (B/C) reasons.
	 */
	public function isValidType($value) {
		return $this->valid_type($value);
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
		if (is_string($offset) AND $this->offsetExists($offset))
			throw new InvalidArgumentException('Trying to add a model that already exists');
		if (is_null($offset)) {
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
	public function to_array() {
		return $this->_collection;
	}
}