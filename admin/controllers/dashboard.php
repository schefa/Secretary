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

jimport('joomla.application.component.controlleradmin');

class SecretaryControllerDashboard extends Secretary\Controller\Admin
{
	 public function delete()
	 {
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		 
		if (!is_array($cid) || count($cid) < 1)
		{
			 JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			$model = $this->getModel('Dashboard');
			
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
			
			// Remove the items.
			if ($model->delete($cid)) 
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			else
				$this->setMessage($model->getError());
		}
		
		// Invoke the postDelete method to allow for the child class to access the model.
		$this->postDeleteHook($model, $cid);
		
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=' . $this->view_list, false));
	}
	
}