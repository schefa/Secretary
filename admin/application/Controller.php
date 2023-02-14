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

require_once SECRETARY_ADMIN_PATH . '/application/ControllerAdmin.php';

// No direct access
defined('_JEXEC') or die;

class Controller
{
    public static function checkin($table, $ids = array())
    {
        // only secretary table
        if (!in_array($table, Database::$secretary_tables)) {
            throw new Exception('Query failure: ' . $table);
            return false;
        }

        $return = false;
        if (!empty($ids)) {
            $db = \Secretary\Database::getDBO();
            $query = $db->getQuery(true);
            $query->update($db->qn('#__secretary_' . $table))
                ->set('checked_out=0')
                ->set('checked_out_time=' . $db->quote('0000-00-00 00:00:00'))
                ->where('id in (' . implode(',', $ids) . ')');
            $db->setQuery($query);
            $return = $db->execute();
        }
        return $return;
    }
}