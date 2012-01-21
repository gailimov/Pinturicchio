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
     * Alias separator
     */
    const ALIAS_SEPARATOR = '.';
    
    /**
     * Singleton instance
     *
     * @var \pinturicchio\FrontController
     */
    private static $_instance;
    
    /**
     * Router instance
     * 
     * @var \pinturicchio\Router
     */
    private $_router;
    
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
    
    private function __construct()
    {
        if (isset(Config::getInstance()->params['viewRenderer'])) {
            $viewRenderer = $this->createClassNameFromAlias(Config::getInstance()->params['viewRenderer']);
            $this->setViewRenderer(new $viewRenderer());
        } else {
            $this->_viewRenderer = new PhpRenderer();
        }
    }
    
    private function __clone()
    {
    }
    
    /**
     * Returns singleton instance
     * 
     * @return \pinturicchio\FrontController
     */
    public static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
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
     * Returns router object
     * 
     * @return \pinturicchio\Router
     */
    public function getRouter()
    {
        if (!$this->_router)
            $this->_router = new Router();
        return $this->_router;
    }
    
    /**
     * Creates class name from alias
     * 
     * @param  string $alias Alias
     * @return string
     */
    public function createClassNameFromAlias($alias)
    {
        return '\\' . str_replace(self::ALIAS_SEPARATOR, '\\', (string) $alias);
    }
    
    /**
     * Dispatching
     * 
     * @return void
     */
    public function dispatch()
    {
        $options = $this->getRouter()->run();
        
        $class = '\\app\\' . $this->_controllersDirectory . '\\' . $options['controller'];
        
        if (!file_exists(Registry::get('rootPath') . str_replace('\\', '/', $class) . '.php'))
            throw new Exception('Controller class "' . $class . '" not found');
        
        $obj = new $class(new Request($options));
        // Setting view renderer
        $obj->setView($this->_viewRenderer);
        
        $action = $options['action'] . $this->_actionPostfix;
        
        if (!is_callable(array($obj, $action))) {
            if (Registry::get('debug'))
                throw new Exception('Action "' . $class . '::' . $action . '" not found');
            else
                throw new NotFoundException('404 Not Found');
        }
        
        $obj->before();
        call_user_func(array($obj, $action));
        $obj->after();
    }
}
