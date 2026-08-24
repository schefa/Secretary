<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\HTML;

defined('_JEXEC') or die;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

class Javascript
{

    /**
     * Get additional javascript for forms
     * 
     * @param string $view
     * @return string
     */
    public static function submitformbutton($view)
    {
        $extension = \Secretary\Application::getSingularSection($view);
        $html = "
        Joomla.submitbutton = function(task)
        {
            if (task == '" . $extension . ".cancel') {
                Joomla.submitform(task, document.getElementById('adminForm'));
            } else {
                if (task != '" . $extension . ".cancel' && document.formvalidator.isValid(document.getElementById('adminForm'))) {
                    Joomla.submitform(task, document.getElementById('adminForm'));
                } else {
                    alert('" . \Secretary\Utilities::cleaner(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED')) . "');
                }
            }
        }
        ";
        
        return $html;
    }
}
