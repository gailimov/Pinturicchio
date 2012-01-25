<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\components;

/**
 * Registry
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Registry
{
    /**
     * Singleton instance
     * 
     * @var \pinturicchio\components\Registry
     */
    private static $_instance;
    
    /**
     * Registry
     * 
     * @var array
     */
    private $_registry = array();
    
    private function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    /**
     * Sets value by key
     * 
     * @param  string $key   Key
     * @param  mixed  $value Value
     * @return void
     */
    public static function set($key, $value)
    {
        self::getInstance()->_registry[(string) $key] = $value;
    }
    
    /**
     * Returns value by key
     * 
     * @param  string $key Key
     * @return mixed || null if key not exists
     */
    public static function get($key)
    {
        if (isset(self::getInstance()->_registry[(string) $key]))
            return self::getInstance()->_registry[$key];
        return null;
    }
    
    /**
     * Returns singleton instance
     * 
     * @return \pinturicchio\components\Registry
     */
    private static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }
}
