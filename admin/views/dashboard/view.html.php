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

class SecretaryViewDashboard extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		
		$this->pagination	= $this->get('Pagination');
		$this->activities	= $this->get('Items');
		$this->state		= $this->get('State');
		$this->canDo		= \Secretary\Helpers\Access::getActions();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
		
		parent::display($tpl);
	}
	
}