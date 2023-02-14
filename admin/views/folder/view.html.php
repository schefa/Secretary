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
JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewFolder extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $title;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{

		$app				= Secretary\Joomla::getApplication();
		$this->view			= $app->input->getCmd('view');
		$this->extension	= $app->input->getCmd('extension');
		$this->layout		= $app->input->getCmd('layout');

		$this->form  		= $this->get('Form');
		$this->item  		= $this->get('Item');
		$this->state		= $this->get('State');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);

		if (count(($errors = $this->get('Errors')) ?? [])) {
			$app->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		// Permission
		$user = \Secretary\Joomla::getUser();
		$show = false;
		if ($this->layout == 'edit' && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by)) {
			$show = true;
		} elseif ($this->layout != 'edit' && true === \Secretary\Helpers\Access::show($this->view, $this->item->id, $this->item->created_by)) {
			$show = true;
		}

		if (!$show) {
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return false;
		}

		$this->folders		= $this->getCategories();
		$this->datafields	= $this->getDataFields();
		$this->getJS();
		$this->title = JText::_('COM_SECRETARY_CATEGORY') . ': ' . JText::_('COM_SECRETARY_' . strtoupper($this->extension));

		if (isset($this->item->checked_out)) {
			$this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		} else {
			$this->checkedOut = false;
		}
		//Get Field options
		if ($this->item->extension == 'documents') {
			$this->productUsageOption	=	JFormHelper::loadFieldType('productUsage', false)->getList($this->item->productUsage);
			$this->emailtemplates	   =	JFormHelper::loadFieldType('templates', false)->getList($this->item->emailtemplate, 'jform[fields][emailtemplate]', 'fullwidth');
		}

		if (in_array($this->item->extension, array('documents', 'subjects', 'products', 'messages'))) {
			$this->itemtemplates		=	JFormHelper::loadFieldType('templates', false)->getList($this->item->template, 'jform[fields][template]', 'fullwidth', array($this->item->extension));
		}

		parent::display($tpl);
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{

		$isNew		= ($this->item->id == 0);

		// Prepare the toolbar.
		$this->document->setTitle($this->title);

		// For new records, check the create permission.
		if (!$this->checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create')))) {
			echo Secretary\Navigation::ToolbarItem('folder.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'newentry');
			echo Secretary\Navigation::ToolbarItem('folder.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'newentry');
			echo Secretary\Navigation::ToolbarItem('folder.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}

		// If not checked out, can save the item.
		elseif (!$this->checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.edit.own') && $this->item->created_by == $user->id))) {
			echo Secretary\Navigation::ToolbarItem('folder.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('folder.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
			if ($this->canDo->get('core.create')) {
				echo Secretary\Navigation::ToolbarItem('folder.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('folder.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}

		echo Secretary\Navigation::ToolbarItem('folder.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
	}

	private function getDataFields()
	{
		$datafields	= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
		return $datafields;
	}

	private function getJS()
	{
		$document = JFactory::getDocument();

		if ($this->layout == 'edit') {
			$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
			$document->addScriptDeclaration('Secretary.printFields( [' . $fields . '] );');
			$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton', 'folder'));
		}
	}

	private function getCategories()
	{
		$id = (!empty($this->item->id)) ? $this->item->id : '';
		$categories = JFormHelper::loadFieldType('Categories', false)->getCategories($this->extension, $this->item->id);
		return $categories;
	}
}
