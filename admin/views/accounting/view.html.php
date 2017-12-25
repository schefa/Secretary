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

class SecretaryViewAccounting extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $canDo;
	protected $extension;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		
		$jinput			= Secretary\Joomla::getApplication()->input;
		$this->extension= $jinput->getCmd('extension','accounting');
		
		$section	= 'accounting';
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);
		$check			= \Secretary\Helpers\Access::edit($section, $this->item->id );
		if( !$check ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

		$this->getJS();
		
		if($this->extension == 'accounting') 
			$this->title =  JText::_('COM_SECRETARY_ACCOUNTING_ENTRY');
		else 
			$this->title =  JText::_('COM_SECRETARY_'.strtoupper($this->extension));
			
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }

		// If not checked out, can save the item.
		if (!$checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('accounting.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('accounting.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$checkedOut && ($this->canDo->get('core.create'))){
			echo Secretary\Navigation::ToolbarItem('accounting.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('accounting.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		
		echo Secretary\Navigation::ToolbarItem('accounting.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		
	}
	
	protected function getJS()
	{
	    $document = JFactory::getDocument();
	    $document->addScript(SECRETARY_MEDIA_PATH .'/js/secretary.accounting.js?v='.SECRETARY_VERSION);
	    $document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','accounting'));
	}
}
