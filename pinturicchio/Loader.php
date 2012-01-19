<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio;

/**
 * Class loader
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Loader
{
    /**
     * Paths
     * 
     * @var array
     */
    private $_paths = array();
    
    /**
     * Sets path
     * 
     * @param  string $path Path
     * @return \pinturicchio\Loader
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
     * @return bool true if success
     */
    private function load($className)
    {
        $pathToClass = str_replace('\\', '/', $className);
        
        foreach ($this->_paths as $path) {
            $file = $path . '/' . $pathToClass . '.php';
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        
        // Nothing found - throw exception
        throw new Exception('Class "' . $className . '" not found');
    }
}
