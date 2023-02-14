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

jimport('joomla.application.component.controlleradmin');

class SecretaryControllerItems extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;
	protected $extension;
	protected $redirect_url;

	public function __construct()
	{
		$this->app		  = \Secretary\Joomla::getApplication();
		$this->catid      = $this->app->input->getInt('catid');
		$this->view       = $this->app->input->getCmd('view');
		$this->extension  = $this->app->input->getCmd('extension');
		$this->redirect_url = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;extension=' . $this->extension;
		parent::__construct();
	}

	public function getModel($name = 'Item', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}

	public function saveOrder()
	{
		$user  = \Secretary\Joomla::getUser();
		$order = $this->app->input->get('order', array(), 'array');
		$msg   = JText::_('COM_SECRETARY_ORDERING_SAVED_FAILED');
		if ($user->authorise('core.admin', 'com_secretary') && !empty($order)) {
			$db = \Secretary\Database::getDBO();
			$oldOrders = array();
			$oldOrdersTasks = array();
			$start = 1;
			foreach ($order as $key => $values) {
				foreach ($values as $id) {
					$query = "UPDATE `#__secretary_status` SET `ordering` = " . $start . " WHERE extension = " . $db->quote($key) . " AND id =" . (int) $id;
					$db->setQuery($query);
					$db->execute();
					$start++;
				}
			}
			$msg = JText::_('COM_SECRETARY_ORDERING_SAVED');
		}

		$this->setMessage($msg);
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&amp;view=items&amp;extension=status', false));
	}

	public function deleteFiles()
	{

		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$files = $this->app->input->get('cid', array(), 'array');
		$user  = \Secretary\Joomla::getUser();

		if ($user->authorise('core.delete', 'com_secretary')) {
			$x = 0;
			foreach ($files as $file) {
				if (file_exists(SECRETARY_ADMIN_PATH . '/uploads/' . $file)) {
					unlink(SECRETARY_ADMIN_PATH . '/uploads/' . $file);
					$x++;
				}
			}
			$this->setMessage(JText::plural('COM_SECRETARY_N_ITEMS_DELETED', $x));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_secretary&amp;view=items&amp;extension=uploads', false));
	}

	private function deleteFile($pk = null)
	{

		// Uploads löschen
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		$upload = Secretary\Database::getQuery('uploads', $pk);

		$path = SECRETARY_ADMIN_PATH . '/uploads/' . $upload->business . '/' . $upload->folder . '/' . $upload->title;
		if (JFile::delete($path)) {
			if ($upload->itemID > 0) $this->_updateItemDocument($upload->itemID, $upload->extension, $pk);
			$this->app->enqueueMessage(JText::sprintf('COM_SECRETARY_UPLOAD_DELETED', $upload->title), 'notice');
		} else {
			$this->app->enqueueMessage(JText::sprintf('COM_SECRETARY_UPLOAD_DELETED_NOT', $upload->title), 'error');
		}

		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__secretary_uploads'))
			->where($db->quoteName('id') . ' = ' . $db->escape($pk));

		$db->setQuery($query);
		$db->execute();
	}

	private function _updateItemDocument($itemID, $extension, $uploadID)
	{
		$db			= \Secretary\Database::getDBO();
		$query		= $db->getQuery(true);
		$fields		= array($db->qn('upload') . " = ''");
		$conditions	= array($db->qn('id') . ' = ' . $db->escape($itemID), $db->qn('upload') . ' = ' . $db->escape($uploadID));
		$query->update($db->qn('#__secretary_' . $extension))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}
}
