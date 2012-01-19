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
     * Default config filename
     */
    const DEFAULT_CONFIG = 'main';
    
    /**
     * Alias separator
     */
    const ALIAS_SEPARATOR = '.';
    
    /**
     * Params
     * 
     * @var array
     */
    public $params = array();
    
    /**
     * Directory
     * 
     * @var string
     */
    private $_directory = 'config';
    
    /**
     * Constructor
     * 
     * @param string $config Config file
     */
    private function __construct($config)
    {
        $file = Registry::get('appPath') . '/' . $this->_directory . '/' . $config . '.php';
        if (!file_exists($file))
            throw new Exception('Config file "' . $file . '" not found');
        $this->params = require $file;
    }
    
    /**
     * Returns new instance
     * 
     * @param  string $config Config file
     * @return \pinturicchio\Config
     */
    public static function factory($config = self::DEFAULT_CONFIG)
    {
        return new self((string) $config);
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
}
