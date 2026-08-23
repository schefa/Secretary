<?php

namespace Secretary;


defined('_JEXEC') or die;

class Utilities
{

    /**
     * Escape string before storing in database
     */
    public static function cleaner($str, $back = FALSE)
    {
        if ($str === null)
		{
            $str = '';
        }

        if ($back)
		{
            $str = stripslashes(html_entity_decode($str, ENT_QUOTES));
        }
        else
		{
            $str = addslashes(htmlentities($str, ENT_QUOTES));
        }
        
        return $str;
    }

    /**
     * Get the gender by a shortcut
     */
    public static function getGender($genderkey = false)
    {
        \Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
        $gender = \Joomla\CMS\Form\FormHelper::loadFieldType('gender', false)->getOptions($genderkey);
        
        return trim($gender);
    }
}