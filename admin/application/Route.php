<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

use JFactory;
use JString;
use JRoute;

// No direct access
defined('_JEXEC') or die;

class Route
{
    
    public static function create( $view = '', $fields = array() )
    {
        $url = 'index.php?option=com_secretary';
        
        if(!empty($view))
            $url .= '&view='.$view;
        
        if(!empty($fields)) {
            foreach($fields AS $key => $value) {
                if(!empty($key) || !empty($value)) {
                    $url .= "&". $key ."=". $value;
                }
            }
        }
        
        return JRoute::_($url, false);
    }
    
    public static function safeURL($string)
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $string);
        
        $lang = JFactory::getLanguage();
        $str = $lang->transliterate($str);
        
        // Trim white spaces at beginning and end of alias and make lowercase
        $str = trim(JString::strtolower($str));
        
        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);
        
        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');
        
        return $str;
    }
}

