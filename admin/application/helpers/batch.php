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

namespace Secretary\Helpers;

use JArrayHelper;
use JError;
use JText;

// No direct access
defined('_JEXEC') or die;

class Batch
{

	public static function batch($view, $commands, $pks, $contexts)
	{
		// Permission
		$section = \Secretary\Application::getSingularSection($view);
		if (!\Secretary\Joomla::getUser()->authorise('core.edit', 'com_secretary.' . $section)) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}

		// Sanitize user ids.
		$pks = array_unique($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			JError::raiseError(303, JText::_('COM_SECRETARY_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['folder_id'])) {
			if (!self::batchCategory($view, $pks, $commands['folder_id']))
				return false;

			$done = true;
		}

		if (!empty($commands['project_id_copy'])) {
			if (!self::batchCopyTasksToProject($view, $pks, $commands['project_id_copy']))
				return false;

			$done = true;
		}

		if (!empty($commands['createContactsinGroups'])) {
			if (!self::createContactsinGroups('subjects', $pks, $commands['createContactsinGroups']))
				return false;
			$done = true;
		}

		if (!empty($commands['states'])) {
			if (!self::batchStates($view, $pks, $commands['states']))
				return false;
			$done = true;
		}

		if (isset($commands['template']) && strlen($commands['template']) > 0) {
			if (!self::batchTemplate($view, $pks, $commands['template']))
				return false;
			$done = true;
		}

		if (!empty($commands['newsletter'])) {
			if (!self::batchNewsletter($pks, $commands['newsletter'])) {
				return false;
			}

			$done = true;
		}

		// Remove Fields
		if (!empty($commands['removefield'])) {
			if (!self::removeField($view, $pks, $commands['removefield'])) {
				return false;
			}
			$done = true;
		}

		// Add Fields
		$input = \Secretary\Joomla::getApplication()->input;
		$data = $input->get('jform', '', 'RAW');
		if (count($data['fields'] ?? []) > 1) {
			if (!self::addField($view, $pks, $data['fields'])) {
				return false;
			}
			$done = true;
		}

		if (!$done) {
			JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		return true;
	}

	public static function batchNewsletter($contactsIDs, $newsletter_id)
	{
		\Secretary\Helpers\Messages::addNewsletterToContacts($newsletter_id, $contactsIDs, true);
		return true;
	}

	private static function createContactsinGroups($view, $messages_ids, $ids)
	{
		$db = \Secretary\Database::getDBO();
		$app = \Secretary\Joomla::getApplication();
		foreach ($messages_ids as $messages_id) {
			$message = \Secretary\Database::getQuery('messages', $messages_id, 'id', 'created_by,created_by_alias');

			if (!empty($message->created_by) && !empty($message->created_by_alias)) {
				$cleanname = explode(" ", trim($message->created_by));
				$lastname = trim(array_pop($cleanname));
				$firstname = trim(str_replace($lastname, '', $name));

				// Get the DB object
				$query = $db->getQuery(true);

				// Insert new contact
				$query->insert($db->quoteName('#__secretary_subjects'))
					->set($db->quoteName('lastname') . ' = ' . $db->quote($lastname))
					->set($db->quoteName('firstname') . ' = ' . $db->quote($firstname))
					->set($db->quoteName('email') . ' = ' . $db->quote($message->created_by_alias));

				$db->setQuery($query);

				try {
					$db->execute();
				} catch (\Exception $e) {
					$app->enqueueMessage($e->getMessage(), 'error');
					return false;
				}
			}
		}
		return true;
	}


	public static function batchCategory($view, $entries_ids, $ids)
	{
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		$app = \Secretary\Joomla::getApplication();

		// Update the reset flag
		$query->update($db->quoteName('#__secretary_' . $view))
			->set($db->quoteName('catid') . ' = ' . $ids)
			->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}

	public static function batchCopyTasksToProject($view, $tasks_ids, $projectID)
	{
		$app = \Secretary\Joomla::getApplication();
		$db = \Secretary\Database::getDBO();

		// Get the tasks
		foreach ($tasks_ids as $task_id) {

			if ($task_id > 0) {
				$query = $db->getQuery(true);
				$query->select('*')
					->from('#__secretary_tasks')
					->where('id=' . $db->escape($task_id));
				$db->setQuery($query);
				$object = $db->loadObject();

				if (!empty($object->id)) {
					$object->id = null;
					$object->projectID = $projectID;
					try {
						$result = $db->insertObject('#__secretary_tasks', $object);
					} catch (\RuntimeException $e) {
						$app->enqueueMessage($e->getMessage(), 'error');
						return false;
					}
				}
			}
		}


		return true;
	}

	public static function batchStates($view, $entries_ids, $ids)
	{
		$app = \Secretary\Joomla::getApplication();
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		// Update the reset flag
		$query->update($db->quoteName('#__secretary_' . $view))
			->set($db->quoteName('state') . ' = ' . $ids)
			->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}


	public static function batchTemplate($view, $entries_ids, $ids)
	{
		$app = \Secretary\Joomla::getApplication();
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		// Update the reset flag
		$query->update($db->quoteName('#__secretary_' . $view))
			->set($db->quoteName('template') . ' = ' . intval($ids))
			->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}


	/**  Batch copy folders to a new category.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success,boolean false on failure.
	 *
	 * @since   1.6
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		// $value comes as {parent_id}.{extension}
		$parts = explode('.', $value);
		$parentId = (int) JArrayHelper::getValue($parts, 0, 1);

		$table = $this->getTable();
		$db = $this->getDbo();
		$user = \Secretary\Joomla::getUser();
		$extension = \Secretary\Joomla::getApplication()->input->get('extension', '', 'word');
		$i = 0;

		// Check that the parent exists
		if ($parentId) {
			if (!$table->load($parentId)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				} else {
					// Non-fatal error
					$this->setError(JText::_('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
					$parentId = 0;
				}
			}
			// Check that user has create permission for parent category
			$canCreate = ($parentId == $table->getRootId()) ? $user->authorise('core.create', $extension) : $user->authorise('core.create', $extension . '.folder.' . $parentId);
			if (!$canCreate) {
				// Error since user cannot create in parent category
				$this->setError(JText::_('COM_SECRETARY_CATEGORIES_BATCH_CANNOT_CREATE'));
				return false;
			}
		}

		// If the parent is 0, set it to the ID of the root item in the tree
		if (empty($parentId)) {
			if (!$parentId = $table->getRootId()) {
				$this->setError($db->getErrorMsg());
				return false;
			}
			// Make sure we can create in root
			elseif (!$user->authorise('core.create', $extension)) {
				$this->setError(JText::_('COM_SECRETARY_CATEGORIES_BATCH_CANNOT_CREATE'));
				return false;
			}
		}

		// We need to log the parent ID
		$parents = array();

		// Calculate the emergency stop count as a precaution against a runaway loop bug
		$query = $db->getQuery(true)
			->select('COUNT(id)')
			->from($db->quoteName('#__secretary_folders'));
		$db->setQuery($query);

		try {
			$count = $db->loadResult();
		} catch (\Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks) && $count > 0) {
			// Pop the first id off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				} else {
					// Not fatal error
					$this->setError(JText::sprintf('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Copy is a bit tricky, because we also need to copy the children
			$query->clear()
				->select('id')
				->from($db->quoteName('#__secretary_folders'))
				->where('lft > ' . (int) $table->lft)
				->where('rgt < ' . (int) $table->rgt);
			$db->setQuery($query);
			$childIds = $db->loadColumn();

			// Add child ID's to the array only if they aren't already there.
			foreach ($childIds as $childId) {
				if (!in_array($childId, $pks)) {
					array_push($pks, $childId);
				}
			}

			// Make a copy of the old ID and Parent ID
			$oldId = $table->id;
			$oldParentId = $table->parent_id;

			// Reset the id because we are making a copy.
			$table->id = 0;

			// If we a copying children, the Old ID will turn up in the parents list
			// otherwise it's a new top level item
			$table->parent_id = isset($parents[$oldParentId]) ? $parents[$oldParentId] : $parentId;

			// Set the new location in the tree for the node.
			$table->setLocation($table->parent_id, 'last-child');

			// TODO: Deal with ordering?
			// $table->ordering	= 1;
			$table->level = null;
			$table->asset_id = null;
			$table->lft = null;
			$table->rgt = null;

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$i] = $newId;
			$i++;

			// Now we log the old 'parent' to the new 'parent'
			$parents[$oldId] = $table->id;
			$count--;
		}

		// Rebuild the hierarchy.
		if (!$table->rebuild()) {
			$this->setError($table->getError());
			return false;
		}

		return $newIds;
	}

	/**  Batch move folders to a new folder.
	 *
	 * @param   integer  $value     The new folder ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		$parentId = (int) $value;

		$table = $this->getTable();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = \Secretary\Joomla::getUser();
		$extension = \Secretary\Joomla::getApplication()->input->get('extension', '', 'word');

		// Check that the parent exists.
		if ($parentId) {
			if (!$table->load($parentId)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);

					return false;
				} else {
					// Non-fatal error
					$this->setError(JText::_('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
					$parentId = 0;
				}
			}
			// Check that user has create permission for parent folder
			$canCreate = ($parentId == $table->getRootId()) ? $user->authorise('core.create', $extension) : $user->authorise('core.create', $extension . '.folder.' . $parentId);
			if (!$canCreate) {
				// Error since user cannot create in parent folder
				$this->setError(JText::_('COM_SECRETARY_CATEGORIES_BATCH_CANNOT_CREATE'));
				return false;
			}

			// Check that user has edit permission for every folder being moved
			// Note that the entire batch operation fails if any folder lacks edit permission
			foreach ($pks as $pk) {
				if (!$user->authorise('core.edit', $extension . '.folder.' . $pk)) {
					// Error since user cannot edit this folder
					$this->setError(JText::_('COM_SECRETARY_CATEGORIES_BATCH_CANNOT_EDIT'));
					return false;
				}
			}
		}

		// We are going to store all the children and just move the folder
		$children = array();

		// Parent exists so we let's proceed
		foreach ($pks as $pk) {
			// Check that the row actually exists
			if (!$table->load($pk)) {
				if ($error = $table->getError()) {
					// Fatal error
					$this->setError($error);
					return false;
				} else {
					// Not fatal error
					$this->setError(JText::sprintf('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Set the new location in the tree for the node.
			$table->setLocation($parentId, 'last-child');

			// Check if we are moving to a different parent
			if ($parentId != $table->parent_id) {
				// Add the child node ids to the children array.
				$query->clear()
					->select('id')
					->from($db->quoteName('#__secretary_folders'))
					->where($db->quoteName('lft') . ' BETWEEN ' . (int) $table->lft . ' AND ' . (int) $table->rgt);
				$db->setQuery($query);

				try {
					$children = array_merge($children, (array) $db->loadColumn());
				} catch (\Exception $e) {
					$this->setError($e->getMessage());
					return false;
				}
			}

			// Store the row.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		// Process the child rows
		if (!empty($children)) {
			// Remove any duplicates and sanitize ids.
			$children = array_unique($children);
			JArrayHelper::toInteger($children);
		}

		return true;
	}


	private static function removeField($view, $entries_ids, $searchfield = "")
	{
		$can = \Secretary\Helpers\Access::getActions($view);
		if (!$can->get('core.delete') or empty($searchfield))
			return false;

		// Get the DB object
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		$query->select('id,fields')->from($db->quoteName('#__secretary_' . $view))->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')')->where('fields like ' . $db->quote('%,"' . $searchfield . '",%'));
		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $item) {

			$query = $db->getQuery(true);

			$newFields = array();
			if ($fields = json_decode($item->fields)) {
				if (!empty($fields)) {
					foreach ($fields as $key => $field) {
						if (!is_array($field) or $field[1] != $searchfield) {
							if (is_numeric($key))
								unset($key);
							$newFields[$key] = $field;
						}
					}
				}
			}
			$newFields = json_encode($newFields, JSON_NUMERIC_CHECK);

			// Update 
			$query->update($db->quoteName('#__secretary_' . $view))
				->set($db->quoteName('fields') . ' = ' . $db->quote($newFields))
				->where($db->quoteName('id') . ' = (' . (int) $item->id . ')');

			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	private static function addField($view, $entries_ids, $fields)
	{
		$can = \Secretary\Helpers\Access::getActions($view);
		if (!$can->get('core.edit') or empty($fields))
			return false;

		// Get the DB object
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		$db->setQuery("SELECT id, fields FROM " . $db->quoteName('#__secretary_' . $view) . " WHERE " . $db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');
		$items = $db->loadObjectList();

		foreach ($items as $item) {

			$query = $db->getQuery(true);
			$newFields = array();

			$oldFields = json_decode($item->fields, true);
			if (!empty($oldFields))
				$newFields = $oldFields;

			foreach ($fields as $key => $field) {
				if (is_numeric($key)) {
					$field = array_values($field);
					$newFields[] = array($field[0], $field[2], $field[3], $field[1]);
				}
			}

			$newFields = json_encode($newFields, JSON_NUMERIC_CHECK);

			// Update
			$query->update($db->quoteName('#__secretary_' . $view))
				->set($db->quoteName('fields') . ' = ' . $db->quote($newFields))
				->where($db->quoteName('id') . ' = (' . (int) $item->id . ')');

			$db->setQuery($query);
			$db->execute();
		}
	}
}