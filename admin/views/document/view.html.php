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

class SecretaryViewDocument extends JViewLegacy
{
    protected $business;
    protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $productUsage;
	protected $genderoptions;
	protected $entityoptions;
	protected $productUsageOption;
	protected $itemtemplates;
	protected $emailTemplate;
	protected $checkedOut;
	protected $info;
	protected $fields;
	
	protected $multiple_subjects = false;
	protected $jsonSubjects = null;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app			= \Secretary\Joomla::getApplication();
		$layout			= $app->input->getCmd('layout');
		$this->view		= $app->input->getCmd('view');
        $this->extension= 'documents'; 
		
		// Multiple contacts
		$this->subjects	= $app->input->getVar('subject');
		if(!empty($this->subjects) && $this->jsonSubjects = json_decode($this->subjects))
		    $this->multiple_subjects = true; 
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		$this->business	= \Secretary\Application::company();
		 
		// Permission
        $this->user     = JFactory::getUser();
		$show = false;
		if( $layout == "edit" && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by )) {
			$show = true;
		} elseif($layout != "edit") {
            $subjectUserId = Secretary\Database::getQuery('subjects', $this->item->subjectid,'id', 'created_by','loadResult');
		    if(false !== \Secretary\Helpers\Access::show($this->view,$this->item->id,$this->item->created_by))
		        $show = true;
		    if(false !== \Secretary\Helpers\Access::show($this->view,$this->item->id,$subjectUserId))
		        $show = true;
		}
		
		if( !$show) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>';
		    return false;
		} elseif (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage( implode("\n", $errors) , 'error');
			return false;
		}
		
		//Get Field options 
		$this->genderoptions		=	JFormHelper::loadFieldType('gender', false)->getList( (int) $this->item->subject[0], 'jform[subject][0]' );
		$this->entityoptions		=	JFormHelper::loadFieldType('entities', false)->getList();
		$this->productUsageOption	=	JFormHelper::loadFieldType('productUsage', false)->getList( $this->item->productUsage );
		$this->itemtemplates		=	JFormHelper::loadFieldType('templates', false)->getList( $this->item->template, 'jform[template]','',array("documents"));
		
		$this->subjectConnections = $this->getConnections();
		
		if(isset($this->item->message['template'])) {
			$this->emailtemplates	=	JFormHelper::loadFieldType('templates', false)->getList( $this->item->message['template'] , 'jform[fields][message][template]','',array("documents"));
			if( $this->item->message['template'] > 0)
				$this->emailTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
		}
		
		$this->defaultTemplate = \Secretary\Helpers\Templates::getTemplate($this->item->template);
		$this->item->datafields	= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
		
		$this->getJS();
		parent::display($tpl);
	}
	
	protected function getConnections() {

	    $html = array();
	    if($this->item->subjectid > 0 && isset($this->item->subject[7])) {
	    
	        $subjectConnect = \Secretary\Helpers\Connections::getConnectionsSubjectData($this->item->subjectid);

	        foreach($subjectConnect as $key=>$value) {
	            $fullname = (!empty($value->note)) ? $value->fullname . " (".$value->note.")" : $value->fullname;
	            $html[] = JHtml::_('select.option', $value->id, $fullname );
	        }
	    
	    }
	    
	    $display = (!empty($html)) ? "block" : "none";
	    $standard = isset($this->item->subject[7]) ? $this->item->subject[7] : 0;
        $result = '<div class="secretary-control-group-name connection-dropdown ui-widget" style="display:'.$display.';">';
	    $result .= '<select id="jform_subject_connection" name="jform[subject][7]" class="form-control fullwidth">';
	    $result .= JHtml::_('select.options', $html, 'value', 'text', $standard);
	    $result .= '</select></div>';
	    return $result;
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		
		$this->document->setTitle('Secretary - '. $this->item->document_title );
		 
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));
        } else {
            $this->checkedOut = false;
        }
		
		// If not checked out, can save the document.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create')))) {
			echo Secretary\Navigation::ToolbarItem('document.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry' );
			echo Secretary\Navigation::ToolbarItem('document.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry' );
		}
		
		if (!$this->checkedOut && ($this->canDo->get('core.create'))) {
			echo Secretary\Navigation::ToolbarItem('document.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false, '' );
		}
		
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('document.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false, '' );
		}
		
		echo Secretary\Navigation::ToolbarItem('document.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false, '' );
		
	}
	
	protected function getJS()
	{
		$document = JFactory::getDocument();
		
		if(!empty($this->item->items)) :
			$sec	= " var e_items = ".$this->item->items."; ";
			$items	= json_decode($this->item->items, true);
			$this->countParameters	= count($items);
		else :
			$sec	= " var e_items = [] ; ";
			$this->countParameters	= 0;
		endif;
		
		$sec .= ' var taxRatePerc = "' . $this->item->tax .'";';
		$sec .= ' var currency = "' . $this->item->currencySymbol .'";';
		
		$fields	= $this->item->datafields['fields'];
		$script = "jQuery.noConflict(); jQuery( document ).ready(function( $ ) { var secretary_fields = [";
		$script .= (isset($fields)) ? $fields : '';
		$script .= "]; Secretary.Fields( secretary_fields ); });";
		
		$document->addScriptDeclaration( $script . $sec);
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','document'));

	}
}
