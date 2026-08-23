<?php

namespace Secretary\Utilities\Text;

defined('_JEXEC') or die;

class Search
{

    /**
     * These helpers are handed record fields that are simply unset on a new
     * entry. Up to PHP 8.0 null behaved like an empty string in strpos()/substr();
     * since 8.1 it is deprecated, so normalise once at the entry points below.
     */
    private static function str($value)
    {
        return $value ?? '';
    }

    static function after($thiss, $inthat)
    {
        $thiss  = self::str($thiss);
        $inthat = self::str($inthat);
        
        if (!is_bool(strpos($inthat, $thiss)))
        {
            return substr($inthat, strpos($inthat, $thiss) + strlen($thiss));
        }
    }

    static function before($thiss, $inthat)
    {
        $thiss  = self::str($thiss);
        $inthat = self::str($inthat);
        
        return substr($inthat, 0, strpos($inthat, $thiss));
    }

    static function between($thiss, $that, $inthat)
    {
        return self::before($that, self::after($thiss, $inthat));
    }
}