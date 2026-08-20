<?php

namespace Secretary\HTML;


require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

defined('_JEXEC') or die;

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
