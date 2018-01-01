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