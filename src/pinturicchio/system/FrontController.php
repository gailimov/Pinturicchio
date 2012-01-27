<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\system;

use pinturicchio\system\http\Request,
    pinturicchio\system\loader\Loader as SystemLoader,
    pinturicchio\components\Config,
    pinturicchio\components\Router,
    pinturicchio\components\loader\Loader,
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
     * Default app namespace
     */
    const DEFAULT_APP_NAMESPACE = 'app';
    
    /**
     * Default autoload flag
     */
    const DEFAULT_AUTOLOAD_FLAG = true;
    
    /**
     * Alias separator
     */
    const ALIAS_SEPARATOR = '.';
    
    /**
     * Key of the views config
     */
    const CONFIG_VIEWS_KEY = 'views';
    
    /**
     * Key of the routes config
     */
    const CONFIG_ROUTES_KEY = 'routes';
    
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
     * \pinturicchio\components\Config
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
     * Running
     * 
     * @param  string $config Config file
     * @return void
     */
    public function run($config)
    {
        // Initialization of config
        $this->initConfig($config);
        
        // Setting aliases
        $this->_aliases['app'] = $this->_config->basePath;
        $this->_aliases['system'] = __DIR__;
        
        // Initialization of loader
        $this->initLoader();
        
        if (isset($this->_config->controllersDirectory))
            $this->setControllersDirectory($this->_config->controllersDirectory);
        
        // Initialization of view
        $this->initView();
        
        // Dispatching
        $this->dispatch();
    }
    
    /**
     * Initializes config
     * 
     * @return void
     */
    public function initConfig($config)
    {
        if (!file_exists($config))
            throw new Exception('Config file "' . $config . '" not found');
        require_once __DIR__ . '/../components/Config.php';
        $this->_config = new Config(require_once $config);
        if (!isset($this->_config->basePath))
            $this->_config->basePath = __DIR__ . '/../../' . self::DEFAULT_APP_NAMESPACE;
        elseif (!is_dir($this->_config->basePath))
            throw new Exception('"' . $this->_config->basePath . '" is not a valid application path');
        if (!isset($this->_config->namespace))
            $this->_config->namespace = self::DEFAULT_APP_NAMESPACE;
        if (!isset($this->_config->autoload))
            $this->_config->autoload = self::DEFAULT_AUTOLOAD_FLAG;
    }
    
    /**
     * Initializes loader
     * 
     * @return void
     */
    public function initLoader()
    {
        if ($this->_config->autoload) {
            require_once __DIR__ . '/../components/loader/Loader.php';
            $loader = new Loader();
            $loader->setPath(__DIR__ . '/../..')
                   ->registerAutoload();
        } else {
            require_once __DIR__ . '/loader/Loader.php';
            $map = array();
            if (isset($this->_config->import)) {
                foreach ($this->_config->import->toArray() as $import)
                    $map += array($this->createClassNameFromAlias($import) => $this->getPathOfAlias($import) . '.php');
            }
            $loader = new SystemLoader();
            $loader->setPath(realpath(__DIR__ . '/../..'))
                   ->addAppClassMap($map)
                   ->registerAutoload();
        }
    }
    
    /**
     * Sets controllers directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\system\FrontController
     */
    public function setControllersDirectory($directory)
    {
        if (!is_dir($this->_config->basePath . '/' . (string) $directory))
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
                    $this->_aliases[$rootAlias] . '/' . str_replace('.', '/', substr($alias, $pos + 1)), '*/'
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
                $array = array();
                // Setting directory
                if (isset($config['directory']))
                    $array['directory'] = $this->getPathOfAlias($config['directory']);
                else
                    $array['directory'] = $this->_config->basePath . '/views';
                // Setting helpers options
                if (isset($config['helpersOptions'])) {
                    $array['helpersOptions']['app']['directory'] = $this->getPathOfAlias($config['helpersOptions']['directory']);
                    $array['helpersOptions']['app']['namespace'] = $this->createClassNameFromAlias($config['helpersOptions']['directory']);
                } else {
                    $array['helpersOptions']['app']['directory'] = $this->getPathOfAlias($this->_config->namespace . '.views.helpers');
                    $array['helpersOptions']['app']['namespace'] = $this->_config->namespace . '\views\helpers';
                }
                $array['helpersOptions']['system']['directory'] = $this->getPathOfAlias('system.view.helpers');
                $array['helpersOptions']['system']['namespace'] = 'pinturicchio\system\view\helpers';
                // For moving 'directory' before 'layoutsDirectory'
                $config = $array + $config->toArray();
                $this->setOptions($this->getViewRenderer(), $config);
            } else {
                $this->getViewRenderer()->setOptions(array(
                    'directory' => $this->_config->basePath . '/views',
                    'layoutDirectory' => 'layouts',
                    'layout' => 'main',
                    'fileExtension' => '.php',
                    'contentKey' => 'content',
                    'helpersOptions' => array(
                        'app' => array(
                            'directory' => $this->getPathOfAlias($this->_config->namespace . '.views.helpers'),
                            'namespace' => $this->_config->namespace . '\views\helpers'
                        ),
                        'system' => array(
                            'directory' => $this->getPathOfAlias('system.view.helpers'),
                            'namespace' => 'pinturicchio\system\view\helpers'
                        )
                    )
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
        $options = $this->getRouter()->addRoutes($this->_config->{self::CONFIG_ROUTES_KEY}->toArray())->run();
        
        $class = '\\' . $this->_config->namespace . '\\' . $this->getControllersDirectory() . '\\' . $options['controller'];
        
        if (!file_exists($this->_config->basePath . '/' . $this->getControllersDirectory() . '/' . $options['controller'] . '.php'))
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
