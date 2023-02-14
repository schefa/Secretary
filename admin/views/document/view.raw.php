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
		$jinput			= \Secretary\Joomla::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$layout			= $jinput->getCmd('layout');

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);

		// Permission
		$user   = \Secretary\Joomla::getUser();
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by);

		$show = false;
		if ($layout == "edit" && true === $check) {
			$show = true;
		} elseif ($layout != "edit") {
			$subjectUserId = Secretary\Database::getQuery('subjects', $this->item->subjectid, 'id', 'created_by', 'loadResult');
			if (false !== \Secretary\Helpers\Access::show($section, $this->item->id, $this->item->created_by))
				$show = true;
			if (false !== \Secretary\Helpers\Access::show($section, $this->item->id, $subjectUserId))
				$show = true;
		}

		if (!$show) {
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 500);
			return false;
		}

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? [])) {
			throw new Exception(implode("\n", $errors), 404);
			return false;
		}

		// Get Business Data
		$this->business	= Secretary\Application::company();

		//Get Field options
		JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
		$this->genderoptions		=	JFormHelper::loadFieldType('gender', false)->getList($this->item->subject[0], 'jform[subject][0]');
		$this->entityoptions		=	JFormHelper::loadFieldType('entities', false)->getList();
		$this->productUsageOption	=	JFormHelper::loadFieldType('productUsage', false)->getList($this->item->productUsage);
		$this->itemtemplates		=	JFormHelper::loadFieldType('templates', false)->getList($this->item->template, 'jform[template]');

		$this->emailtemplates		=	JFormHelper::loadFieldType('templates', false)->getList($this->item->message['template'], 'jform[fields][message][template]');
		if ($this->item->message['template'] != 0)
			$this->emailTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);

		if (!empty($this->item->template))
			$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);

		parent::display($tpl);
	}
}
