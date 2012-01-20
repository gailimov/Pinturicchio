<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio;

/**
 * URL router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Router
{
    /**
     * Params
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * Running
     * 
     * @return array An array with controller, action and params
     */
    public function run()
    {
        return $this->route($this->getActiveRoute($this->prepareUri()));
    }
    
    /**
     * Creates URL
     * 
     * Using example:
     * 
     * 'urlScheme' => array(
     *     'home' => array('^$', 'Site::index'),
     *     'greeting' => array('^hello/(?P<name>[-_a-z0-9]+)$', 'Site::greet')
     * )
     * 
     * $this->createUrl('greeting', array('name' => 'john'));
     * 
     * Returns relative URL: '/hello/john'
     * 
     * If site located in subdirectory named 'subdir', then returns '/subdir/hello/john'
     * 
     * $this->createUrl('greeting', array('name' => 'john'), true);
     * 
     * Returns absolute URL: 'http://example.com/hello/john'
     * 
     * If site located in subdirectory named 'subdir', then returns 'http://example.com/subdir/hello/john'
     * 
     * @param  string $name     URL scheme name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function createUrl($name, array $params = null, $absolute = false, $https = false)
    {
        foreach (Config::factory()->params['urlScheme'] as $schemeName => $scheme) {
            if ($schemeName == $name) {
                $scheme = $this->transform($scheme);
                $replacement = ($params) ? '%s' : '';
                /** @TODO: Пофиксить, чтобы заменялись только именованный параметры, с соотвествующим ключем в params */
                $url = preg_replace('/\([^\)]*\)/', $replacement, $scheme['pattern']);
                $url = str_replace('^', '', $url);
                $url = str_replace('$', '', $url);
            }
        }
        
        if (!$absolute) {        
            if ($params)
                return $this->getBaseDirectory() . vsprintf($url, $params);
            return $this->getBaseDirectory() . $url;
        } else {
            return $this->getHostInfo($https) . $this->createUrl($name, $params);
        }
    }
    
    /**
     * Prepares URI
     * 
     * @return string URI
     */
    private function prepareUri()
    {
        $uri = $this->getBaseDirectory();
        // Necessary if site in the subdirectory
        $uri = preg_replace('/^' . preg_quote($uri, '/') . '/is', '', $_SERVER['REQUEST_URI']);
        $uri = preg_replace('/(\/?)(\?.*)?$/is', '', $uri);
        // Cuts out unnecessary symbols
        $uri = preg_replace('/[^0-9A-Za-zА-Яа-я._%\\-\\/]/is', '', $uri);
        
        return $uri;
    }
    
    /**
     * Returns base directory
     * 
     * @return string
     */
    private function getBaseDirectory()
    {
        return preg_replace('/^(.*?)index\.php$/is', '$1', $_SERVER['SCRIPT_NAME']);
    }
    
    /**
     * Returns the scheme and host part of the URI
     * 
     * @param  bool $https Use HTTPS?
     * @return string
     */
    private function getHostInfo($https = false)
    {
        if ($https)
            return 'https://' . $_SERVER['HTTP_HOST'];
        return 'http://' . $_SERVER['HTTP_HOST'];
    }
    
    /**
     * Returns active route comparing it with URI
     * 
     * @param  string $uri URI
     * @return string
     */
    private function getActiveRoute($uri)
    {
        foreach (Config::factory()->params['urlScheme'] as $name => $scheme) {
            $scheme = $this->transform($scheme);
            if (preg_match('#' . $scheme['pattern'] . '#', $uri, $matches)) {
                $this->_params = $_GET = array_merge(array_slice(array_unique($matches), 1), $_GET);
                return $scheme['route'];
            }
        }
        
        // Nothing matched - throw exception
        throw new NotFoundException('404 Not Found');
    }
    
    /**
     * Transforms an array of URL schema into associative
     * 
     * Example:
     * 
     * $scheme = $this->transform(array('^hello/(?P<name>[-_a-z0-9]+)$', 'Site::greet'));
     * print_r($scheme);
     * 
     * Output:
     * 
     * array(
     *     'pattern' => '^hello/(?P<name>[-_a-z0-9]+)$',
     *     'route' => 'Site::greet'
     * )
     * 
     * @param  array $scheme URL scheme
     * @return array
     */
    private function transform(array $scheme)
    {
        return array(
            'pattern' => $scheme[0],
            'route' => $scheme[1]
        );
    }
    
    /**
     * Routing
     * 
     * Returns an array with controller, action and params
     * 
     * @param  string $route Route
     * @return array
     */
    private function route($route)
    {
        $route = explode('::', $route);
        return array(
            'controller' => $route[0],
            'action' => $route[1],
            'params' => $this->_params
        );
    }
}
