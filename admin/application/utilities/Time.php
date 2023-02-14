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

namespace Secretary\Utilities;

use JText;

// No direct access
defined('_JEXEC') or die;

class Time
{

    /**
     * Method to time that has passed by since beginning
     * 
     * @param int $start
     * @return string
     */
    public static function elapse($start)
    {
        $etime = time() - $start;

        if ($etime < 1) {
            return '0 seconds';
        }

        $a = array(
            365 * 24 * 60 * 60 => JText::_('COM_SECRETARY_YEAR'),
            30 * 24 * 60 * 60 => JText::_('COM_SECRETARY_MONTH'),
            24 * 60 * 60 => JText::_('COM_SECRETARY_DAY'),
            60 * 60 => JText::_('COM_SECRETARY_HOUR'),
            60 => JText::_('COM_SECRETARY_MINUTE'),
            1 => JText::_('COM_SECRETARY_SECOND')
        );
        $a_plural = array(
            JText::_('COM_SECRETARY_YEAR') => JText::_('COM_SECRETARY_YEARS'),
            JText::_('COM_SECRETARY_MONTH') => JText::_('COM_SECRETARY_MONTHS'),
            JText::_('COM_SECRETARY_DAY') => JText::_('COM_SECRETARY_DAYS'),
            JText::_('COM_SECRETARY_HOUR') => JText::_('COM_SECRETARY_HOURS'),
            JText::_('COM_SECRETARY_MINUTE') => JText::_('COM_SECRETARY_MINUTES'),
            JText::_('COM_SECRETARY_SECOND') => JText::_('COM_SECRETARY_SECONDS')
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            }
        }
    }
}