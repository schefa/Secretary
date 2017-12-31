<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
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
