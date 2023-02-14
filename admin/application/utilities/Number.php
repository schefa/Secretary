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

namespace Secretary\Utilities;

// No direct access
defined('_JEXEC') or die;

class Number
{
    private static $_numberformat = array();

    /**
     *  Formats a number into money
     */
    public static function getNumberFormat($value, $currencySymbol = NULL)
    {
        if (empty(self::$_numberformat['' . $value . '_' . $currencySymbol])) {
            $params = \Secretary\Application::parameters();
            $numberformat = $params->get('numberformat', 0);
            $currencyformat = (int) $params->get('currencyformat', 0);

            $result = 0;
            if (intval($numberformat) === 0) {
                $result = number_format(floatval($value), 2, '.', ',');
            } elseif (intval($numberformat) === 1) {
                $result = number_format(floatval($value), 2, '.', '');
            } elseif (intval($numberformat) === 2) {
                $result = number_format(floatval($value), 2, ',', '.');
            } elseif (intval($numberformat) === 3) {
                $result = number_format(floatval($value), 2, ',', '');
            }

            if (!is_null($currencySymbol)) {
                switch ($currencyformat) {
                    default:
                    case 0:
                        $result = $result . ' ' . $currencySymbol;
                        break;
                    case 1:
                        $result = $result . $currencySymbol;
                        break;
                    case 2:
                        $result = $currencySymbol . ' ' . $result;
                        break;
                    case 3:
                        $result = $currencySymbol . $result;
                        break;
                }
            }

            self::$_numberformat['' . $value . '_' . $currencySymbol] = $result;
        }
        return self::$_numberformat['' . $value . '_' . $currencySymbol];
    }

    public static function transformAmount($value, $type = 'get', $currency = NULL, $null = NULL)
    {
        switch ($type) {
            default:
            case 'get':
                if ($value == 0) {
                    $result = 0;
                } else {
                    $result = number_format($value, 2, ',', '.');
                }
                break;
            case 'set':
                $result = number_format($value, 2, ',', '.');
                break;
        }
        if ($currency)
            $result .= ' ' . $currency;
        if (($value == 0) && isset($null)) {
            $result = $null;
        }

        return $result;
    }

    /**
     * Method to translate the bytes into something more readable
     */
    public static function human_filesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    /**
     * Method to get the bytes of an input value
     */
    public static function getBytes($value)
    {
        if (empty($value))
            return 0;

        $value = trim($value);

        preg_match('#([0-9]+)[\s]*([a-z]+)#i', $value, $matches);

        $last = '';
        if (isset($matches[2])) {
            $last = $matches[2];
        }

        if (isset($matches[1])) {
            $value = (int) $matches[1];
        }

        switch (strtolower($last)) {
            case 'g':
            case 'gb':
                $value *= 1024;
            case 'm':
            case 'mb':
                $value *= 1024;
            case 'k':
            case 'kb':
                $value *= 1024;
        }

        return (int) $value;
    }
}