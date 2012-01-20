<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\http;

/**
 * HTTP request class
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Request
{
    /**
     * Controller
     * 
     * @var string
     */
    private $_controller;
    
    /**
     * Action
     * 
     * @var string
     */
    private $_action;
    
    /**
     * Params
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * Constructor
     * 
     * @param array $options Request options
     */
    public function __construct(array $options)
    {
        $this->_controller = $options['controller'];
        $this->_action = $options['action'];
        $this->_params = $options['params'];
    }
    
    /**
     * Returns controller ID
     * 
     * @return string
     */
    public function getControllerId()
    {
        return mb_strtolower($this->_controller);
    }
    
    /**
     * Returns action ID
     * 
     * @return string
     */
    public function getActionId()
    {
        return $this->_action;
    }
    
    /**
     * Returns params
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
    
    /**
     * Returns param value by key
     * 
     * @param  string $key Key
     * @return mixed || null if key not exists
     */
    public function getParam($key)
    {
        if (isset($this->_params[(string) $key]))
            return $this->_params[$key];
        return null;
    }
}
