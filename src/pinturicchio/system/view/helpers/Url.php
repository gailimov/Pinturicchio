<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\system\view\helpers;

use pinturicchio\system\FrontController;

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
     * @see \pinturicchio\components\Router::createUrl()
     * 
     * @param  string $name     URL scheme name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function url($name, array $params = null, $absolute = false, $https = false)
    {
        return FrontController::getInstance()->getRouter()->createUrl($name, $params, $absolute, $https);
    }
}
