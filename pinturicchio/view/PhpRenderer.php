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
    
    public function __construct()
    {
        /** @TODO Вынести в отдельный метод (DRY) */
        if (isset(Config::factory()->params['views']['directory']))
            $this->setDirectory(Config::factory()->params['views']['directory']);
        if (isset(Config::factory()->params['views']['fileExtension']))
            $this->setFileExtension(Config::factory()->params['views']['fileExtension']);
        if (isset(Config::factory()->params['views']['layoutDirectory']))
            $this->setLayoutDirectory(Config::factory()->params['views']['layoutDirectory']);
        if (isset(Config::factory()->params['views']['layout']))
            $this->setLayout(Config::factory()->params['views']['layout']);
        if (isset(Config::factory()->params['views']['contentKey']))
            $this->setContentKey(Config::factory()->params['views']['contentKey']);
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
        if (!file_exists($file))
            throw new Exception('View file "' . $file . '" not found');
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
}
