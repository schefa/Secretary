<?php

namespace Secretary;

defined('_JEXEC') or die;

class Debug
{

    /**
     * Debug
     */
    public static function _($array, $die = false)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        
        if ($die)
        {
            die;
        }
    }
}