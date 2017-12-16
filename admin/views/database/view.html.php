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

class SecretaryViewDatabase extends JViewLegacy
{	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{ 
	    $canSee	= Secretary\Joomla::getUser()->authorise('core.admin','com_secretary');
		if (!$canSee || count($errors = $this->get('Errors'))) {
			throw new Exception(500, JText('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		
		$model = $this->getModel('Database');
		$this->assetsErrors = $model->assetsErrorMissingParent();
		
		parent::display($tpl);
	}
	
}