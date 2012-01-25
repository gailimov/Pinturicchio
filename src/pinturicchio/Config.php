<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio;

/**
 * Config
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Config
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
        return isset($this->_data[$key]);
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
}
