<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Utilities;

// No direct access
defined('_JEXEC') or die;

class Text
{
    /**
     * Make a string to only contain letters 
     */
    public static function onlyLetters($s,$additional = NULL) {
        return preg_replace("/[^a-zA-Z".$additional."]+/", "", $s);
    }
    
    /**
     * Make a string alphanumeric and remove all other characters
     */
    public static function alphanumeric($s) {
        if(is_array($s)) {
            $s = implode('_',$s);
        }
        return preg_replace("/[^a-zA-Z0-9]+/", "", $s);
    }
    
    /**
     * Method to remove all tags 
     */
    public static function ripTags($string)
    {
        
        $string = strip_tags($string);
        
        // ----- remove HTML TAGs -----
        $string = preg_replace ('/\<[^>]*\>/', ' ', $string);
        
        // ----- remove control characters -----
        $string = str_replace("\t", ' ', $string);   // --- replace with space
        $string	= str_replace( array("\n","\r","\r\n"), "<br />", $string);
        $string	= str_replace("<br /><br />", "<br />", $string);
        
        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        
        return $string;
        
    }
    
    public static function prepareTextarea($str,$input = true) {
        $str	= \Secretary\Utilities::cleaner($str,true);
        if($input)
            $str	= preg_replace('/\<br(\s*)?\/?\>/i', "\n", $str);
        return $str;
    }
}