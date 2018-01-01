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

class SecretaryTableAccounts_system extends JTable
{
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_accounts_system', 'id', $db);
    }
	
	public function rebuildLevel($pk, $parentid, $level = null )
	{
		
		// Get Level of parent
		$parentLevel = Secretary\Database::getQuery('accounts_system',intval($parentid),'id','level','loadResult');
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
		$childs = Secretary\Database::getQuery('accounts_system', $pk, 'parent_id', 'id,level', 'loadObjectList');
		foreach($childs as $child)
			$this->rebuildLevel($child->id, $pk, $child->level);
				
		return true;
	}
	
	public function reorderSystem()
	{
		
		$query	= $this->_db->getQuery(true);
		$query->select('id,title,level,parent_id,type as state')
				->from($this->_tbl);
		
		try {
			$this->_db->setQuery($query);
			$oldItems = $this->_db->loadObjectList();
		} catch(Exception $ex) {
			$this->setError($ex->getMessage());
			return false;
		}
		
		$newItems = \Secretary\Helpers\Folders::reorderFolderItems( $oldItems, true );
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
