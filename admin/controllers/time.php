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

jimport('joomla.application.component.controllerform');

class SecretaryControllerTime extends JControllerForm
{

	protected $app;

	public function __construct()
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->section = $this->app->input->getCmd('section', 'list');
		$this->pid = $this->app->input->getInt('pid');
		$this->locationid = $this->app->input->getInt('location_id');
		$this->catid = $this->app->input->getInt('catid', 0);
		$this->extension = $this->app->input->getCmd('extension');
		$this->view_list = 'times';
		parent::__construct();
	}

	public function getModel($name = 'Time', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		if ($this->extension == 'tasks') {
			return \Secretary\Joomla::getUser()->authorise('core.edit.own', 'com_secretary.time');
		} else {
			return \Secretary\Helpers\Access::allowEdit('time', $data, $key);
		}
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;
		if ($this->extension == 'tasks' && !empty($this->pid)) {
			$append .= '&pid=' . $this->pid;
		}
		if (!empty($this->catid)) {
			$append .= '&catid=' . $this->catid;
		}
		if (!empty($this->locationid)) {
			$append .= '&location_id=' . $this->locationid;
		}
		return $append;
	}

	protected function getRedirectToListAppend()
	{
		if ($this->extension == 'tasks') {
			$this->extension = 'projects';
		}

		$append = parent::getRedirectToListAppend();
		$append .= '&section=' . $this->section;
		if (!empty($this->catid)) {
			$append .= '&catid=' . $this->catid;
		}
		if ($this->extension == 'task') {
			$this->extension = 'projects';
		}
		$append .= '&extension=' . $this->extension;
		return $append;
	}

	public function save($key = null, $urlVar = null)
	{

		parent::save($key, $urlVar);

		$task = $this->getTask();

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {

			$projectID = $this->input->getInt('id');

			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')
				->from($db->quoteName('#__secretary_tasks'))
				->where($db->quoteName('projectID') . ' = ' . $db->escape($projectID));

			$db->setQuery($query);
			$items = $db->loadObjectList();

			$newId = 0;

			$db->setQuery('SELECT id FROM #__secretary_times ORDER BY id DESC LIMIT 0,1');
			$newId = $db->loadResult();

			if ($newId > 0) {

				foreach ($items as $item) {

					$profile = new stdClass();
					$profile->projectID = $newId;
					$profile->business = $db->escape($item->business);
					$profile->parentID = $db->escape($item->parentID);
					$profile->level = $db->escape($item->level);
					$profile->state = $db->escape($item->state);
					$profile->ordering = $db->escape($item->ordering);
					$profile->title = $db->escape($item->title);
					$profile->progress = $db->escape($item->progress);
					$profile->contacts = $db->escape($item->contacts);
					$profile->startDate = $db->escape($item->startDate);
					$profile->endDate = $db->escape($item->endDate);
					$profile->calctime = $db->escape($item->calctime);
					$profile->totaltime = $db->escape($item->totaltime);
					$profile->text = $db->escape($item->text);
					$profile->fields = $db->escape($item->fields);
					$profile->upload = $db->escape($item->upload);

					// Insert the object into the user profile table.
					$result = $db->insertObject('#__secretary_tasks', $profile);
				}
			}
		}
	}

	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$this->input->set('extension', 'times');

		$model = $this->getModel('Time');

		$vars = $this->input->post->get('batch', array(), 'array');
		$cid = $this->input->post->get('cid', array(), 'array');

		// Attempt to run the batch operation.
		if ($model->batch($vars, $cid, null)) {
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));
		} else {
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
		}
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=times' . $this->getRedirectToListAppend(), false));
	}

	public function subscription()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$id = $this->app->input->getString('id');
		$id = Secretary\Security::encryptor('open', $id);
		$msg = \Secretary\Helpers\Times::subscription();
		$this->setMessage($msg);
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=time&id=' . $id . '&extension=' . $this->extension, false));
		return true;
	}
}