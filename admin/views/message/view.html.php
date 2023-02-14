<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die; 

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewMessage extends JViewLegacy
{
    
    protected $state;
    protected $view;
    protected $tmpl;
	protected $form;
	protected $item;
	protected $return_page;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app           = Secretary\Joomla::getApplication();
	    $layout        = $app->input->getCmd('layout');
	    $this->view    = $app->input->getCmd('view');
	    $this->tmpl    = $app->input->getCmd('tmpl');
        $user          = Secretary\Joomla::getUser();
		
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		// Get the parameters
		$this->params		= $this->state->get('params');

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? [])) {
            $app->enqueueMessage(implode("\n", $errors), 'error'); return false;
		}

		if($app->isSite() && $layout === 'form') {
            		    
    		// Only Active Menu allowed
    		$menus              = $app->getMenu();
    		$menu		        = $menus->getActive();
    		$id			        = (int) @$menu->query['id'];
		    $this->tid	        = $app->input->getInt('tid');
            $this->return_page  = $this->params->get('return');
            
            // Check if access is not public
            $groups = $user->getAuthorisedViewLevels();
            if (empty($id) || (!empty($this->item->messagesCategory->access) && (!in_array($this->item->messagesCategory->access, $groups)))) {
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'). $this->item->messagesCategory->access, 'error'); return false;
            }
            
            // Handle email cloaking
            if (isset($item->contact_to) && $this->params->get('show_email')) {
            	$item->contact_to = JHtml::_('email.cloak', $item->contact_to); }
    
    		$this->_prepareDocument();
    		
		} else {

		    // Permission
		    $this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		    $userID = Secretary\Database::getQuery('subjects',(int) $this->item->created_by,'id','created_by','loadResult');
		    $show = false;
		    
		    if( $this->_layout == 'edit' && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $userID ) ) {
		        $show = true;
		    } elseif( true === \Secretary\Helpers\Access::show($this->view, $this->item->id, (int) $userID ) ) {
		        $show = true;
		    }
		    
		    if( !$show) {
		        $app->enqueueMessage( JText::_('JERROR_ALERTNOAUTHOR') , 'error'); return false;
		    }
		    
    		$this->business	= Secretary\Application::company();
    		$this->states	= $this->getStates();
    		
    	} 
		
		return parent::display($tpl);
	}
	
	protected function _prepareDocument()
	{
	    $app     = \Secretary\Joomla::getApplication();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$title   = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_CONTACT_DEFAULT_PAGE_TITLE')); }

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];
		
		if (empty($title)) {
			$title = $app->get('sitename');
		} elseif ($app->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} elseif ($app->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename')); }

		if (empty($title)) {
			$title = $this->item->name; }
			
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description')); }

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords')); }

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots')); }

	}

	private function getStates()
	{
	    $states = JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
	    return $states;
	}
	
	private function getCategories()
	{
	    $categories = JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
	    return $categories;
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
	
	    if(isset($this->item->id)) {
	        if(COM_SECRETARY_PDF) {
	            echo '<a class="btn btn-danger modal" rel="{size: {x: 900, y: 500}, handler:\'iframe\'}" href="'. Secretary\Route::create('message', array('format'=>'pdf', 'id'=> $this->item->id )) .'"><img src="'. SECRETARY_MEDIA_PATH.'/images/pdf-20.png" />&nbsp;'. JText::_('PDF') .'</a>';
	        }
	    }
	
	    if ($this->_layout == 'edit')
	    {
	        	
	        if (!isset($this->checkedOut) && ($this->canDo->get('core.edit')||($this->canDo->get('core.create')))) {
	            echo Secretary\Navigation::ToolbarItem('message.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
	            echo Secretary\Navigation::ToolbarItem('message.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
	        }
	        $this->getJS();
	        // echo Secretary\Navigation::ToolbarItem('message.save', 'COM_SECRETARY_SEND', false, 'newentry', 'fa-send');
	        if( $this->tmpl !== 'component') { 
	           echo Secretary\Navigation::ToolbarItem('message.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
	        }
	    }
	    else
	    {
	        echo '<a class="btn btn-default" href="'. Secretary\Route::create('message', array('layout'=>'edit', 'id'=> $this->item->id )) .'">'. JText::_('COM_SECRETARY_TOOLBAR_EDIT') .'</a>';
	        	
	        /*$sender = JUser::getInstance($this->item->created_by);
	         if ($sender->authorise('core.admin') || $sender->authorise('core.manage', 'com_secretary') && $sender->authorise('core.login.admin'))
	         {
	         echo Secretary\Navigation::ToolbarItem('message.reply', 'COM_SECRETARY_MESSAGES_TOOLBAR_REPLY', false, '', 'fa-share');
	         }*/ 
	        if( $this->tmpl !== 'component') { 
	           echo Secretary\Navigation::ToolbarItem('message.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
	        }
	    }
	
	}
	
	
	protected function getJS()
	{
	    $document = JFactory::getDocument();
	    $document->addScriptDeclaration("
		Joomla.submitbutton = function(task)
		{
			if (task == 'message.cancel' || document.formvalidator.isValid(document.id('message-form')))
			{
				Joomla.submitform(task, document.getElementById('message-form'));
			}
		}
		");
	
	}
}
