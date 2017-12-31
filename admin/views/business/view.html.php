<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewBusiness extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $jinput			= \Secretary\Joomla::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$this->layout	= $jinput->getCmd('layout');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);
		
		// Permission
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id );
		if( $this->layout == 'edit' && !$check ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif( $this->layout != 'edit' && false === \Secretary\Helpers\Access::show($section, $this->item->id) ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors));
		    return false;
		}

        if (isset($this->item->checked_out)) {
            $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == \Secretary\Joomla::getUser()->get('id'));
        } else {
            $this->checkedOut = false;
        }
		
		$this->getJS();
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html	= array();
		
		if (!$this->checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create')))) {
			$html[] = Secretary\Navigation::ToolbarItem('business.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			$html[] = Secretary\Navigation::ToolbarItem('business.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		
		if (empty($this->item->id)) {
			$html[] = Secretary\Navigation::ToolbarItem('business.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		} else {
			$html[] = Secretary\Navigation::ToolbarItem('business.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

		echo implode("\n", $html);
	}
	
	protected function getJS()
	{
	    $document = JFactory::getDocument();
	    $document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','business'));
	}
	
}
