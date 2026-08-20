<?php

defined('_JEXEC') or die;

class SecretaryTableTask extends \Joomla\CMS\Table\Table
{

	/**
	 * Class constructor
	 *
	 * @param mixed $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__secretary_tasks', 'id', $db);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::bind()
	 */
	public function bind($array, $ignore = '')
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.task.' . $array['id']))
		{
			$actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='task']/");
			$default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.task.' . $array['id'])->getData();
			$array_jaccess = array();
			
            foreach ($actions as $action)
			{
				if (isset($default_actions[$action->name]))
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
                }
			}
			$array['rules'] = $array_jaccess;
		}


		if (isset($array['rules']) && is_array($array['rules']))
		{
			$array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array['rules']);
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::check()
	 */
	public function check()
	{

		//If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}

		return parent::check();
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetName()
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		
        return 'com_secretary.task.' . (int) $this->$k;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = null, $id = null)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.time');
		
        return $asset->id;
	}

	/**
	 * Delete and save activity
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
	public function delete($pk = null)
	{
		$this->load($pk);
		$result = parent::delete($pk);
		
        if ($result)
		{
			\Secretary\Helpers\Activity::set('tasks', 'deleted', $this->catid, $pk);
		}
		
        return $result;
	}

	/**
	 * Rebuilds tasks
	 * 
	 * @param number $pk
	 * @param number $parentid
	 * @param number $level
	 * @return boolean
	 */
	public function rebuildLevel($pk, $parentid, $level = null)
	{

		// Get Level of parent
		$parentLevel = Secretary\Database::getQuery('tasks', intval($parentid), 'id', 'level', 'loadResult');

		if (($level != 0) && $level < $parentLevel)
		{
			$this->setError(\Joomla\CMS\Language\Text::_('rebuildLevel'));
			
            return false;
		}

		// Update Level of pk
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl)
			->set('level = ' . (int) ($parentLevel + 1))
			->where($this->_tbl_key . ' = ' . (int) $pk);
		$this->_db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion.
		// DatabaseDriver::execute() throws rather than returning false in Joomla 4+.
		try
		{
			$this->_db->execute();
		}
        catch (Exception $ex)
		{
			$this->setError(\Joomla\CMS\Language\Text::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $ex->getMessage()));
			
            return false;
		}

		// Update Childs
		$childs = Secretary\Database::getQuery('tasks', intval($pk), 'parentID', 'id,level', 'loadObjectList');
		
        foreach ($childs as $child)
		{
			$this->rebuildLevel($child->id, $pk, $child->level);
		}

		return true;
	}

	/**
	 * Reorders the tasks
	 * 
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::reorder()
	 */
	public function reorder($where = '')
	{

		$business	 = Secretary\Application::company();
		$query	= $this->_db->getQuery(true);
		$query->select($this->_db->qn(array('id', 'title', 'level', 'parentID', 'state')))
			->from($this->_tbl)
			->where($this->_db->qn('business') . ' = ' . intval($business['id']));

		try
		{
			$this->_db->setQuery($query);
			$oldItems = $this->_db->loadObjectList();
		}
        catch (Exception $ex)
		{
			$this->setError($ex->getMessage());
			
            return false;
		}

		$newItems = \Secretary\Helpers\Times::reorderTasks($oldItems, true);
		$x = 1;

		foreach ($newItems as $itemId)
		{
			// Update Level of pk
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl)
				->set('ordering = ' . intval($x))
				->where('id = ' . intval($itemId));
			$this->_db->setQuery($query);

			// If there is an update failure, return false to break out of the recursion.
			// DatabaseDriver::execute() throws rather than returning false in Joomla 4+.
			try
			{
				$this->_db->execute();
			}
            catch (Exception $ex)
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $ex->getMessage()));
				
                return false;
			}

			$x++;
		}

		return true;
	}
}
