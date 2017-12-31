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

class SecretaryViewDocuments extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $view;
	protected $task;
	protected $categoryId;
	protected $itemsFilter = 0;
	
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
		
		$this->view			= $app->input->getCmd('view');
		$this->task			= $app->input->getVar('task');
		$this->categoryId	= $app->input->getInt('catid', 0);
		$this->locationId	= $app->input->getInt('location',0);
		$this->currencyId	= $app->input->getVar('currency');
		
		$model = $this->getModel();
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->itemsExpired	= $this->get('ItemsExpired');
		$this->report		= $this->get('Summary');
		$this->pagination	= $this->get('Pagination');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		$this->itemsRepeat  = \Secretary\Helpers\Times::getLookAfterRepetitions();
		
		$this->itemsFilter  = $model->itemsFilter;
		$this->maxValue     = $model->maxValue;
		
		// Permission
		if ( !$this->canDo->get('core.show') && $this->itemsFilter == 0) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>';
		    return false;
		} elseif (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		$this->locations	= JFormHelper::loadFieldType('Locations', false)->getLocations( $this->view );
		$this->categories	= JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= JFormHelper::loadFieldType('SecretaryStatus', false)->getOptions( $this->view );
		$this->templates	= JFormHelper::loadFieldType('Templates', false)->getOptions(array( $this->view ));
			
		if(!empty($this->task) && $this->task == 'showRepetitions') {
			$this->setLayout('default_repetition');	
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
		
		$this->document->setTitle('Secretary - '. $this->report['title']);
		
		$addEntryText = JText::_('COM_SECRETARY_DOCUMENT');
		if(!empty($this->report['alias'])) {
			$addEntryText = JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $this->report['alias']);
		} else {
			$addEntryText = JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $addEntryText);
		}
		
		if ($this->canDo->get('core.create')) {
			$html[] = Secretary\Navigation::ToolbarItem('document.add', $addEntryText, false, 'newentry');
		}
		
        // Stapel
		if (isset($this->items[0]) && $this->canDo->get('core.edit') && $this->canDo->get('core.edit.state'))
		{
			$title = JText::_('COM_SECRETARY_TOOLBAR_BATCH');
			$html[] = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
						<span class=\"fa fa-database\" title=\"". $title ."\"></span>". $title ."</button>";
			$html[] = Secretary\Navigation::ToolbarItem('documents.updateProducts', 'COM_SECRETARY_PRODUCT_INFORMATION_UPDATE', true, 'default hidden-toolbar-btn', 'fa-refresh'); 
		}
		
		if ($this->canDo->get('core.edit.state')) {

            if ($this->canDo->get('core.delete')) {
				$html[] = Secretary\Navigation::ToolbarItem('documents.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
            }
			
            if (isset($this->items[0]->checked_out)) {
				$html[] = Secretary\Navigation::ToolbarItem('documents.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
            }
		}
		
		if(!empty($html)) {
			array_unshift($html, '<div class="select-arrow-toolbar-next">&#10095;</div>');
		}
		
		echo implode("\n", $html);
	}
	
	protected function getSortFields()
	{
		return array(
			'a.nr' => JText::_('COM_SECRETARY_NR'),
			'a.created' => JText::_('COM_SECRETARY_DATE'),
			'contact_name' => JText::_('COM_SECRETARY_SUBJECT'),
			'a.catid' => JText::_('COM_SECRETARY_FOLDER'),
			'a.state' => JText::_('JSTATUS'),
			'a.total' => JText::_('COM_SECRETARY_TOTAL')
		);
	}
	
	protected function getJS()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("
		jQuery(document).ready(function($){
		    
			$('.reloadSite').on('change',function(){
		     
    		    var catid         = $('#documents_category').val();
    		    var locationID    = $('#documents_office').val();
    		    var currencyID    = $('#documents_currency').val();
    		    
				$('#documents_catID').val(catid);
				$('#documents_locationID').val(locationID);
				$('#documents_currencyID').val(currencyID);
		    
				$('form').get(0).setAttribute('action', 'index.php?option=com_secretary&view=documents&location='+locationID+'&catid='+catid+'&currency='+currencyID); 
				this.form.submit();
			});
		    
		});
		");
	}
}
