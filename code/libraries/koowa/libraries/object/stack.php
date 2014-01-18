<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

 /**
  * Object Stack
  *
  * A stack is a data type or collection in which the principal (or only) operations on the collection are the addition
  * of an object to the collection, known as push and removal of an entity, known as pop. The relation between the push
  * and pop operations is such that the stack is a Last-In-First-Out (LIFO) data structure.
  *
  * @link http://en.wikipedia.org/wiki/Stack_(abstract_data_type)
  *
  * @author  Johan Janssens <https://github.com/johanjanssens>
  * @package Koowa\Library\Object
  */
class KObjectStack extends KObject implements Iterator, Countable, Serializable
{ 
    /**
     * The object container
     *
     * @var array
     */
    protected $_object_stack = null;
    
    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional KObjectConfig object with configuration options
     * @return KObjectStack
     */
    public function __construct(KObjectConfig $config)
    { 
        parent::__construct($config);
        
        $this->_object_stack = array();
    }
    
    /**
     * Peeks at the element from the end of the stack
     *
     * @return mixed The value of the top element
     */
    public function peek()
    {
        return end($this->_object_stack);
    }
      
    /**
     * Pushes an element at the end of the stack
     *
     * @param  mixed $object
     * @return KObjectStack
     */
    public function push($object)
    {
        $this->_object_stack[] = $object;
        return $this;
    }
    
    /**
     * Pops an element from the end of the stack
     *
     * @return  mixed The value of the popped element
     */
    public function pop()
    {
        return array_pop($this->_object_stack);
    }

	/**
     * Counts the number of elements
     *
     * Required by the Countable interface
     * 
     * @return integer	The number of elements
     */
    public function count()
    {
        return count($this->_object_stack);
    }

    /**
     * Rewind the Iterator to the top
     *
     * Required by the Iterator interface
     *
     * @return	object KObjectQueue
     */
    public function rewind()
    {
        reset($this->_object_stack);
        return $this;
    }

    /**
     * Check whether the stack contains more objects
     *
     * Required by the Iterator interface
     *
     * @return	boolean
     */
    public function valid()
    {
        return !is_null(key($this->_object_stack));
    }

    /**
     * Return current object index
     *
     * Required by the Iterator interface
     *
     * @return	mixed
     */
    public function key()
    {
        return key($this->_object_stack);
    }

    /**
     * Return current object pointed by the iterator
     *
     * Required by the Iterator interface
     *
     * @return	mixed
     */
    public function current()
    {
        return $this->_object_stack[$this->key()];
    }

    /**
     * Move to the next object
     *
     * Required by the Iterator interface
     *
     * @return	mixed
     */
    public function next()
    {
        return next($this->_object_stack);
    }

    /**
     * Serialize
     *
     * Required by the Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserialize
     *
     * Required by the Serializable interface
     *
     * @param  string $data
     * @return void
     */
    public function unserialize($data)
    {
        $data = array_reverse(unserialize($data));

        foreach ($data as $item) {
            $this->push($item);
        }
    }

    /**
     * Serialize to an array representing the stack
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_object_stack;
    }

    /**
     * Check to see if the registry is empty
     * 
     * @return boolean	Return TRUE if the registry is empty, otherwise FALSE
     */
    public function isEmpty()
    {
        return empty($this->_object_stack);
    }  
}
