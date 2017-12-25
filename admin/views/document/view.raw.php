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

class SecretaryViewDocument extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $document_title;
	protected $productUsage;
	protected $genderoptions;
	protected $entityoptions;
	protected $productUsageOption;
	protected $itemtemplates;
	protected $emailTemplate;
	protected $checkedOut;
	protected $info;
	protected $fields;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$jinput			= JFactory::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$layout			= $jinput->getCmd('layout');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);
		
		// Permission
		$user = JFactory::getUser();
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by );
		
		$show = false;
		if( $layout == "edit" && true === $check) {
		    $show = true;
		} elseif($layout != "edit") {
		    $subjectUserId = Secretary\Database::getQuery('subjects', $this->item->subjectid,'id', 'created_by','loadResult');
		    if(false !== \Secretary\Helpers\Access::show($section,$this->item->id,$this->item->created_by))
		        $show = true;
		        if(false !== \Secretary\Helpers\Access::show($section,$this->item->id,$subjectUserId))
		            $show = true;
		}
		
		if( !$show) {
		    throw new Exception( JText::_('JERROR_ALERTNOAUTHOR'),500); return false;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception( implode("\n", $errors), 404); return false;
		}
		
		
		// Get Business Data
		$this->business	= Secretary\Application::company();
		
		//Get Field options
		JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
		$this->genderoptions		=	JFormHelper::loadFieldType('gender', false)->getList( $this->item->subject[0], 'jform[subject][0]' );
		$this->entityoptions		=	JFormHelper::loadFieldType('entities', false)->getList();
		$this->productUsageOption	=	JFormHelper::loadFieldType('productUsage', false)->getList( $this->item->productUsage );
		$this->itemtemplates		=	JFormHelper::loadFieldType('templates', false)->getList( $this->item->template, 'jform[template]');
		
		$this->emailtemplates		=	JFormHelper::loadFieldType('templates', false)->getList( $this->item->message['template'] , 'jform[fields][message][template]');
		if( $this->item->message['template'] != 0)
			$this->emailTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
		
		if(!empty($this->item->template))
			$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template); 
			
		parent::display($tpl);
	}
	
}
