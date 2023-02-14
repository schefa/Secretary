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

namespace Secretary\Controller;

// No direct access
defined('_JEXEC') or die;

use JControllerAdmin;
use JRoute;
use JSession;
use JText;
use Secretary;

jimport('joomla.application.component.controlleradmin');

class Admin extends JControllerAdmin
{
	private $redirectUrl;

	public function __construct()
	{
		$this->redirectUrl = 'index.php?option=com_secretary&amp;view=' . $this->view_list;
		parent::__construct();
	}

	protected function postDeleteUrl()
	{
		$this->setRedirect(JRoute::_($this->redirectUrl, false));
	}

	public function delete()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$section = Secretary\Application::getSingularSection($this->view_list);

		if (\Secretary\Joomla::getUser()->authorise('core.delete', 'com_secretary.' . $section)) {
			$cid = $this->input->post->get('cid', array(), 'array');

			require_once SECRETARY_ADMIN_PATH . '/models/' . $section . '.php';
			$classname = 'SecretaryModel' . ucfirst($section);
			if (class_exists($classname)) {
				$model = new $classname();
				// Remove the items.
				if ($model->delete($cid)) {
					$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid ?? [])));
				} else {
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
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$ids = $this->input->post->get('cid', array(), 'array');

		$return = Secretary\Controller::checkin($this->view_list, $ids);
		if ($return === false) {
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($this->redirect_url, false), $message, 'error');
		} else {
			// Checkin succeeded.
			$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids ?? []));
			$this->setRedirect(JRoute::_($this->redirect_url, false), $message);
		}
		return $return;
	}

	public function setStates()
	{
		$pks = \Secretary\Joomla::getApplication()->input->get('cid', array(), 'array');
		$this->setStatus($pks, $this->view);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}

	public function setStatus($pks, $view)
	{
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		// Get the DB object
		$db = Secretary\Database::getDBO();

		for ($x = 0; $x < count($pks ?? []); $x++) {

			if (!(\Secretary\Helpers\Access::getActions($view)))
				continue;

			$db->setQuery("SELECT " . $db->qn("closeTask") . " FROM " . $db->qn("#__secretary_status") . "
                    WHERE id = (SELECT state FROM " . $db->qn("#__secretary_" . $db->escape($view)) . " WHERE id = " . $db->escape($pks[$x]) . ") ");
			$closeTask = $db->loadResult();

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__secretary_' . $db->escape($view)))
				->set($db->quoteName('state') . ' = ' . $db->escape($closeTask))
				->where($db->quoteName('id') . ' = ' . $db->escape($pks[$x]));

			$db->setQuery($query);
			$db->query();
		}
	}
}