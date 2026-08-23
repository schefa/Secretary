<?php

namespace Secretary\Helpers;


defined('_JEXEC') or die;

class Batch
{

	public static function batch($view, $commands, $pks, $contexts)
	{
		// Permission
		$section = \Secretary\Application::getSingularSection($view);
		
		if (!\Secretary\Joomla::getUser()->authorise('core.edit', 'com_secretary.' . $section))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
		}

		// Sanitize user ids.
		$pks = array_unique($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_NO_ITEM_SELECTED'), 303);
		}

		$done = false;

		if (!empty($commands['folder_id']))
		{
			if (!self::batchCategory($view, $pks, $commands['folder_id']))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['project_id_copy']))
		{
			if (!self::batchCopyTasksToProject($view, $pks, $commands['project_id_copy']))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['states']))
		{
			if (!self::batchStates($view, $pks, $commands['states']))
			{
				return false;
			}
			$done = true;
		}

		if (isset($commands['template']) && strlen($commands['template']) > 0)
		{
			if (!self::batchTemplate($view, $pks, $commands['template']))
			{
				return false;
			}
			$done = true;
		}

		// Remove Fields
		if (!empty($commands['removefield']))
		{
			if (!self::removeField($view, $pks, $commands['removefield']))
			{
				return false;
			}
			$done = true;
		}

		// Add Fields
		$input = \Secretary\Joomla::getApplication()->input;
		$data = $input->get('jform', '', 'RAW');
		
		if (count($data['fields'] ?? []) > 1)
		{
			if (!self::addField($view, $pks, $data['fields']))
			{
				return false;
			}
			$done = true;
		}

		if (!$done)
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 404);
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
			->set($db->quoteName('catid') . ' = ' . intval($ids))
			->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (\RuntimeException $e)
		{
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
		foreach ($tasks_ids as $task_id)
		{
			if ($task_id > 0)
			{
				$query = $db->getQuery(true);
				$query->select('*')
					->from('#__secretary_tasks')
					->where('id=' . $db->escape($task_id));
				$db->setQuery($query);
				$object = $db->loadObject();

				if (!empty($object->id))
				{
					$object->id = null;
					$object->projectID = $projectID;
					
                    try
					{
						$result = $db->insertObject('#__secretary_tasks', $object);
					}
                    catch (\RuntimeException $e)
					{
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
			->set($db->quoteName('state') . ' = ' . intval($ids))
			->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
        catch (\RuntimeException $e)
		{
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

		try
		{
			$db->execute();
		}
        catch (\Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			
            return false;
		}

		return true;
	}


	private static function removeField($view, $entries_ids, $searchfield = "")
	{
		$can = \Secretary\Helpers\Access::getActions($view);
		
        if (!$can->get('core.delete') || empty($searchfield))
		{
			return false;
		}

		// Get the DB object
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		$query->select('id,fields')->from($db->quoteName('#__secretary_' . $view))->where($db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')')->where('fields like ' . $db->quote('%,"' . $searchfield . '",%'));
		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			$query = $db->getQuery(true);

			$newFields = array();
			
            if ($fields = json_decode($item->fields))
			{
				if (!empty($fields))
				{
					foreach ($fields as $key => $field)
					{
						if (!is_array($field) || $field[1] != $searchfield)
						{
							if (is_numeric($key))
							{
								unset($key);
							}
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
		
        if (!$can->get('core.edit') || empty($fields))
		{
			return false;
		}

		// Get the DB object
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);

		$db->setQuery("SELECT id, fields FROM " . $db->quoteName('#__secretary_' . $view) . " WHERE " . $db->quoteName('id') . ' IN (' . implode(',', $entries_ids) . ')');
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			$query = $db->getQuery(true);
			$newFields = array();

			$oldFields = json_decode($item->fields, true);
			
            if (!empty($oldFields))
			{
				$newFields = $oldFields;
			}

			foreach ($fields as $key => $field)
			{
				if (is_numeric($key))
				{
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