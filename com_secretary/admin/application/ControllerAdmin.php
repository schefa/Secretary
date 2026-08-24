<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Secretary;


class Admin extends AdminController
{
	private $redirectUrl;

	public function __construct()
	{
		$this->redirectUrl = 'index.php?option=com_secretary&amp;view=' . $this->view_list;
		parent::__construct();
	}

	protected function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirectUrl, false));
	}

	public function delete()
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$section = Secretary\Application::getSingularSection($this->view_list);

		if (\Secretary\Joomla::getUser()->authorise('core.delete', 'com_secretary.' . $section))
		{
			$cid = $this->input->post->get('cid', array(), 'array');

			require_once SECRETARY_ADMIN_PATH . '/models/' . $section . '.php';
			$classname = 'SecretaryModel' . ucfirst($section);
			
            if (class_exists($classname))
			{
				$model = new $classname;
				// Remove the items.
				if ($model->delete($cid))
				{
					$this->setMessage(\Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid ?? [])));
                }
                else
				{
					$this->setMessage($model->getError(), 'error');
                }
			}
			$this->postDeleteUrl();
		}
	}

	/**
	 * Checkin action
	 * 
	 * @return boolean
	 */
	public function checkin()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$ids = $this->input->post->get('cid', array(), 'array');

		$return = Secretary\Controller::checkin($this->view_list, $ids);
		
        if ($return === false)
		{
			// Checkin failed.
			$message = \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', '');
			$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false), $message, 'error');
		}
        else
		{
			// Checkin succeeded.
			$message = \Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids ?? []));
			$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false), $message);
		}
		
        return $return;
	}

	public function setStates()
	{
		$pks = \Secretary\Joomla::getApplication()->input->get('cid', array(), 'array');
		$this->setStatus($pks, $this->view);
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
		
        return true;
	}

	public function setStatus($pks, $view)
	{
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		// Get the DB object
		$db = Secretary\Database::getDBO();

		for ($x = 0; $x < count($pks ?? []); $x++)
		{
			if (!(\Secretary\Helpers\Access::getActions($view)))
			{
				continue;
			}

			$db->setQuery("SELECT " . $db->qn("closeTask") . " FROM " . $db->qn("#__secretary_status") . "
                    WHERE id = (SELECT state FROM " . $db->qn("#__secretary_" . $db->escape($view)) . " WHERE id = " . $db->escape($pks[$x]) . ") ");
			$closeTask = $db->loadResult();

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__secretary_' . $db->escape($view)))
				->set($db->quoteName('state') . ' = ' . $db->escape($closeTask))
				->where($db->quoteName('id') . ' = ' . $db->escape($pks[$x]));

			$db->setQuery($query);
			$db->execute();
		}
	}
}