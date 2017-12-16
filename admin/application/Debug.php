<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die;

class Debug
{
    
    /**
     * Debug
     */
    public static function _($array, $die = false) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        if($die) die;
    }
    
}
