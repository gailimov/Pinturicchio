<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\components\loader;

/**
 * Abstract class for SPL loaders
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
abstract class SplLoader
{
    /**
     * Registers autoload method
     * 
     * @return void
     */
    abstract public function registerAutoload();
    
    /**
     * Loads class
     * 
     * @param  string $className Class name
     * @return void
     */
    abstract protected function load($className);
}
