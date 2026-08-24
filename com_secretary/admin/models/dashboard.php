<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;

class SecretaryModelDashboard extends ListModel
{

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'created', 'a.created',
				'extension', 'a.extension',
			);
		}
		parent::__construct($config);
	}

	/**
	 * Override method session state 
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('a.created', 'desc');
	}

	/**
	 * @Override
	 * Method to get activities
	 * 
	 * @return array activities
	 */
	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select($this->getState('list.select', 'a.*'));
		$query->from($db->qn('#__secretary_activities', 'a'));

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		
        if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to prepare activity items
	 * 
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::getItems()
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		$user	= \Secretary\Joomla::getUser();

		if (!empty($items))
		{
			foreach ($items as $x => $activity)
			{
				$extension = Secretary\Application::getSingularSection($activity->extension);
				// Permission Document
				$canSee = false;
				
                if (((int) $user->id == (int) $activity->created_by) || $user->authorise('core.show.other', 'com_secretary.' . $extension))
				{
					$canSee = true;
				}

				if (!$canSee)
				{
					$canSee = $user->authorise('core.show.other', 'com_secretary.' . $extension . '.' . $activity->id);
				}
				
                if (!$canSee)
				{
					unset($items[$x]);
					continue;
				}
			}
		}

		return $items;
	}

	/**
	 * Method to delete a single activity
	 */
	public function delete(&$pks)
	{
		$app = Factory::getApplication();
		$pks = (array) $pks;
		$table = Table::getInstance('Activities', 'SecretaryTable');
		$user	= \Secretary\Joomla::getUser();

		PluginHelper::importPlugin('content');

		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($user->authorise('core.delete', 'com_secretary'))
				{
					$context = 'com_secretary.' . $this->name;
					$result = $app->triggerEvent($this->event_before_delete, array($context, $table));

					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						
                        return false;
					}

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						
                        return false;
					}

					// Trigger the onContentAfterDelete event.
					$app->triggerEvent($this->event_after_delete, array($context, $table));
				}
                else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					
                    if ($error)
					{
						Log::add($error, Log::WARNING, 'jerror');
						
                        return false;
                    }
                    else
					{
                        Log::add(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), Log::WARNING, 'jerror');
						
                        return false;
                    }
				}
			}
            else
			{
				$this->setError($table->getError());
				
                return false;
			}
		}

		$this->cleanCache();
		
        return true;
	}
}
