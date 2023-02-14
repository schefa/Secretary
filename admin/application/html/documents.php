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

// No direct access
defined('_JEXEC') or die;

class Documents
{
    /**
     * Method to get a summary of documents depending on their status
     * 
     * @param array $data
     * @param int $totalData
     * @return string HTML
     */
    public static function summary($data, $totalData)
    {

        $sum = array();
        $html = array();

        for ($i = 0; $i < $totalData; $i++) {
            $lastCurrency = ($i > 0) ? $data[$i - 1]->currency : '';
            $currentCurrency = $data[$i]->currency;
            $nextCurrency = (isset($data[$i + 1]->currency)) ? $data[$i + 1]->currency : 0;
            if (!isset($sum[$currentCurrency])) $sum[$currentCurrency] = array();
            $sum[$currentCurrency][] = $data[$i]->total;

            $html[] = JText::_($data[$i]->status_title) . ': ';
            $html[] = '<span class="brutto-' . $data[$i]->class . ' pull-right">';
            $html[] .= \Secretary\Utilities\Number::getNumberFormat($data[$i]->total, $data[$i]->currencySymbol);
            $html[] .= '</span><br>';

            if (($totalData - 1) == $i || $currentCurrency !== $nextCurrency) {

                if (count($sum[$currentCurrency] ?? []) > 1 && ($currentCurrency !==  $nextCurrency)) {
                    $html[] = '<h4 class="text-right">';
                    $html[] = \Secretary\Utilities\Number::getNumberFormat(array_sum($sum[$currentCurrency]), $data[$i - 1]->currencySymbol);
                    $html[] = '</h4>';
                }

                if (($totalData - 1) !== $i)
                    $html[] = '<div class="sidebar-split"></div>';
            }
        }

        return implode('', $html);
    }
}
