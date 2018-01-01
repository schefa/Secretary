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

use JText;

require_once SECRETARY_ADMIN_PATH .'/application/HTML.php';
 
// No direct access
defined('_JEXEC') or die;

class Javascript
{

    /**
     * Get additional javascript for forms
     * 
     * @param string $view
     * @return string
     */
    public static function submitformbutton($view) {
        $extension = \Secretary\Application::getSingularSection($view);
        $html = "
        Joomla.submitbutton = function(task)
        {
            if (task == '".$extension.".cancel') {
                Joomla.submitform(task, document.getElementById('adminForm'));
            } else {
                if (task != '".$extension.".cancel' && document.formvalidator.isValid(document.id('".$extension."-form'))) {
                    Joomla.submitform(task, document.getElementById('adminForm'));
                } else {
                    alert('". \Secretary\Utilities::cleaner(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')) ."');
                }
            }
        }
        ";
        return $html;
    }
}
