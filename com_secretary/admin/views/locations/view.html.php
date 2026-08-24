<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewLocations extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $canDo;
    protected $categories;
    protected $categoryId;
    protected $extension;
    protected $items;
    protected $pagination;
    protected $state;
    protected $states;
    protected $title;
    protected $view;

    /**
     * Method to display the View
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\View\HtmlView::display()
     */
	public function display($tpl = null)
	{
	    $jinput				= Secretary\Joomla::getApplication()->input;
		$this->view			= $jinput->getCmd('view', 'locations');
		$this->extension	= $jinput->getCmd('extension');
		$this->categoryId	= $jinput->getInt('catid');
		
		$this->title		= (!empty($this->extension)) ? \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS_'.strtoupper($this->extension)) : \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		
		// Permission
		if ( !$this->canDo->get('core.show'))
		{
		    echo '<div class="alert alert-danger">'. \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR').'</div>';
		    
            return false;
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
		    throw new Exception(implode("\n", $errors));
		}
		
		$this->categories	= \Joomla\CMS\Form\FormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->sectionIcons = \Joomla\CMS\Form\FormHelper::loadFieldType('SecretarySections', false)->getIcons();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		if ($this->canDo->get('core.create'))
		{
			$addEventText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION');
			echo Secretary\Navigation::ToolbarItem('location.add', \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry' );
		}

		if ($this->canDo->get('core.edit'))
		{
		    echo '<button data-joomla-dialog=\'{"popupType": "inline", "src": "#secretary-batch-dialog"}\' type="button" class="btn btn-small">
						<span class="fa fa-database" title=\"'.\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'\"></span>'.
		    \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'</button>';
		}
		
		if ($this->canDo->get('core.edit') && $this->canDo->get('core.delete') && isset($this->items[0]))
		{
			echo Secretary\Navigation::ToolbarItem('locations.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
	} 
}
