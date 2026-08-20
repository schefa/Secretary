<?php

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
            self::$user = Factory::getUser();
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