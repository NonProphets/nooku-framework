<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * State object
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelState extends KObject implements KFactoryIdentifiable
{

	/**
	 * Array to hold the state
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * The object identifier
	 *
	 * @var object
	 */
	protected $_identifier = null;

	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
		// Set the objects identifier
        $this->_identifier = $options['identifier'];

		// Initialize the options
		$options  = $this->_initialize($options);

		//
		$this->_data = $options['data'];

	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(array $options)
	{
		$defaults = array(
            'data'      => array(),
			'identifier' => null
       	);

        return array_merge($defaults, $options);
    }

    /**
	 * Get the identifier
	 *
	 * @return 	object A KFactoryIdentifier object
	 * @see 	KFactoryIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}


	/**
     * Get a state value
     *
     * @param  	string 	The user-specified state name.
     * @return 	string 	The corresponding state value or NULL if the state doesn't exist
     */
    public function __get($name)
    {
    	if(isset($this->_data[$name])) {
    		return $this->_data[$name]->value;
    	}

    	return null;
    }

    /**
     * Set state value
     *
     * @param  	string 	The user-specified state name.
     * @param  	mixed  	The user-specified state value.
     * @return 	void
     */
    public function __set($name, $value)
    {
    	if(isset($this->_data[$name])) {
    		$this->_data[$name]->value = $value;
    	} else {
    		throw new KModelException('Cannot set a state before it is inserted');
    	}
   }

	/**
     * Test existence of a state
     *
     * @param  string  The column key.
     * @return boolean
     */
    public function __isset($name)
    {
    	return array_key_exists($name, $this->_data);
    }

    /**
     * Unset a state value
     *
     * @param	string  The column key.
     * @return	void
     */
    public function __unset($name)
    {
    	if(isset($this->_data[$name])) {
    		$this->_data[$name]->value = null;
    	}
    }

	/**
     * Insert a new state
     *
     * @param   string		The name of the state
     * @param   mixed		Filter(s), can be a KFilterInterface object, a filter name or an array of filter names
     * @param   mixed  		The default value of the state
     * @return  KModelState
     */
    public function insert($name, $filter, $default = null)
    {
    	if(!isset($this->_data[$name]))
    	{
    		$state = new stdClass();
    		$state->name   = $name;
    		$state->filter = $filter;
    		$state->value  = $default;
    		$this->_data[$name] = $state;
    	}

        return $this;
    }

	/**
     * Remove an existing state
     *
     * @param   string		The name of the state
     * @return  KModelState
     */
    public function remove( $name )
    {
    	unset($this->_data[$name]);
        return $this;
    }

	/**
     * Reset all cached data
     *
     * @return KModelState
     */
    public function reset()
    {
    	$this->_data = array();
    	return $this;
    }

	/**
     * Set the state data
     *
     * @param   array|object	An associative array of state data by name
     * @return  KModelState
     */
    public function setData(array $data)
    {
		// Filter data
		foreach($data as $key => $value)
		{
			if(isset($this->_data[$key]))
    		{
    			$filter = $this->_data[$key]->filter;

    			if(!($filter instanceof KFilterInterface))
				{
					$names = (array) $filter;

					$name   = array_shift($names);
					$filter = self::_createFilter($name);

					foreach($names as $name) {
						$filter->addFilter($this->_createFilter($name));
					}
				}

    			$this->_data[$key]->value = $filter->sanitize($value);
    		}
		}

        return $this;
    }

    /**
     * Get the state data
     *
     * @return  array 	An associative array of state data by name
     */
    public function getData()
    {
        $result = array();

   		foreach ($this->_data as $k => $v) {
            $result[$k] = $v->value;
        }

        return $result;
    }

	/**
	 * Create a filter based on it's name
	 *
	 * @param 	string	Filter name
	 * @throws	KModelException	When the filter could not be found
	 * @return  KFilterInterface
	 */
	protected static function _createFilter($name)
	{
		try {
			$filter = KFactory::get('lib.koowa.filter.'.$name);
		} catch(KFactoryAdapterException $e) {
			throw new KModelException('Invalid filter: '.$name);
		}

		return $filter;
	}

	/**
	 * Get a property
	 *
	 * @param   string	The name of the property
     * @param   mixed  	The default value
     * @return  mixed 	The value of the property
	 */
	public function get($property, $default = null)
	{
		return isset($this->$property) ? $this->$property : $default;
	}

 	/**
     * Set the object properties
     *
     * @param   string				The name of the property
     * @param   mixed  				The value of the property
     * @return  KModelState
     */
	public function set($property, $value)
	{
		$this->$property = $value;
		return $this;
	}

}