<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');

class SecretaryViewLocations extends JViewLegacy
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
		
		$this->title		= (!empty($this->extension)) ? JText::_('COM_SECRETARY_LOCATIONS_'.strtoupper($this->extension)) : JText::_('COM_SECRETARY_LOCATIONS');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		
		// Permission
		if ( !$this->canDo->get('core.show')) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>';
		    return false;
		} elseif (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors));
		    return false;
		}
		
		$this->categories	= JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->sectionIcons = JFormHelper::loadFieldType('SecretarySections', false)->getIcons();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		if ($this->canDo->get('core.create')) {
			$addEventText = JText::_('COM_SECRETARY_LOCATION');
			echo Secretary\Navigation::ToolbarItem('location.add', JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry' );
		}

		if ($this->canDo->get('core.edit')) {
		    echo '<button data-toggle="modal" data-target="#collapseModal" class="btn btn-small">
						<span class="fa fa-database" title=\"'.JText::_('COM_SECRETARY_TOOLBAR_BATCH').'\"></span>'.
		    JText::_('COM_SECRETARY_TOOLBAR_BATCH').'</button>';
		}
		
		if ($this->canDo->get('core.edit') && $this->canDo->get('core.delete') && isset($this->items[0])) {
			echo Secretary\Navigation::ToolbarItem('locations.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
	} 
}
