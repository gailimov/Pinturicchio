<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio;

use pinturicchio\http\Request,
    pinturicchio\view\Renderer,
    pinturicchio\view\PhpRenderer;

/**
 * Front controller
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class FrontController
{
    /**
     * Controllers directory
     * 
     * @var string
     */
    private $_controllersDirectory = 'controllers';
    
    /**
     * @var \pinturicchio\view\Renderer
     */
    private $_viewRenderer;
    
    /**
     * Action postfix
     * 
     * @var string
     */
    private $_actionPostfix = 'Action';
    
    public function __construct()
    {
        if (isset(Config::factory()->params['viewRenderer'])) {
            $viewRenderer = Config::factory()->createClassNameFromAlias(Config::factory()->params['viewRenderer']);
            $this->setViewRenderer(new $viewRenderer());
        } else {
            $this->_viewRenderer = new PhpRenderer();
        }
    }
    
    /**
     * Sets controllers directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\FrontController
     */
    public function setControllersDirectory($directory)
    {
        $this->_controllersDirectory = (string) $directory;
        return $this;
    }
    
    /**
     * Returns controllers directory
     * 
     * @return string
     */
    public function getControllersDirectory()
    {
        return $this->_controllersDirectory;
    }
    
    /**
     * Set view renderer object
     * 
     * @param  \pinturicchio\view\Renderer $viewRenderer
     * @return \pinturicchio\FrontController
     */
    public function setViewRenderer(Renderer $viewRenderer)
    {
        $this->_viewRenderer = $viewRenderer;
        return $this;
    }
    
    /**
     * Dispatching
     * 
     * @return void
     */
    public function dispatch()
    {
        $router = new Router();
        $options = $router->run();
        
        $class = '\\app\\' . $this->_controllersDirectory . '\\' . $options['controller'];
        
        if (!class_exists($class))
            throw new Exception('Controller class "' . $class . '" not found');
        
        $obj = new $class(new Request($options));
        // Setting view renderer
        $obj->setView($this->_viewRenderer);
        
        $action = $options['action'] . $this->_actionPostfix;
        
        if (!is_callable(array($obj, $action)))
            if (Registry::get('debug'))
                throw new Exception('Action "' . $class . '::' . $action . '" not found');
            else
                throw new NotFoundException('404 Not Found');
        
        $obj->before();
        call_user_func(array($obj, $action));
        $obj->after();
    }
}
