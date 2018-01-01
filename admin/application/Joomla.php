<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary;

use JFactory; 

// No direct access
defined('_JEXEC') or die;

class Joomla
{
    private static $app;
    private static $user;
    
    /**
     * Joomla User Interface
     */
    public static function getUser() {
        if(!isset(self::$user)) {
            self::$user = JFactory::getUser();
        }
        return self::$user;
    }
    
    /**
     * Joomla Application Interface
     */
    public static function getApplication($mode = 'administrator') {
        if(!isset(self::$app)) {
            self::$app = JFactory::getApplication($mode);
        }
        return self::$app; 
    }
    
    /**
     * Joomla Cache Interface
     */
    public static function getCache() {
        return JFactory::getCache('com_secretary', '');
    }
     
}
