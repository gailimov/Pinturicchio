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
     * Singleton instance
     * 
     * @var \pinturicchio\Config
     */
    private static $_instance;
    
    /**
     * Constructor
     * 
     * @param string $config Config file
     */
    private function __construct($config)
    {
        $file = FrontController::getInstance()->config['basePath'] . '/' . $this->_directory . '/' . $config . '.php';
        if (!file_exists($file))
            throw new Exception('Config file "' . $file . '" not found');
        $this->params = require $file;
    }
    
    /**
     * Returns singleton instance
     * 
     * @param  string $config Config file
     * @return \pinturicchio\Config
     */
    public static function getInstance($config = self::DEFAULT_CONFIG)
    {
        if (!self::$_instance)
            self::$_instance = new self((string) $config);
        return self::$_instance;
    }
}
