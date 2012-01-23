<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\view;

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
     * Invokes helper magically
     * 
     * @param  string $helper Helper name
     * @param  array  $args   Argumants
     * @return string Output result of helper
     */
    public function __call($helper, array $args)
    {
        $class = '\\pinturicchio\\view\\helpers\\' . ucfirst($helper);
        $this->ensure(file_exists(__DIR__ . '/helpers/' . ucfirst($helper) . '.php'),
                      'View helper class "' . $class . '" not found');
        
        return call_user_func_array(array($class, $helper), $args);
    }
    
    /**
     * Sets options
     * 
     * @param  array $options Options
     * @return \pinturicchio\PhpRenderer
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . ucfirst($key);
            $this->ensure(in_array($setter, get_class_methods(__CLASS__)), 'Property "' . $key . '" not exists');
            $this->$setter($value);
        }
        
        return $this;
    }
    
    /**
     * Sets directory
     * 
     * @param  string $directory Directory
     * @return \pinturicchio\PhpRenderer
     */
    public function setDirectory($directory)
    {
        $this->ensure(is_dir((string) $directory), '"' . $directory . '" is not a valid views directory');
        $this->_directory = $directory;
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
        $this->ensure(is_dir($this->getDirectory() . '/' . (string) $directory),
                      '"' . $directory . '" is not a valid layouts directory');
        $this->_layoutDirectory = $directory;
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
        $file = $this->getDirectory() . '/' . $template . $this->getFileExtension();
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
            $this->getLayoutDirectory() . '/' . $this->getLayout(),
            array($this->getContentKey() => $content)
        );
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
