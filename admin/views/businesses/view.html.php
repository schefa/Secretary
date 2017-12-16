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

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');

class SecretaryViewBusinesses extends JViewLegacy
{
	protected $canDo;
	protected $items;
	protected $pagination;
	protected $state;
	protected $states;
	protected $view;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app		        = \Secretary\Joomla::getApplication();
		$this->view         = $app->input->getCmd('view');
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->states		= JFormHelper::loadFieldType('Secretarystatus', false)->getOptions('root'); 

		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		if ( !$this->canDo->get('core.show')) {
			throw new Exception( JText::_('JERROR_ALERTNOAUTHOR') , 500);
			return false;
		} elseif (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors));
		    return false;
		}
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html = array(); 
		
		if ($this->canDo->get('core.create')) {
			$addEventText = JText::_('COM_SECRETARY_BUSINESS');
			$html[] = Secretary\Navigation::ToolbarItem('business.add', JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry' );
		}
		
		if(!empty($this->items[0]) && JFactory::getUser()->authorise('com_secretary','core.admin')) {
			$html[] = Secretary\Navigation::ToolbarItem('businesses.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash');
			$html[] = Secretary\Navigation::ToolbarItem('businesses.setDefault', 'COM_SECRETARY_TOOLBAR_SET_HOME', true, 'default', 'fa-star');
		}
		
		echo implode("\n", $html);
	}
	
}
