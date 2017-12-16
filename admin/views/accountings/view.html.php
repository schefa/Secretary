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

class SecretaryViewAccountings extends JViewLegacy
{
	
	protected $accountId;
	protected $canDo;
	protected $extension;
	protected $items;
	protected $pagination;
	protected $view;
	protected $state;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app				= Secretary\Joomla::getApplication();
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		
		$this->extension	= $app->input->getCmd('extension','accounting');
		$this->accountId	= $app->input->getInt('account');
		$this->view			= $app->input->getCmd('view');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		if($this->extension == 'accounts_system') {
			$this->title = JText::_('COM_SECRETARY_ACCOUNTS_SYSTEM_STANDARD');
		} else {
			$this->title =  JText::_('COM_SECRETARY_'.strtoupper($this->extension));
		}
		
		$this->accounts		= JFormHelper::loadFieldType('Accounts', false)->getOptions( );
		$this->states		= JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( 'accounting' );
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html = array();
	
		if ($this->canDo->get('core.create')) {
			switch($this->extension) {
				default: case 'accounting': $addEventText = JText::_('COM_SECRETARY_ACCOUNTING_ENTRY'); break;
				case 'accounts': $addEventText = JText::_('COM_SECRETARY_KONTO'); break;
				case 'accounts_system': $addEventText = JText::_('COM_SECRETARY_KONTO'); break;
			}
			$html[] = Secretary\Navigation::ToolbarItem('accounting.add', JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry');
		}

		if (isset($this->items[0]->checked_out)) {
			$html[] = Secretary\Navigation::ToolbarItem('accountings.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
		}
		
		if($this->canDo->get('core.edit') && $this->extension == 'accounting') {
			$html[] = Secretary\Navigation::ToolbarItem('accountings.buchen', 'COM_SECRETARY_ACCOUNTING_BOOK', true, 'default', 'fa-book');
			$html[] = Secretary\Navigation::ToolbarItem('accountings.storno', 'COM_SECRETARY_ACCOUNTING_REVERSE', true, 'default', 'fa-ban');
		}
		
		if($this->extension == 'accounts_system' && $this->canDo->get('core.delete')) {
			$html[] = Secretary\Navigation::ToolbarItem('accountings.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash');
		}
		
		if(!empty($html) && $this->extension != 'accounts_system')
			array_unshift($html, '<div class="select-arrow-toolbar-next">&#10095;</div>');
		
		echo implode("\n", $html);
	} 
    
	protected function getJS()
	{
		$document = JFactory::getDocument();
		
		$document->addScriptDeclaration("
		
		jQuery(document).ready(function($){
			$('#documents_category').change(function(){
				var value = $(this).val();
				$('#documents_catID').val(value);
				$('form').get(0).setAttribute('action', 'index.php?option=com_secretary&view=accountings&account='+value); 
				this.form.submit();
			});
		});
		
		Joomla.orderTable = function() {
				table = document.getElementById('sortTable');
				direction = document.getElementById('directionTable');
				order = table.options[table.selectedIndex].value;
				if (order != '". $this->state->get('list.ordering') ."') {
					dirn = 'asc';
				} else {
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, '');
			}
		");
	}
    
    
	protected function getSortFields()
	{
		switch($this->extension) {
			case 'accounting' : 
				return array(
					'a.id' => JText::_('JGRID_HEADING_ID'),
					'a.created' => JText::_('COM_SECRETARY_CREATED'),
					'a.total' => JText::_('COM_SECRETARY_TOTAL'),
					'a.ordering' => JText::_('COM_SECRETARY_TOTAL'),
				);
				break;
			case 'accounts' : 
				return array(
					'a.id' => JText::_('JGRID_HEADING_ID'),
					'a.budget' => JText::_('COM_SECRETARY_TOTAL'),
				);
				break;
			case 'accounts_system' : 
				return array(
					'a.id' => JText::_('JGRID_HEADING_ID'),
					'a.ordering' => JText::_('COM_SECRETARY_TOTAL'),
				);
				break;
		}	
	}

    
}
