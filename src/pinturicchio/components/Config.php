<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\components;

/**
 * Config
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Config implements \ArrayAccess
{
    /**
     * Data
     * 
     * @var array
     */
    private $_data = array();
    
    /**
     * Constructor
     * 
     * @param array $array Array
     */
    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value))
                $this->_data[$key] = new self($value);
            else
                $this->_data[$key] = $value;
        }
    }
    
    /**
     * Magically sets value by key
     * 
     * @param  string $key Key
     * @param  mixed  $value Value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
    
    /**
     * Magically returns value by key
     * 
     * @param  string $key Key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * Magic isset()
     * 
     * @param  string $key Key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
    
    /**
     * Magic unset()
     * 
     * @param  string $key Key
     * @return void
     */
    public function __unset($key)
    {
        $this->delete($key);
    }
    
    /**
     * Sets value by key
     * 
     * @param  string $key Key
     * @param  mixed  $value Value
     * @return \pinturicchio\components\Config
     */
    public function set($key, $value)
    {
        $this->_data[(string) $key] = $value;
        return $this;
    }
    
    /**
     * Returns value by key
     * 
     * @param  string $key Key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_data))
            return $this->_data[(string) $key];
        return null;
    }
    
    /**
     * Checks for the key
     * 
     * @param  string $key Key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->_data[(string) $key]);
    }
    
    /**
     * Deletes value by key
     * 
     * @param  string $key Key
     * @return void
     */
    public function delete($key)
    {
        unset($this->_data[(string) $key]);
    }
    
    /**
     * Returns array of stored data
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array();
        
        foreach ($this->_data as $key => $value) {
            if ($value instanceof self)
                $array[$key] = $value->toArray();
            else
                $array[$key] = $value;
        }
        
        return $array;
    }
    
    /**
     * Defined by ArrayAccess interface
     * @link http://www.php.net/manual/en/class.arrayaccess.php
     * 
     * Assigns a value to the specified offset.
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     * 
     * @param  mixed $offset Offset
     * @param  mixed $value  Value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    /**
     * Defined by ArrayAccess interface
     * @link http://www.php.net/manual/en/class.arrayaccess.php
     * 
     * Returns the value at specified offset
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     * 
     * @param  mixed $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Defined by ArrayAccess interface
     * @link http://www.php.net/manual/en/class.arrayaccess.php
     * 
     * Whether or not an offset exists
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     * 
     * @param  mixed $offset Offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    
    /**
     * Defined by ArrayAccess interface
     * @link http://www.php.net/manual/en/class.arrayaccess.php
     * 
     * Unsets an offset
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     * 
     * @param  mixed $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
