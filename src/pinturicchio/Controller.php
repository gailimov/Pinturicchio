<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio;

use pinturicchio\http\Request;

/**
 * Base controller
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
abstract class Controller
{
    /**
     * \pinturicchio\http\Request
     * 
     * @var \pinturicchio\http\Request
     */
    private $_request;
    
    /**
     * \pinturicchio\Router
     * 
     * @var \pinturicchio\Router
     */
    private $_router;
    
    /**
     * View renderer object
     * 
     * @var object
     */
    private $_view;
    
    /**
     * Constructor
     * 
     * @param \pinturicchio\http\Request $request Request object
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
        $this->_router = new Router();
    }
    
    /**
     * Before filter
     * 
     * This method invoked before all actions. You may using it instead of constructor.
     * 
     * @return void
     */
    public function before()
    {
    }
    
    /**
     * After filter
     * 
     * This method invked after all actions. You may using this method instead of destructor
     * 
     * @return void
     */
    public function after()
    {
    }
    
    /**
     * Returns Request object
     * 
     * @return \pinturicchio\http\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Sets view renderer object
     * 
     * @param  object $view View renderer object
     * @return \pinturicchio\Controller
     */
    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }
    
    /**
     * Returns view object
     * 
     * @return \pinturicchio\view\PhpRenderer
     */
    public function getView()
    {
        return $this->_view;
    }
    
    /**
     * Creates relative URL
     * 
     * @see \pinturicchio\Router::createUrl()
     * 
     * @param  string $name   URL scheme name
     * @param  array  $params Params
     * @return string
     */
    public function createUrl($name, array $params = null)
    {
        return $this->_router->createUrl($name, $params);
    }
    
    /**
     * Creates absolute URL
     * 
     * @see \pinturicchio\Router::createAbsoluteUrl()
     * 
     * @param  string $name   URL scheme name
     * @param  array  $params Params
     * @param  bool   $https  Use HTTPS?
     * @return string
     */
    public function createAbsoluteUrl($name, array $params = null, $https = false)
    {
        return $this->_router->createAbsoluteUrl($name, $params, $https);
    }
    
    /**
     * Render partial template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public function renderPartial($template, $params = array())
    {
        return $this->_view->renderPartial($template, $params);
    }
    
    /**
     * Render template
     * 
     * @param  array  $params Params
     * @return string
     */
    public function render($params = array())
    {
        return $this->_view->render($this->_request->getControllerId() . '/' . $this->_request->getActionId(), $params);
    }
}
