<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\system\loader;

require_once __DIR__ . '/../../components/loader/SplLoader.php';

use pinturicchio\components\loader\SplLoader;

/**
 * Loader
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Loader extends SplLoader
{
    /**
     * System class-map
     * 
     * @var array
     */
    private $_systemClassMap = array();
    
    /**
     * Components class-map
     * 
     * @var array
     */
    private $_componentsClassMap = array();
    
    /**
     * App class-map
     * 
     * @var array
     */
    private $_appClassMap = array();
    
    /**
     * Paths
     * 
     * @var array
     */
    private $_paths = array();
    
    public function __construct()
    {
        $map = require_once __DIR__ . '/map.php';
        $this->_systemClassMap = $map['system'];
        $this->_componentsClassMap = $map['components'];
    }
    
    /**
     * Adds new app class map
     * 
     * Adds a new class-map consisting of the name and file of the class
     * 
     * @param  array $map Map
     * @return \pinturicchio\system\loader\Loader
     */
    public function addAppClassMap(array $map)
    {
        foreach ($map as $class => $file) {
            if ($class[0] == '\\')
                $class = substr($class, 1);
            if (!isset($this->_appClassMap[$class]))
                $this->_appClassMap[$class] = $file;
        }
        
        return $this;
    }
    
    /**
     * Sets path
     * 
     * @param  string $path Path
     * @return pinturicchio\components\Loader
     */
    public function setPath($path)
    {
        if (!in_array((string) $path, $this->_paths))
            $this->_paths[] = $path;
        return $this;
    }
    
    /**
     * Registers autoload method
     * 
     * @return void
     */
    public function registerAutoload()
    {
        spl_autoload_register(array($this, 'load'));
    }
    
    /**
     * Loads class
     * 
     * @param  string $className Class name
     * @return void
     */
    protected function load($className)
    {
        // System classes
        if (isset($this->_systemClassMap[$className])) {
            require_once $this->_systemClassMap[$className];
        // Components classes
        } elseif (isset($this->_componentsClassMap[$className])) {
            require_once $this->_componentsClassMap[$className];
        // App classes
        } elseif (isset($this->_appClassMap[$className])) {
            require_once $this->_appClassMap[$className];
        } else {
            // Nothing found - throw exception
            require_once 'Exception.php';
            throw new Exception('Class "' . $className . '" not found');
        }
    }
}
