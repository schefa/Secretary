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

namespace Secretary;

use JFormHelper;

// No direct access
defined('_JEXEC') or die;

class Utilities
{

    /**
     * Escape string before storing in database
     */
    public static function cleaner($str, $back = FALSE)
    {
        if ($back) {
            $str = stripslashes(html_entity_decode($str, ENT_QUOTES));
        } else {
            $str = addslashes(htmlentities($str, ENT_QUOTES));
        }
        return $str;
    }

    /**
     * Get the gender by a shortcut
     */
    public static function getGender($genderkey = false)
    {
        JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
        $gender = JFormHelper::loadFieldType('gender', false)->getOptions($genderkey);
        return trim($gender);
    }

    /**
     * Reorder a list of items 
     */
    public static function reorderTree(array $items, $parentAttribute = 'refer_to', $parentID = 'id')
    {
        $length = count($items ?? []) - 1;
        for ($parent_idx = $length; $parent_idx >= 0; $parent_idx--) {
            for ($child_idx = $length; $child_idx >= 0; $child_idx--) {
                if ($items[$child_idx]->$parentAttribute === $items[$parent_idx]->$parentID) {
                    if (!isset($items[$child_idx]->step)) {
                        $items[$child_idx]->step = 1;
                    } else
                        $items[$child_idx]->step++;
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