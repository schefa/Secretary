<?php

defined('_JEXEC') or die;

class SecretaryTableFolder extends \Joomla\CMS\Table\Table
{

	/**
	 * Class constructor
	 *
	 * @param mixed $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__secretary_folders', 'id', $db);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::bind()
	 */
	public function bind($array, $ignore = '')
	{

		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.folder.' . $array['id']))
		{
			$actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='folder']/");
			$default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.folder.' . $array['id'])->getData();
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
	 * @see \Joomla\CMS\Table\Table::_getAssetName()
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		
        return 'com_secretary.folder.' . (int) $this->$k;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = NULL, $id = NULL)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.folder');
		
        return $asset->id;
	}

	/**
	 * Prepare data before saving
	 * 
	 * @param array $data
	 */
	public function prepareStore(&$data)
	{

		// Prepare
		$business	            = Secretary\Application::company();
		$data['business']		= (!empty($this->business)) ? $this->business : (int) $business['id'];
		$data['created_by']		= (!empty($this->created_by)) ? $this->created_by : \Secretary\Joomla::getUser()->id;
		$data['created_time']	= (!empty($this->created_time)) ? $this->created_time : date('Y-m-d h:i:s');
		$data['level']			= (isset($this->level)) ? $this->level : 1;
		$data['description']	= Secretary\Utilities\Text::ripTags($data['description']);

		if (!isset($data['fields']))
		{
			$data['fields'] = array();
		}

		foreach ($data['fields'] as $key => $value)
		{
			if (!is_numeric($key) && !is_array($value))
			{
				$q = $this->_db->getQuery(true);
				$q->select('*')
					->from('#__secretary_fields')
					->where('hard = ' . $this->_db->quote($key));

				$this->_db->setQuery($q);
				$field = $this->_db->loadObject();
				$data['fields'][] = array('id' => $field->id, 'hard' => $key, 'title' => \Joomla\CMS\Language\Text::_($field->title), 'values' => $value);
				unset($data['fields'][$key]);
			}
		}

		// Data Fields
		$data['fields']	= (isset($data['fields'])) ? \Secretary\Helpers\Items::saveFields($data['fields']) : FALSE;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::check()
	 */
	public function check()
	{
		if ($this->parent_id > 0 && $this->parent_id == $this->id)
		{
			$this->setError(\Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_FIELD_PARENT_DESC'));
			
            return false;
		}

		$parent = Secretary\Database::getQuery('folders', $this->parent_id, 'id', 'level,parent_id');
		
        if ($this->parent_id > 0 && !empty($parent) && (isset($this->level) && $parent->level > $this->level) || ($this->id > 0 && $this->id === $this->parent_id))
		{
			$this->setError(\Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_FIELD_PARENT_DESC'));
			
            return false;
		}

		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}

		return true;
	}

	/**
	 * Delete and save activity
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
	public function delete($pk = NULL)
	{
		$this->load($pk);
		$result = parent::delete($pk);
		
        if ($result)
		{
			\Secretary\Helpers\Activity::set('folders', 'deleted', 0, $pk);
		}
		
        return $result;
	}

	/** ***********************************************/

	public function rebuildLevel($pk, $parentid, $level = null)
	{

		// Get Level of parent
		$parentLevel = Secretary\Database::getQuery('folders', $parentid, 'id', 'level', 'loadResult');
		
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
		$childs = Secretary\Database::getQuery('folders', $pk, 'parent_id', 'id,level', 'loadObjectList');
		
        foreach ($childs as $child)
		{
			$this->rebuildLevel($child->id, $pk, $child->level);
		}

		return true;
	}

	public function reorderFolder($business, $extension)
	{

		$query	= $this->_db->getQuery(true);
		$query->select('id,title,level,parent_id,state')
			->from($this->_tbl)
			->where($this->_db->quoteName('business') . ' = ' . intval($business))
			->where($this->_db->quoteName('extension') . "=" . $this->_db->quote($extension));

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

		$newItems = \Secretary\Helpers\Folders::reorderFolderItems($oldItems, true);
		$x = 1;

		foreach ($newItems as $itemId)
		{
			// Update Level of pk
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl);
			$query->set('ordering = ' . intval($x));
			$query->where('id = ' . intval($itemId));
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
