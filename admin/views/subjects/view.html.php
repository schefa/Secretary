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

JFormHelper::addFieldPath(JPATH_SITE . '/administrator/components/com_secretary/models/fields');

class SecretaryViewSubjects extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app			= Secretary\Joomla::getApplication();
		$this->view		= $app->input->getCmd('view');
		$this->zip		= $app->input->getVar('zip');
		$this->catid	= $app->input->getInt('catid');
		
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);

		$this->selectedColumns = Secretary\Session::getColumns('contacts_columns',\Secretary\Helpers\Subjects::$selectedColumns);
        $this->acceptedColumns = array_filter($this->selectedColumns, function($val){ return ($val === true); });
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->contact_locations	= $this->get('StatsLocation');

		if ( !$this->canDo->get('core.show')) { 
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) { 
			$app->enqueueMessage( implode("\n", $errors) , 'error');
			return false;
		}
		
		$this->newsletter	= JFormHelper::loadFieldType('Categories', false)->getNewsletter();
		$this->states		= JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->folders		= JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->templates	= JFormHelper::loadFieldType('Templates', false)->getOptions(array( $this->view ));
		$this->getJS();
		
		parent::display($tpl);
	} 
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html	= array();

		if ($this->canDo->get('core.create')) {
			$addEventText = JText::_('COM_SECRETARY_SUBJECT');
			$html[] = Secretary\Navigation::ToolbarItem('subject.add', JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry' );
		
			if ($this->canDo->get('core.edit'))
			{
				$html[] = '<button class="btn btn-default hasTooltip" onclick="Joomla.submitbutton(\'subjects.importUsers\')" title="'.JText::_("COM_SECRETARY_SUBJECTS_IMPORT_DESC").'">
		<span class="fa fa-arrow-circle-o-down"></span>'. JText::_("COM_SECRETARY_SUBJECTS_IMPORT") . '</button>';
			}
			 
			if ($this->canDo->get('core.edit'))
			{
			    $title = JText::sprintf('COM_SECRETARY_CREATE_THIS', JText::_('COM_SECRETARY_DOCUMENT'));
			    $html[] = Secretary\Navigation::ToolbarItem('subjects.addDocuments',  $title, true, 'btn btn-default', 'fa-file');
			}
		}
		
		// Batch button
		if (isset($this->items[0]) && $this->canDo->get('core.edit') && $this->canDo->get('core.edit.state'))
		{
			$title = JText::_('COM_SECRETARY_TOOLBAR_BATCH');
			$html[] = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\"><span class=\"fa fa-database\" title=\"". $title ."\"></span>". $title ."</button>";
		}
		
		if ($this->canDo->get('core.edit.state')) {
            if ($this->canDo->get('core.delete')) {
				$html[] = Secretary\Navigation::ToolbarItem('subjects.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
			}
            if (isset($this->items[0]->checked_out)) {
				$html[] = Secretary\Navigation::ToolbarItem('subjects.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
            }
		}
		if(!empty($html))
			array_unshift($html, '<div class="select-arrow-toolbar-next">&#10095;</div>');
		
		echo implode("\n", $html);
	}
     
	protected function getJS()
	{
		$document = JFactory::getDocument();
		
		$document->addScriptDeclaration('
		
			jQuery(document).ready(function($){
				$("#subjects_category").change(function(){
					var value = $(this).val();
					$("#subjects_catID").val(value);
					$("form").get(0).setAttribute("action", "index.php?option=com_secretary&view=subjects&catid="+value); 
					document.adminForm.submit();
				});
			});
		
			Joomla.orderTable = function() {
				table = document.getElementById("sortTable");
				direction = document.getElementById("directionTable");
				order = table.options[table.selectedIndex].value;
				if (order != "'. $this->state->get('list.ordering') .'") {
					dirn = "asc";
				} else {
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, "");
			}
		');
	}

	protected function getSortFields()
	{
	    $fields = array(
	        'a.firstname' => JText::_('COM_SECRETARY_FIRSTNAME'),
	        'a.lastname' => JText::_('COM_SECRETARY_NACHNAME'),
	    );
	    foreach($this->acceptedColumns as $key => $t) {
	        $fields['a.'.$key] = JText::_('COM_SECRETARY_'.$key);
	    } 
	    return $fields;
	}
	
}
