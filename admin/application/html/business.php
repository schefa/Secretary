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

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

use JText;
use JUri;

// No direct access
defined('_JEXEC') or die;

class Business
{
    /**
     * Method to display selected categories for a company view
     * 
     * @param array $data
     * @param int $totalData
     * @return string HTML
     */
    public static function selectedCategories($categories)
    {
        $html = array();
        if (!empty($categories)) {
            $html[] = '<ul class="company_selected_categories">';
            for ($i = 0; $i < count($categories ?? []); $i++) {
                $category = \Secretary\Database::getQuery('folders', $categories[$i]);
                if (isset($category->title)) $html[] = '<li>' . JText::_($category->title) . '</li>';
            }
            $html[] = '</ul>';
        }
        return implode('', $html);
    }

    /**
     * Method to display start message when no company exists
     * 
     * @return string
     */
    public static function startBusiness()
    {

        $html = array();
        $html[] = '<div class="businesses-start">';
        $html[] = '<h1>' . JText::_('COM_SECRETARY_BUSINESS_WELCOME') . '</h1>';

        $html[] = '<a href="' . \Secretary\Route::create('index.php?option=com_secretary&task=business.edit') . '" class="btn-large btn btn-success">' . JText::_('COM_SECRETARY_BUSINESS_STARTBUSINESS') . '</a>';

        $html[] = '<div class="secretary-install-or">' . JText::_('COM_SECRETARY_OR') . '</div><a href="' . \Secretary\Route::create('index.php?option=com_secretary&task=business.csample') . '" class="btn btn-large btn-default">' . JText::_('COM_SECRETARY_INSTALL_SAMPLE_DATA') . '</a>';
        $html[] = '<div class="secretary-install-desc">' . JText::_('COM_SECRETARY_BUSINESS_STARTBUSINESS_DESC') . '</div>';

        $html[] = '<div class="fullwidth"><img class="secretary-start-logo" src="' . SECRETARY_MEDIA_PATH . '/images/secretary_medium_logo.png" /></div>';
        $html[] = ' </div>';

        return implode("\n", $html);
    }
}
