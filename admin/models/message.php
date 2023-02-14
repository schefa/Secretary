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

use Joomla\Registry\Registry;

jimport('joomla.application.component.modeladmin');

class SecretaryModelMessage extends JModelAdmin
{
	protected $app;
	protected $view_item = 'message';
	protected static $_item = array();
	protected $_context = 'com_secretary.message';

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$this->app         = \Secretary\Joomla::getApplication();
		$this->business    = \Secretary\Application::company();
		$this->layout      = $this->app->input->getCmd('layout');
		$this->catid       = $this->app->input->getInt('catid');
		parent::__construct();
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::populateState()
	 */
	protected function populateState()
	{
		// Load state from the request.
		$messageId = (int) $this->app->input->getInt('id');
		$this->setState('message.id', $messageId);

		// Load the parameters.
		if ($this->app->isSite()) {
			$params = $this->app->getParams('com_secretary');
			$registry = new Registry;
			$registry->loadString($params);
			$this->setState('params', $registry);
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = $this->app->getUserState('com_secretary.edit.message.data', array());

		if (empty($data) && $this->layout != 'form') {
			$data = $this->getItem();
			$contact_to	= $this->app->input->getInt('contact');

			$data->subject			= Secretary\Utilities::cleaner($data->subject, true);
			$data->message			= Secretary\Utilities::cleaner($data->message, true);
			$data->contact_to_alias	= Secretary\Utilities::cleaner($data->contact_to_alias, true);
			$data->created_by		= Secretary\Utilities::cleaner($data->created_by, true);
			$data->created_by_alias	= Secretary\Utilities::cleaner($data->created_by_alias, true);
			$data->contact_to		= (isset($contact_to)) ? $contact_to : Secretary\Utilities::cleaner($data->contact_to, true);
		}

		// $this->preprocessData('com_secretary.message', $data);

		return $data;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		if (empty(self::$_item[$pk]) && ($item = parent::getItem($pk))) {

			if ($this->layout === 'form') {

				$pk = (!empty($pk)) ? $pk : (int) $this->getState('message.id');
				$tid = $this->app->input->getInt('tid');
				$cid = $this->app->input->getInt('cid');
				$user = \Secretary\Joomla::getUser();

				if (empty($cid) || empty($tid)) {
					$menus   = $this->app->getMenu();
					$menu = $menus->getActive();
					$cid = (int) @$menu->query['cid'];
					$tid = (int) @$menu->query['tid'];
				}

				$messagesCategory	= Secretary\Database::getQuery('folders', (int) $cid, 'id', 'id,business,title,alias,access');
				$template			= Secretary\Database::getQuery('templates', $tid, 'id', 'id,business,fields');
				$businessID			= isset($messagesCategory->business) ? $messagesCategory->business : $template->business;
				if (empty($businessID)) $businessID = $this->business['id'];

				if (($cid > 0 && empty($messagesCategory)) && empty($template)) {
					$this->setError(JText::_('COM_SECRETARY_MESSAGE_NO_CATEGORY_OR_TEMPLATE'));
					return false;
				}

				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select("a.*,CONCAT_WS(firstname,lastname) AS name")
					->from('#__secretary_subjects AS a')
					->select("c.title as category_title,c.access as category_access")
					->leftJoin('#__secretary_folders AS c ON c.id = a.catid')
					->where('a.business=' . intval($businessID))
					->where('a.id = ' . intval($pk));

				try {
					// Filter by start and end dates.
					$db->setQuery($query);
					$item = $db->loadObject();

					if (empty($item)) {
						JError::raiseError(404, JText::_('COM_SECRETARY_ERROR_CONTACT_NOT_FOUND') . $businessID);
						return false;
					}
				} catch (Exception $e) {
					$this->setError($e);
					self::$_item[$pk] = new stdClass();
					return false;
				}

				$item->messagesCategory = $messagesCategory;
				$item->template = $template;
			} else {

				$item->subject			= Secretary\Utilities::cleaner($item->subject, true);
				$item->message			= Secretary\Utilities::cleaner($item->message, true);
				$item->contact_to_alias	= Secretary\Utilities::cleaner($item->contact_to_alias, true);
				$item->created_by		= Secretary\Utilities::cleaner($item->created_by, true);
				$item->created_by_alias	= Secretary\Utilities::cleaner($item->created_by_alias, true);

				if (!empty($this->catid)) {
					$item->fields = Secretary\Database::getQuery('folders', $this->catid, 'id', 'fields', 'loadResult');
					if (!empty($item->fields) && $catFields = json_decode($item->fields)) {
						$newFields = array();
						foreach ($catFields as $key => $value) {
							if ('template' === $value[3] && empty($item->template)) {
								$item->template = $value[2];
							} else {
								$newFields[$key] = $value;
							}
						}
						$item->fields = \Secretary\Helpers\Items::rebuildFieldsForDocument($newFields);
					}
				}
				$item->catid = (int) (empty($item->catid)) ? $this->catid : $item->catid;
				$item->template = (!empty($item->template)) ? $item->template : 0;

				$user = \Secretary\Joomla::getUser();
				$userContact = Secretary\Database::getQuery('subjects', (int) $user->id, 'created_by', 'id', 'loadResult');
				$item->created_by = (isset($item->created_by)) ? $item->created_by : (int) $userContact;

				if (empty($item->message) && ($item->template > 0)) {
					$template = Secretary\Database::getQuery('templates', $item->template);
					$fields['title'] = JText::_($template->title);
					$item->message = \Secretary\Helpers\Templates::transformText($template->text, NULL, $fields);
				}
			}

			self::$_item[$pk] = $item;
		}

		return self::$_item[$pk];
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::save()
	 */
	public function save($data)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables; 
		$user	= \Secretary\Joomla::getUser();
		$userId	= \Secretary\Database::getQuery('subjects', (int) \Secretary\Joomla::getUser()->id, 'created_by', 'id', 'loadResult');

		$table	= $this->getTable();
		$pk		= (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');

		// Access
		if (!(\Secretary\Helpers\Access::checkAdmin())) {
			if (!$user->authorise('core.create', 'com_secretary.message') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.message.' . $pk))) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}

		// Allow an exception to be thrown.
		try {
			// Load the row if saving an existing record.
			if ($pk > 0) {
				$table->load($pk);
			}


			$data['business']	= (int) $this->business['id'];

			if (isset($table->refer_to)) $data['refer_to'] = $table->refer_to;
			$data['created']			= (empty($data['created'])) ? date('Y-m-d h:i:s') : $table->created;
			$data['subject']			= (empty($data['subject'])) ? $table->subject : Secretary\Utilities::cleaner($data['subject']);
			$data['message']			= (empty($data['message'])) ? $table->message : Secretary\Utilities::cleaner($data['message']);
			$data['contact_to']			= (empty($data['contact_to'])) ? $table->contact_to : Secretary\Utilities::cleaner($data['contact_to']);
			$data['contact_to_alias']	= (empty($data['contact_to_alias'])) ? $table->contact_to_alias : Secretary\Utilities::cleaner($data['contact_to_alias']);
			$data['created_by']			= (empty($data['created_by'])) ? (int) $userId : Secretary\Utilities::cleaner($data['created_by']);

			if (empty($data['created_by_alias']) && $data['contact_to'] > 1 && $data['catid'] == 0) {
				$data['created_by_alias'] = $this->getUserData((int) $data['contact_to']);
			} else {
				$data['created_by_alias']	= (empty($data['created_by_alias'])) ? $table->created_by_alias : Secretary\Utilities::cleaner($data['created_by_alias']);
			}

			// Bind
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}

			// Check
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}

			// Store
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			$newID = (int) $table->id;

			// Update Parent
			if (!isset($data['refer_to']) || $data['refer_to'] < 1) {
				$table->refer_to = $newID;
				$table->store();
			}

			// Update Upload Document
			if ($user->authorise('core.upload', 'com_secretary')) {
				\Secretary\Helpers\Uploads::upload('attachment', 'messages', $data['upload_title'], $newID);
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
			$this->setState($this->getName() . '.id', $table->$pkName);
		}

		$this->cleanCache();
		return true;
	}

	public function storeMessage($tid, $cid, $contact, $data)
	{
		// Initialise variables;
		$user	= \Secretary\Joomla::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();

		// Access checks.
		if (empty($tid) || empty($contact) || empty($data)) {
			throw new Exception(JText::_('Daten nicht vollständig'));
			return false;
		}

		// Allow an exception to be thrown.
		try {

			$fields = array();
			foreach ($data['fields'] as $key => $field) {
				$fields[] = array((int) $field['id'], Secretary\Utilities::cleaner($field['title']), Secretary\Utilities::cleaner($field['values']), Secretary\Utilities::cleaner($field['hard']));
			}
			$data['fields']				= json_encode($fields);

			$data['created_by']			= Secretary\Utilities::cleaner($data['contact_name']);
			$data['created_by_alias']	= Secretary\Utilities::cleaner($data['contact_email']);
			$data['subject']			= Secretary\Utilities::cleaner($data['subject']);
			$data['message']			= Secretary\Utilities::cleaner($data['message']);
			$data['business']			= (int) $contact->business;
			$data['contact_to']			= (int) $contact->id;
			$data['catid']				= (int) $cid;
			$data['created']			= date('Y-m-d h:m:s');
			$data['template']			= (int) $tid;

			// Load existing record.
			if (isset($pk) && $pk > 0) {
				$table->load($pk);
			}

			// Bind
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}

			// Store
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			// Aktivität
			$newID = (int) $table->id;

			// Update Parent
			if (!isset($data['refer_to']) || $data['refer_to'] < 1) {
				$table->refer_to = $newID;
				$table->store();
			}

			\Secretary\Helpers\Activity::set('messages', 'created', $data['catid'], $newID, 1);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
			$this->setState($this->getName() . '.id', $table->$pkName);
		}

		return true;
	}

	public function getUserData($id)
	{
		$db		= \Secretary\Database::getDBO();
		$query	= $db->getQuery(true)
			->select($db->escape('username'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('id') . "=" . $db->escape((int)$id));

		try {
			$db->setQuery($query);
			return $db->loadResult();
		} catch (Exception $ex) {
			throw new Exception($ex->getMessage());
			return NULL;
		}
	}

	/**
	 * Getter for parameters
	 * 
	 * @return mixed|JObject
	 */
	public function getParam()
	{
		return $this->getState('params');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Message', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record, 'message');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.message', 'message', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::cleanCache()
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_secretary');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		\Secretary\Helpers\Batch::batch('messages', $commands, $pks, $contexts);
		$this->cleanCache();
		return true;
	}
}
