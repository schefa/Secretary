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

jimport('joomla.application.component.view');

class SecretaryViewLocation extends JViewLegacy
{
    protected $business;
    protected $canDo;
    protected $extension;
    protected $form;
    protected $item;
    protected $layout;
    protected $state;
    protected $titel;
	protected $view;

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
		$this->extension= $jinput->getCmd('extension');
		$this->layout	= $jinput->getCmd('layout');
		$this->business	= Secretary\Application::company();
		$this->title	= (!empty($this->extension)) ? JText::_('COM_SECRETARY_LOCATION_'.strtoupper( $this->extension )) : JText::_('COM_SECRETARY_LOCATION');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		
		// Permission 
		$check	= \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by );
		if( $this->layout == 'edit' && !$check ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif( $this->layout != 'edit' && false === \Secretary\Helpers\Access::show($this->view, $this->item->id,  $this->item->created_by) ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif (count(($errors = $this->get('Errors')) ?? [])) {
		    throw new Exception(implode("\n", $errors));
		    return false;
		}

        if (isset($this->item->checked_out)) {
            $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == Secretary\Joomla::getUser()->id);
        } else {
            $this->checkedOut = false;
        }
        
        $this->document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','location'));
		
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
			echo Secretary\Navigation::ToolbarItem('location.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('location.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$this->checkedOut && ($this->canDo->get('core.create'))){
			echo Secretary\Navigation::ToolbarItem('location.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('location.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			echo Secretary\Navigation::ToolbarItem('location.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		}
		else {
			echo Secretary\Navigation::ToolbarItem('location.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

	}
	
}
