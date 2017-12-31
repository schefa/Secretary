<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
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
