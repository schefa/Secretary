<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JFormHelper::addFieldPath(JPATH_SITE . '/administrator/components/com_secretary/models/fields');

class SecretaryViewFolders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $canDo;
	protected $extension;
	protected $states;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app	= Secretary\Joomla::getApplication();
        $user	= Secretary\Joomla::getUser();
		
		if(empty($app->input->getCmd('extension'))) {
		    $app->redirect(Secretary\Route::create('folders', array('extension'=>'documents')));
		}
		
		$this->view			= $app->input->getCmd('view');
		$this->extension	= $app->input->getCmd('extension');
		
		// Access
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		if (!$this->canDo->get('core.show') || !$user->authorise('core.show','com_secretary.'.substr($this->extension,0,-1))) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		}
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->states		= $this->getStates();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(404, implode("\n", $errors)); return false;
		}
		
		$this->items	= \Secretary\Helpers\Folders::reorderFolderItems( $this->items );
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$user		= JFactory::getUser();
		$html		= array();
		 
		$title = (isset($this->extension)) ? JText::_('COM_SECRETARY_CATEGORIES_'.strtoupper($this->extension)) : JText::_('COM_SECRETARY_CATEGORIES');
		$addEntryText = JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $title);
		
		
		// Prepare the toolbar
		if ($this->canDo->get('core.create') && $user->authorise('core.create','com_secretary.'.substr($this->extension,0,-1))) {
			$html[] = Secretary\Navigation::ToolbarItem('folder.add', $addEntryText, false, 'newentry', 'fa-plus');
		}
		
        // Stapel
		if (isset($this->items[0]) && $this->canDo->get('core.edit')) {
			$html[] = '<button data-toggle="modal" data-target="#collapseModal" class="btn btn-small">
						<span class="fa fa-database" title=\"'.JText::_('COM_SECRETARY_TOOLBAR_BATCH').'\"></span>'.
			JText::_('COM_SECRETARY_TOOLBAR_BATCH').'</button>';
		}
		
		if ($this->canDo->get('core.delete')) {
			$html[] = Secretary\Navigation::ToolbarItem('folders.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
		
		if ($this->canDo->get('core.admin')) {
			$html[] = Secretary\Navigation::ToolbarItem('folders.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
		}
		
		echo implode("\n", $html);
	}
	
	private function getStates()
	{
		$states = JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		return $states;
	}
	
	protected function getSortFields()
	{
		return array(
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE')
		);
	}
}