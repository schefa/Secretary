<?php

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';


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
        
        if (!empty($categories))
		{
            $html[] = '<ul class="company_selected_categories">';
            
            for ($i = 0; $i < count($categories ?? []); $i++)
			{
                $category = \Secretary\Database::getQuery('folders', $categories[$i]);
                
                if (isset($category->title))
                {
                	$html[] = '<li>' . \Joomla\CMS\Language\Text::_($category->title) . '</li>';
                }
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
        $html[] = '<div class="fullwidth"><img class="secretary-start-logo" src="' . SECRETARY_MEDIA_PATH . '/images/secretary_medium_logo.png" /></div>';
        $html[] = '<h1>' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS_WELCOME') . '</h1>';

        $html[] = '<a href="' . \Secretary\Route::create('index.php?option=com_secretary&task=business.edit') . '" class="btn-large btn btn-success">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS_STARTBUSINESS') . '</a>';

        $html[] = '<div class="secretary-install-or">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_OR') . '</div><a href="' . \Secretary\Route::create('index.php?option=com_secretary&task=business.csample') . '&amp;' . \Joomla\CMS\Session\Session::getFormToken() . '=1" class="btn btn-large btn-default">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_INSTALL_SAMPLE_DATA') . '</a>';
        $html[] = '<div class="secretary-install-desc">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS_STARTBUSINESS_DESC') . '</div>';

        $html[] = ' </div>';

        return implode("\n", $html);
    }
}
