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

    /**
     * Reorder a list of items 
     */
    public static function reorderTree(array $items, $parentAttribute = 'refer_to', $parentID = 'id')
    {
        $length = count($items ?? []) - 1;
        
        for ($parent_idx = $length; $parent_idx >= 0; $parent_idx--)
		{
            for ($child_idx = $length; $child_idx >= 0; $child_idx--)
			{
                if ($items[$child_idx]->$parentAttribute === $items[$parent_idx]->$parentID)
				{
                    if (!isset($items[$child_idx]->step))
					{
                        $items[$child_idx]->step = 1;
                    }
                    else
                    {
                        $items[$child_idx]->step++;
                    }
                    // new position for insert
                    $newPosition = ($child_idx < $parent_idx) ? $parent_idx : $parent_idx + 1;
                    // Move element in Array
                    $out = array_splice($items, $child_idx, 1);
                    array_splice($items, $newPosition, 0, $out);
                }
            }
        }
        
        return $items;
    }
}