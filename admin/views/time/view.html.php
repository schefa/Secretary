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

class SecretaryViewTime extends JViewLegacy
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

	    $app	          = Secretary\Joomla::getApplication();
		$this->view		= 'time';
		
		$jinput			= $app->input;
		$this->layout	= $jinput->getCmd('layout');
		$this->extension= $jinput->getCmd('extension');
		
		$model			= $this->getModel('Time');
		$this->tableName= $model->tableName;
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
 
		// Permission
		$user = \Secretary\Joomla::getUser();
		$show = false;
		if( $this->layout == 'edit' && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by ) ) {
		    $show = true;
		} elseif($this->layout != 'edit' && true === \Secretary\Helpers\Access::show($this->view, $this->item->id, (int) $this->item->created_by) ) {
		    $show = true;
		}
		
		if( !$show) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		} elseif (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode("\n", $errors),'error'); return false;
		}
		
		switch ($this->extension) {
			case 'tasks':
				$this->title		= JText::_('COM_SECRETARY_TASK');
				$this->projects		= $model->getProjects($this->item->projectID);
				$this->projectTimer	= \Secretary\Helpers\Times::getProjectStatus($this->item->id, $this->item->projectID);
				break;	
				 
			default :
				$this->title	= JText::_('COM_SECRETARY_'.substr($this->extension,0,-1));
				break;
			
		}
	
        if (isset($this->item->checked_out)) {
		    $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $this->checkedOut = false;
        }
		
		$this->extension	= $model->extension;
		
		$this->document->setTitle('Secretary - '.$this->title);
		$this->business	= Secretary\Application::company();
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{ 
		$isNew		= ($this->item->id == 0);

		// If not checked out, can save the item.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('time.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('time.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$this->checkedOut && ($this->canDo->get('core.create'))){
			echo Secretary\Navigation::ToolbarItem('time.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('time.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		
		if (empty($this->item->id)) {
			echo Secretary\Navigation::ToolbarItem('time.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		} else {
			echo Secretary\Navigation::ToolbarItem('time.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

	}
	
	protected function getJS()
	{
		
		$contacts = array();
		
		$app 	= \Secretary\Joomla::getApplication();
		$layout	= $app->input->getCmd('layout');
		
		$user = \Secretary\Joomla::getUser();
		$userContact = ($user->id > 0) ? Secretary\Database::getQuery('subjects',$user->id,'created_by') : (object) array('id'=>-1);
		$this->userAttendee = false;
		
		if(!empty($this->item->contacts)) 
		{
			if($c = json_decode($this->item->contacts)) {
				
				foreach($c AS $object)
				{
					if(!empty($object) && is_object($object)) {
						if($this->extension == 'task') {
							$subject 	= Secretary\Database::getQuery('subjects', $key,'id',"firstname,lastname");
							if(!empty($subject)) {
								$contacts[$key] = $subject->firstname . " " . $subject->lastname ." : " . $value;
							}
						} else {
							$subject 	= Secretary\Database::getQuery('subjects',$object->id,'id',"firstname,lastname");
							if(!empty($subject)) {
								$object->firstname = $subject->firstname;
								$object->lastname = $subject->lastname;
								$contacts[] =   $object;
							}
						}
						if(is_object($userContact) && $object->id == $userContact->id)
							$this->userAttendee = true;
					}
					unset($subject);
				}
			} 
		}
		
		$this->contactsCounts = count($contacts);
		$this->contacts =  $contacts ;

		$document = JFactory::getDocument();
		$document->addScriptDeclaration("var featuresList = ". json_encode( $contacts ) .";");
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','time'));
	}
}
