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

class SecretaryViewSubject extends JViewLegacy
{
	protected $business;
	protected $canDo; 
	protected $checkedOut;
	protected $form;
	protected $item;
	protected $state;
	protected $userprofile = 0;
	
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
		$this->layout	= $app->input->getCmd('layout');
		$this->catid	= $app->input->getInt('catid');

		$model = $this->getModel();
		$this->userprofile  = $model->userprofile;
		
		$user			= \Secretary\Joomla::getUser(); 
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		$this->business	= \Secretary\Application::company();
		
		if($this->layout === 'modal')
			$this->setLayout('edit');
    
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		
		// Permission
		$show = false;
		if( $this->layout == 'edit' && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by ) ) {
		    $show = true;
		} elseif($this->layout != 'edit' && true === \Secretary\Helpers\Access::show($this->view, $this->item->id, (int) $this->item->created_by) ) {
		    $show = true;
		}
		if($user->id > 0 && $this->userprofile > 0)
		    $show = true;
		elseif($user->id > 0 && $this->userprofile !== 0) {
		    echo '<div class="alert alert-danger">'. JText::_('COM_SECRETARY_PROFILE_NOT_FOUND').'</div>';
		    return false;
		}

		if(!$show) { 
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>';
		    return false;
		} elseif (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage( implode("\n", $errors) , 'error');
			return false;
		}
		
		// Existing contact
		if(!empty($this->item->id))
		{
			$model	= $this->getModel();
			
			require_once JPATH_ADMINISTRATOR.'/components/com_secretary/models/documents.php';
			$documentModel                 = new SecretaryModelDocuments(array('contact_ids'=>array($this->item->id)));
			$this->item->documents         = $documentModel->getItems();
			$this->item->documents_summary = $documentModel->getSummary();
			  
			$this->document_categories	= \Secretary\Helpers\Folders::getList("documents");
		    $this->myConnections = $model->getConnections($this->item->id);
		}

		$this->itemtemplates		=	JFormHelper::loadFieldType('templates', false)->getList( $this->item->template, 'jform[template]','', array('subjects'));
		if(isset($this->item->template) && $this->item->template > 0)
			$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template); 
		
 
        if (isset($this->item->checked_out)) {
		    $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
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
		// If not checked out, can save the item.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create') || $this->userprofile !== 0 )))
		{
			echo Secretary\Navigation::ToolbarItem('subject.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('subject.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		
		if($this->layout === 'edit' &&  $this->userprofile == 0 )
		{
			if (!$this->checkedOut && ($this->canDo->get('core.create'))){
				echo Secretary\Navigation::ToolbarItem('subject.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
			}
			// If an existing item, can save to a copy.
			if (!($this->item->id == 0) && ($this->canDo->get('core.create'))) {
				echo Secretary\Navigation::ToolbarItem('subject.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
			}
		}
		
		if (empty($this->item->id)) {
			echo Secretary\Navigation::ToolbarItem('subject.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		} else {
			echo Secretary\Navigation::ToolbarItem('subject.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		} 
	}

	protected function getJS()
	{
		$document = JFactory::getDocument(); 
		
		$contacts = array();
		if(!empty($this->myConnections) && ($this->layout == 'edit')) 
		{
			foreach($this->myConnections AS $object)
			{
			    $object->id = ($this->item->id != $object->one) ? $object->one : $object->two;
				$subject 	= Secretary\Database::getQuery('subjects',$object->id,'id',"firstname,lastname");
				if(!empty($subject)) {
					$object->firstname = $subject->firstname;
					$object->lastname = $subject->lastname;
					$contacts[] =   $object;
				}
			}
		}
		$this->contactsCounts = count($contacts);
		$contacts = json_encode( $contacts );
		
		$document->addScriptDeclaration("var featuresList = ". $contacts .";");
		
		if($this->layout == 'modal')
		{		
			$document->addScriptDeclaration('
				Joomla.submitbutton = function(task)
				{
					if (task == "subject.save" || task == "subject.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
					{
						if (window.opener && (task == "subject.save" || task == "subject.cancel"))
						{
							window.opener.document.closeEditWindow = self;
							window.opener.setTimeout("window.document.closeEditWindow.close()", 1000);
						}
			
						Joomla.submitform(task, document.getElementById("adminForm"));
					}
				}; 
			');
		} else {
		    $document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','subject'));
		}
	} 
}
