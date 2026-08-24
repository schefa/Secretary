<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

defined('_JEXEC') or die;

class Route
{

    public static function create($view = '', $fields = array())
    {
        $url = 'index.php?option=com_secretary';

        if (!empty($view))
        {
            $url .= '&view=' . $view;
        }

        if (!empty($fields))
		{
            foreach ($fields as $key => $value)
			{
                if (!empty($key) || !empty($value))
				{
                    $url .= "&" . $key . "=" . $value;
                }
            }
        }

        return \Joomla\CMS\Router\Route::_($url, false);
    }

    public static function safeURL($string)
    {
        // Remove any '-' from the string since they will be used as concatenaters
        $str = str_replace('-', ' ', $string);

        $lang = Factory::getLanguage();
        $str = $lang->transliterate($str);

        // Trim white spaces at beginning and end of alias and make lowercase
        $str = trim(StringHelper::strtolower($str));

        // Remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

        // Trim dashes at beginning and end of alias
        $str = trim($str, '-');

        return $str;
    }
}