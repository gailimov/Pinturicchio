<?php

/**
 * Pinturicchio
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3
 */


namespace pinturicchio\view;

/**
 * Interface for renderers
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
interface Renderer
{
    /**
     * Render partial template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public function renderPartial($template, $params = array());
    
    /**
     * Render template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public function render($template, $params = array());
}
