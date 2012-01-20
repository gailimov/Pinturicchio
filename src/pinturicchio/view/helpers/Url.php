<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\view\helpers;

use pinturicchio\Router;

/**
 * URL helper
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Url
{
    /**
     * Creates URL
     * 
     * @see \pinturicchio\Router::createUrl()
     * 
     * @param  string $name     URL scheme name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function url($name, array $params = null, $absolute = false, $https = false)
    {
        $router = new Router();
        return $router->createUrl($name, $params, $absolute, $https);
    }
}
