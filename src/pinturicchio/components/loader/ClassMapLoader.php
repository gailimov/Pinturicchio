<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\components\loader;

require_once 'SplLoader.php';

/**
 * Class-map loader
 * 
 * Usage example:
 * 
 *     $loader = new ClassMapLoader();
 *     $loader->add(array(
 *         'pinturicchio\components\Registry' => __DIR__ . '/../pinturicchio/components/Registry.php',
 *         'pinturicchio\components\Config' => __DIR__ . '/../pinturicchio/components/Config.php'
 *     ));
 *     $loader->registerAutoload();
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class ClassMapLoader extends SplLoader
{
    /**
     * Map
     * 
     * @var array
     */
    private $_map = array();
    
    /**
     * Adds new map
     * 
     * Adds a new class-map consisting of the name and file of the class
     * 
     * Usage example:
     * 
     *     $loader = new ClassMapLoader();
     *     $loader->add(array(
     *         'pinturicchio\components\Registry' => __DIR__ . '/../pinturicchio/components/Registry.php',
     *         'pinturicchio\components\Config' => __DIR__ . '/../pinturicchio/components/Config.php'
     *     ));
     * 
     * @param  array $map Map
     * @return pinturicchio\components\loader\ClassMapLoader
     */
    public function add(array $map)
    {
        foreach ($map as $class => $file) {
            if ($class[0] == '\\')
                $class = substr($class, 1);
            if (!isset($this->_map[$class]))
                $this->_map[$class] = $file;
        }
        
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
        if (isset($this->_map[$className])) {
            require_once $this->_map[$className];
            return;
        }
        
        // Nothing found - throw exception
        require_once 'Exception.php';
        throw new Exception('Class "' . $className . '" not found');
    }
}
