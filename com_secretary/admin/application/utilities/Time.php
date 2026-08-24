<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\Utilities;


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

        if ($etime < 1)
		{
            return '0 seconds';
        }

        $a = array(
            365 * 24 * 60 * 60 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_YEAR'),
            30 * 24 * 60 * 60 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_MONTH'),
            24 * 60 * 60 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_DAY'),
            60 * 60 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_HOUR'),
            60 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_MINUTE'),
            1 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_SECOND')
        );
        $a_plural = array(
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_YEAR') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_YEARS'),
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_MONTH') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_MONTHS'),
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_DAY') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_DAYS'),
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_HOUR') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_HOURS'),
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_MINUTE') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_MINUTES'),
            \Joomla\CMS\Language\Text::_('COM_SECRETARY_SECOND') => \Joomla\CMS\Language\Text::_('COM_SECRETARY_SECONDS')
        );

        foreach ($a as $secs => $str)
		{
            $d = $etime / $secs;
            
            if ($d >= 1)
			{
                $r = round($d);
                
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            }
        }
    }
}