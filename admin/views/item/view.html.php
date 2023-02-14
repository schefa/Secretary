<?php

use Dompdf\Exception;

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

class SecretaryViewItem extends JViewLegacy
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
		$app		     = Secretary\Joomla::getApplication();
		$this->extension = $app->input->getCmd('extension');
		$this->canDo	 = \Secretary\Helpers\Access::getActions('item');

		$user            = Secretary\Joomla::getUser();
		if (!$user->authorise('core.admin', 'com_secretary')) {
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
			return false;
		}

		if ($this->extension !== 'plugins') {
			$this->item		= $this->get('Item');
			$this->state	= $this->get('State');
			$this->form		= $this->get('Form');
		}

		switch ($this->extension) {

			case 'settings':
				$this->rulesList = array();
				$model = $this->getModel();
				$sections = Secretary\Application::$sections;
				unset($sections['system']);
				unset($sections['item']);
				foreach ($sections as $singular => $plural) {
					if ($rules = json_decode($this->item->rules, true)) {
						if (isset($rules[$singular])) {
							$this->rulesList[$plural] = Secretary\HTML::_('configuration.getRulesField', $singular, $rules[$singular]);
						}
					} elseif (!isset($rules[$singular])) {
						$this->rulesList[$singular] = Secretary\HTML::_('configuration.getRulesField', $singular);
					}
				}
				break;

			case 'uploads':
				$canUpload = $user->authorise('core.upload', 'com_secretary');
				if (!$canUpload)
					throw new Exception(JText::_('COM_SECRETARY_PERMISSION_FAILED'));

				break;
		}

		if (count(($errors = $this->get('Errors')) ?? [])) {
			throw new Exception(implode("\n", $errors));
		}

		$this->getJS();
		parent::display($tpl);
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{

		$user		= \Secretary\Joomla::getUser();
		if (isset($this->item->id)) $isNew		= ($this->item->id == 0);
		if (isset($this->item->checked_out)) {
			$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		} else {
			$checkedOut = false;
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create')))) {
			echo Secretary\Navigation::ToolbarItem('item.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('item.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$checkedOut && $this->extension != 'settings' && ($this->canDo->get('core.create'))) {
			echo Secretary\Navigation::ToolbarItem('item.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}

		if ($this->extension == 'settings') {
			echo '<a href="' . Secretary\Route::create('dashboard') . '" class="btn btn-default">' . JText::_('COM_SECRETARY_TOOLBAR_CLOSE') . '</a>';
		} else {
			echo Secretary\Navigation::ToolbarItem('item.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}
	}

	protected function getJS()
	{
		$document = JFactory::getDocument();

		$this->title =  JText::_('COM_SECRETARY_' . strtoupper($this->extension));
		$document->setTitle('Secretary - ' . $this->title);
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton', 'item'));
	}
}
