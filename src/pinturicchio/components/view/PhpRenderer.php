<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\components\view;

/**
 * PHP renderer
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class PhpRenderer implements Renderer
{
    /**
     * Helpers directory
     */
    const HELPERS_DIRECTORY = 'helpers';
    
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
     * Helper options
     * 
     * @var array
     */
    private $_helpersOptions = array();
    
    public function __construct()
    {
        $this->_helpersOptions = array(
            'default' => array(
                'directory' => __DIR__ . '/' . self::HELPERS_DIRECTORY,
                'namespace' => __NAMESPACE__ . '\\' . self::HELPERS_DIRECTORY
            )
        );
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
        foreach ($this->_helpersOptions as $key => $value) {
            if ($value['namespace'][0] == '\\')
                $value['namespace'] = substr($value['namespace'], 1);
            $class = '\\' . $value['namespace'] . '\\' . ucfirst($helper);
            if (file_exists($value['directory'] . '/' . ucfirst($helper) . '.php'))
                return call_user_func_array(array(new $class, $helper), $args);
        }
        
        // Helper not found - throws exception
        throw new Exception('View helper "' . ucfirst($helper) . '" not found');
    }
    
    /**
     * Sets options
     * 
     * @param  array $options Options
     * @return \pinturicchio\components\PhpRenderer
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
     * @return \pinturicchio\components\PhpRenderer
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
     * @return \pinturicchio\components\PhpRenderer
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
     * @return \pinturicchio\components\PhpRenderer
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
     * @return \pinturicchio\components\PhpRenderer
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
     * @return \pinturicchio\components\PhpRenderer
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
     * Sets helpers options
     * 
     * Usage example:
     * 
     *     $renderer->setHelpersOptions(array(
     *         // Don't call array as "default", this reserved name
     *         'app' => array(
     *             'directory' => __DIR__ . '/views/helpers',
     *             'namespace' => 'app\views\helpers'
     *         )
     *     ));
     * 
     * @param  array $options Options
     * @return \pinturicchio\components\PhpRenderer
     */
    public function setHelpersOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->ensure(is_dir((string) $value['directory']),
                          '"' . $value['directory'] . '" is not a valid helpers directory');
        }
        
        $this->_helpersOptions = array_merge($options, $this->_helpersOptions);
        
        return $this;
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
