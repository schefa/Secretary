<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\Utilities\Text;

// No direct access
defined('_JEXEC') or die;

class Search
{ 
    
    static function after ($thiss, $inthat)
    {
        if (!is_bool(strpos($inthat, $thiss)))
            return substr($inthat, strpos($inthat,$thiss)+strlen($thiss));
    }
    
    static function after_last ($thiss, $inthat)
    {
        if (!is_bool(self::strrevpos($inthat, $thiss)))
            return substr($inthat, self::strrevpos($inthat, $thiss)+strlen($thiss));
    }
    
    static function before ($thiss, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $thiss));
    }
    
    static function before_last ($thiss, $inthat)
    {
        return substr($inthat, 0, self::strrevpos($inthat, $thiss));
    }
    
    static function between ($thiss, $that, $inthat)
    {
        return self::before ($that, self::after($thiss, $inthat));
    }
    
    static function between_last ($thiss, $that, $inthat)
    {
        return self::after_last($thiss, self::before_last($that, $inthat));
    }
    
    static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos (strrev($instr), strrev($needle));
        if ($rev_pos===false) return false;
        else return strlen($instr) - $rev_pos - strlen($needle);
    }
         
}