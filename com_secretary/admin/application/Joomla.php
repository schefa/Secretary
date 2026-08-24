<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class Joomla
{
    private static $app;
    private static $user;

    /**
     * Joomla User Interface
     */
    public static function getUser()
    {
        if (!isset(self::$user))
		{
            self::$user = Factory::getApplication()->getIdentity();
        }
        
        return self::$user;
    }

    /**
     * Joomla Application Interface
     */
    public static function getApplication($mode = 'administrator')
    {
        if (!isset(self::$app))
		{
            self::$app = Factory::getApplication($mode);
        }
        
        return self::$app;
    }

    /**
     * Joomla Cache Interface
     */
    public static function getCache()
    {
        return Factory::getCache('com_secretary', '');
    }
}