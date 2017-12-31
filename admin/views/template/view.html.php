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

class SecretaryViewTemplate extends JViewLegacy
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
	    $jinput			= Secretary\Joomla::getApplication()->input;
		$this->view		= $jinput->getCmd('view');
		$this->layout	= $jinput->getCmd('layout');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions(); // \Secretary\Helpers\Access::getActions($this->view);
		$this->extension=$this->item->extension;

		// Title
		$this->title		= JText::_('COM_SECRETARY_TEMPLATE');
		if(!empty($this->extension)) $this->title = JText::_('COM_SECRETARY_'.strtoupper( $this->extension ));
		
		// Permission
		$user = Secretary\Joomla::getUser();
		$show = false;
		if( $this->layout == 'edit' && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id ) ) {
		    $show = true;
		} elseif($this->layout != 'edit' && true === \Secretary\Helpers\Access::show($this->view, $this->item->id ) ) {
		    $show = true;
		}
		
		if( !$show) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		} elseif (count($errors = $this->get('Errors'))) {
		    $app->enqueueMessage( implode("\n", $errors) , 'error');
		    return false;
		}
		
		
		if(!empty($this->item->business)) {
  			$this->business	= Secretary\Database::getQuery('businesses', $this->item->business,'id','*','loadAssoc');
		} else {
  			$this->business	= Secretary\Application::company();
		}

        if (isset($this->item->checked_out)) {
		    $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $this->checkedOut = false;
        }

        $this->datafields		= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
        
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{ 
		// If not checked out, can save the item.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('template.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('template.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$this->checkedOut && ($this->canDo->get('core.create'))){
			echo Secretary\Navigation::ToolbarItem('template.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!($this->item->id == 0) && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('template.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			echo Secretary\Navigation::ToolbarItem('template.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		}
		else {
			echo Secretary\Navigation::ToolbarItem('template.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

	}
	protected function getJS()
	{
		$document = JFactory::getDocument();

		$document->addScript(SECRETARY_MEDIA_PATH.'/js/secretary.template.js?v='.SECRETARY_VERSION);

		$javaScript = 'var proportion = "'. $this->item->dim->proportion .'",
			formatWidth = '. $this->item->dim->formatWidth .',
			formatHeight = '. $this->item->dim->formatHeight .',
			dpi = '. $this->item->dpi .',
			zindex = 100;';

		$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
		$javaScript = 'Secretary.printFields( ['. $fields .'] );' . $javaScript;
		
		$document->addScriptDeclaration($javaScript ." 
			Joomla.submitbutton = function(task) {
				if (task == 'template.cancel') {
					Joomla.submitform(task, document.getElementById('adminForm'));
				} else {
					if (task != 'template.cancel' && document.formvalidator.isValid(document.id('adminForm'))) {
            		    var toolbar = document.getElementById('secretary-template-designer-toggle');
            		    if(toolbar.classList.contains('active')) {
            		        document.getElementById('secretary-canvas-input').value = document.getElementById('secretary-canvas').innerHTML;
            		    }
                        Joomla.submitform(task, document.getElementById('adminForm'));
					} else {
						alert('". $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')) ."');
					}
				}
			}
		"); 
	}


}
