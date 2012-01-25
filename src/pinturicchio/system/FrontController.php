<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\system;

use pinturicchio\system\http\Request,
    pinturicchio\components\Registry,
    pinturicchio\components\Config,
    pinturicchio\components\Router,
    pinturicchio\components\view\Renderer,
    pinturicchio\components\view\PhpRenderer;

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
     * Key of the views config
     */
    const CONFIG_VIEWS_KEY = 'views';
    
    /**
     * Action postfix
     */
    const ACTION_POSTFIX = 'Action';
    
    /**
     * Singleton instance
     *
     * @var pinturicchio\system\FrontController
     */
    private static $_instance;
    
    /**
     * \pinturicchio\Config
     * 
     * @var \pinturicchio\components\Config
     */
    private $_config;
    
    /**
     * Aliases
     * 
     * @var array
     */
    private $_aliases = array();
    
    /**
     * Router instance
     * 
     * @var \pinturicchio\components\Router
     */
    private $_router;
    
    /**
     * Config directory
     * 
     * @var string
     */
    private $_configDirectory = 'config';
    
    /**
     * Default config filename
     * 
     * @var string
     */
    private $_configName = 'main';
    
    /**
     * Controllers directory
     * 
     * @var string
     */
    private $_controllersDirectory = 'controllers';
    
    /**
     * @var \pinturicchio\components\view\Renderer
     */
    private $_viewRenderer;
    
    private function __construct()
    {
        // Initialization of config
        $this->initConfig();
        
        $this->_aliases['app'] = Registry::get('appPath');
        
        if (isset($this->_config->controllersDirectory))
            $this->setControllersDirectory($this->_config->controllersDirectory);
        
        // Initialization of view
        $this->initView();
    }
    
    private function __clone()
    {
    }
    
    /**
     * Returns singleton instance
     * 
     * @return \pinturicchio\system\FrontController
     */
    public static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }
    
    /**
     * Initializes config
     * 
     * @return void
     */
    public function initConfig()
    {
        $config = Registry::get('appPath') . '/' . $this->_configDirectory . '/' . $this->_configName . '.php';
        $this->_config = new Config(require_once $config);
    }
    
    /**
     * Sets controllers directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\system\FrontController
     */
    public function setControllersDirectory($directory)
    {
        if (!is_dir(Registry::get('appPath') . '/' . (string) $directory))
            throw new Exception('"' . $directory . '" is not a valid controllers directory');
        $this->_controllersDirectory = $directory;
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
     * @param  \pinturicchio\components\view\Renderer $viewRenderer
     * @return \pinturicchio\system\FrontController
     */
    public function setViewRenderer(Renderer $viewRenderer)
    {
        $this->_viewRenderer = $viewRenderer;
        return $this;
    }
    
    /**
     * Returns view renderer object
     * 
     * @return \pinturicchio\components\view\Renderer
     */
    public function getViewRenderer()
    {
        return $this->_viewRenderer;
    }
    
    /**
     * Returns router object
     * 
     * @return \pinturicchio\components\Router
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
     * Converts an alias in the appropriate path
     * 
     * @param  string $alias Alias
     * @return string if succes, bool false if fails
     */
    public function getPathOfAlias($alias)
    {
        if (isset($this->_aliases[$alias])) {
            return $this->_aliases[$alias];
        } else if (($pos = strpos($alias, '.')) !== false) {
            $rootAlias = substr($alias, 0, $pos);
            if (isset($this->_aliases[$rootAlias])) {
                return $this->_aliases[$alias] = rtrim(
                    $this->_aliases[$rootAlias] . '/' . str_replace('.', '/', substr($alias, $pos + 1)), '*'. '/'
                );
            }
        }
        
        return false;
    }
    
    /**
     * Initializes view
     * 
     * @return void
     */
    public function initView()
    {
        if (isset($this->_config->viewRenderer)) {
            $viewRenderer = $this->createClassNameFromAlias($this->_config->viewRenderer);
            $this->setViewRenderer(new $viewRenderer());
        } else {
            $this->setViewRenderer(new PhpRenderer());
            // Set the properties values from config if it's exists, otherwise set default values
            if (isset($this->_config->{self::CONFIG_VIEWS_KEY})) {
                $config = $this->_config->{self::CONFIG_VIEWS_KEY};
                if (isset($config['directory']))
                    $config['directory'] = $this->getPathOfAlias($config['directory']);
                else
                    $config['directory'] = Registry::get('appPath') . '/views';
                // For moving 'directory' before 'layoutsDirectory'. Yes, it's sucks
                /** @TODO Think of something better */
                asort($config);
                $this->setOptions($this->getViewRenderer(), $config);
            } else {
                $this->getViewRenderer()->setOptions(array(
                    'directory' => Registry::get('appPath') . '/views',
                    'layoutDirectory' => 'layouts',
                    'layout' => 'main',
                    'fileExtension' => '.php',
                    'contentKey' => 'content'
                ));
            }
        }
    }
    
    /**
     * Dispatching
     * 
     * @return void
     */
    public function dispatch()
    {
        $options = $this->getRouter()->addRoutes($this->_config->routes->toArray())->run();
        
        $class = '\\app\\' . $this->getControllersDirectory() . '\\' . $options['controller'];
        
        if (!file_exists(Registry::get('rootPath') . str_replace('\\', '/', $class) . '.php'))
            throw new Exception('Controller class "' . $class . '" not found');
        
        $obj = new $class(new Request($options));
        // Setting view renderer
        $obj->setView($this->getViewRenderer());
        
        $action = $options['action'] . self::ACTION_POSTFIX;
        
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
    
    /**
     * Sets options
     * 
     * @param  object $obj      Object whose properties setted
     * @param  array  $property Options
     * @return object
     */
    private function setOptions($obj, array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (!in_array($setter, get_class_methods($obj)))
                throw new Exception('Config property "' . $key . '" not exists');
            $obj->$setter($value);
        }
    }
}
