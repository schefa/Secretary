<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

class SecretaryTableTask extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_tasks', 'id', $db);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
	{
        if (!JFactory::getUser()->authorise('core.admin', 'com_secretary.task.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_secretary', 'task');
            $default_actions = JFactory::getACL()->getAssetRules('com_secretary.task.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
				if(isset($default_actions[$action->name]))
					$array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array_jaccess);
        }
		
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array['rules']);
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::check()
     */
    public function check() {

        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }

        return parent::check();
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetName()
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_secretary.task.' . (int) $this->$k;
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
	protected function _getAssetParentId(JTable $table = null, $id = null)
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
    public function delete($pk = null) {
        $this->load($pk);
        $result = parent::delete($pk);
        if ($result) {
			\Secretary\Helpers\Activity::set('tasks', 'deleted', $this->catid, $pk);
        }
        return $result;
    }
	
    /**
     * Rebuilds tasks
     * 
     * @param unknown $pk
     * @param unknown $parentid
     * @param unknown $level
     * @return boolean
     */
	public function rebuildLevel($pk, $parentid, $level = null )
	{
		
		// Get Level of parent
		$parentLevel = Secretary\Database::getQuery('tasks',intval($parentid),'id','level','loadResult');
		 
		if(($level != 0) && $level < $parentLevel) {
			$this->setError(JText::_('rebuildLevel'));
			return false;
		}
		
		// Update Level of pk
		$query = $this->_db->getQuery(true);
		$query->update($this->_tbl)
				->set('level = '.(int) ($parentLevel + 1))
				->where($this->_tbl_key.' = '. (int) $pk);
		$this->_db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion.
		if (!$this->_db->query()) {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}
		
		// Update Childs
		$childs = Secretary\Database::getQuery('tasks',intval( $pk ), 'parentID', 'id,level', 'loadObjectList');
		foreach($childs as $child)
			$this->rebuildLevel($child->id, $pk, $child->level);
				
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
		$query->select($this->_db->qn(array('id','title','level','parentID','state')))
				->from($this->_tbl)
				->where($this->_db->qn('business').' = '. intval($business['id']));
		
		try {
			$this->_db->setQuery($query);
			$oldItems = $this->_db->loadObjectList();
		} catch(Exception $ex) {
			$this->setError($ex->getMessage());
			return false;
		}
		
		$newItems = \Secretary\Helpers\Times::reorderTasks( $oldItems, true );
		$x = 1;
		
		foreach($newItems as $itemId)
		{
				
			// Update Level of pk
			$query = $this->_db->getQuery(true);
			$query->update($this->_tbl)
					->set('ordering = '. intval($x))
					->where('id = '. intval($itemId));
			$this->_db->setQuery($query);
	
			// If there is an update failure, return false to break out of the recursion.
			if (!$this->_db->query()) {
				$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $this->_db->getErrorMsg()));
				$this->setError($e);
				return false;
			}
			
			$x++;
		}
		
		return true;
    }
	
}
