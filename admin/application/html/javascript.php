<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\HTML;

use JText;

require_once JPATH_ADMINISTRATOR .'/components/com_secretary/application/HTML.php';
 
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
