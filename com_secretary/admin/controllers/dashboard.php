<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerDashboard extends Secretary\Controller\Admin
{
	public function delete()
	{
		\Joomla\CMS\Session\Session::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$cid = \Secretary\Joomla::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid ?? []) < 1)
		{
			\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), \Joomla\CMS\Log\Log::WARNING, 'jerror');
		}
        else
		{
			$model = $this->getModel('Dashboard');

			// Make sure the item ids are integers
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(\Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid ?? [])));
            }
			else
			{
				$this->setMessage($model->getError());
            }
		}

		// Invoke the postDelete method to allow for the child class to access the model.
		$this->postDeleteHook($model, $cid);

		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list, false));
	}
}
