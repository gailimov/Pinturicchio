<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\view;

use pinturicchio\Config,
    pinturicchio\Registry;

/**
 * PHP renderer
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class PhpRenderer implements Renderer
{
    /**
     * Directory
     * 
     * @var string
     */
    private $_directory = 'views';
    
    /**
     * File extension
     * 
     * @var string
     */
    private $_fileExtension = '.php';
    
    /**
     * Layout directory
     * 
     * @var string
     */
    private $_layoutDirectory = 'layouts';
    
    /**
     * Layout
     * 
     * @var string
     */
    private $_layout = 'main';
    
    /**
     * Content key. Contains rendered template
     * 
     * @var string
     */
    private $_contentKey = 'content';
    
    /**
     * Key of the config
     * 
     * @var string
     */
    private $_configKey = 'views';
    
    public function __construct()
    {
        // Set the properties values from config
        if (isset(Config::factory()->params[$this->_configKey]))
            $this->setFromConfig(array_keys(Config::factory()->params[$this->_configKey]));
    }
    
    /**
     * Invokes helper magically
     * 
     * @param  string $helper Helper name
     * @param  array  $args   Argumants
     * @return string Output result of helper
     */
    public function __call($helper, array $args)
    {
        $class = '\\pinturicchio\\view\\helpers\\' . ucfirst($helper);
        $this->ensure(file_exists(Registry::get('rootPath') . str_replace('\\', '/', $class) . '.php'),
                      'View helper class "' . $class . '" not found');
        
        return call_user_func_array(array($class, $helper), $args);
    }
    
    /**
     * Sets directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\PhpRenderer
     */
    public function setDirectory($directory)
    {
        $this->_directory = (string) $directory;
        return $this;
    }
    
    /**
     * Returns directory
     * 
     * @return string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }
    
    /**
     * Sets file extension
     * 
     * @param  string $fileExtension File extension
     * @return \pinturicchio\PhpRenderer
     */
    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = (string) $fileExtension;
        return $this;
    }
    
    /**
     * Returns file extension
     * 
     * @return string
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }
    
    /**
     * Sets layout directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\PhpRenderer
     */
    public function setLayoutDirectory($directory)
    {
        $this->_layoutDirectory = (string) $directory;
        return $this;
    }
    
    /**
     * Returns layout directory
     * 
     * @return string
     */
    public function getLayoutDirectory()
    {
        return $this->_layoutDirectory;
    }
    
    /**
     * Sets layout
     * 
     * @param  string $layout Layout
     * @return \pinturicchio\PhpRenderer
     */
    public function setLayout($layout)
    {
        $this->_layout = (string) $layout;
        return $this;
    }
    
    /**
     * Returns layout
     * 
     * @return string
     */
    public function getLayout()
    {
        return $this->_layout;
    }
    
    /**
     * Sets content key
     * 
     * @param  string $contentKey Content key
     * @return \pinturicchio\PhpRenderer
     */
    public function setContentKey($contentKey)
    {
        $this->_contentKey = (string) $contentKey;
        return $this;
    }
    
    /**
     * Returns content key
     * 
     * @return string
     */
    public function getContentKey()
    {
        return $this->_contentKey;
    }
    
    /**
     * Render partial template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public function renderPartial($template, $params = array())
    {
        echo $this->fetchPartial($template, $params);
    }
    
    /**
     * Render template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public function render($template, $params = array())
    {
        echo $this->fetch($template, $params);
    }
    
    /**
     * Fetch partial template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private function fetchPartial($template, $params = array())
    {
        extract($params, EXTR_SKIP);
        ob_start();
        $file = Registry::get('appPath') . '/' . $this->_directory . '/' . $template . $this->_fileExtension;
        $this->ensure(file_exists($file), 'View file "' . $file . '" not found');
        include_once $file;
        
        return ob_get_clean();
    }
    
    /**
     * Fetch template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private function fetch($template, $params = array())
    {
        $content = $this->fetchPartial($template, $params);
        return $this->fetchPartial(
            $this->_layoutDirectory . '/' . $this->_layout,
            array($this->_contentKey => $content)
        );
    }
    
    /**
     * Sets property value from config
     * 
     * @param  array || string $property Array of properties or one preperty
     * @return \pinturicchio\PhpRenderer
     */
    private function setFromConfig($property)
    {
        if (is_array($property)) {
            for ($i = 0; $i < count($property); $i++)
                $this->invokeSetter('set' . ucfirst((string) $property[$i]), (string) $property[$i]);
        } else {
            $this->invokeSetter('set' . ucfirst((string) $property), (string) $property);
        }
    }
    
    /**
     * Invokes setter
     * 
     * @param  string $setter   Setter
     * @param  string $property Property
     * @return \pinturicchio\PhpRenderer
     */
    private function invokeSetter($setter, $property)
    {
        $this->ensure(in_array($setter, get_class_methods(__CLASS__)), 'Property "' . $property . '" not exists');
        if (isset(Config::factory()->params[$this->_configKey][$property]))
            $this->$setter(Config::factory()->params[$this->_configKey][$property]);
    }
    
    /**
     * Throws an exception if the expression is false
     * 
     * @param  mixed  $expr    Expression
     * @param  string $message Error message
     * @return void
     */
    private function ensure($expr, $message)
    {
        if (!$expr)
            throw new Exception($message);
    }
}
