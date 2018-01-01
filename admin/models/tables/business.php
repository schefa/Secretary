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

class SecretaryTableBusiness extends JTable
{

    /**
     * Class constructor
     * 
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_businesses', 'id', $db);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
    {        
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.business.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_secretary', 'business');
            $default_actions = JFactory::getACL()->getAssetRules('com_secretary.business.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
				if(isset($default_actions[$action->name]))
                	$array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array_jaccess);
        }
		
        // Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array['rules']);
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }
    
    /**
     * Method to prepare store
     * 
     * @param array $array Data
     */
    public function prepareStore(&$array) {
        
        $array['created_by']		= (!empty($this->created_by)) ? $this->created_by : \Secretary\Joomla::getUser()->id;
        $array['fields']			= (isset($array['fields'])) ? \Secretary\Helpers\Items::saveFields($array['fields']) : FALSE;
        $array['guv1']              = (!empty($array['guv1'])) ? json_encode($array['guv1']) : FALSE;
        $array['guv2']              = (!empty($array['guv2'])) ? json_encode($array['guv2']) : FALSE;
        $array['selectedFolders']   = (isset($array['selectedFolders'])) ? json_encode($array['selectedFolders'], JSON_NUMERIC_CHECK) : FALSE;
        
        if(empty($array['home']))
            $array['home'] = $this->checkIfStandard();
    }
	
	/**
	 * Method to check if the company should be the standard
	 * 
	 * @return number 1 if no company exists
	 */
	protected function checkIfStandard()
	{
		$db   = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__secretary_businesses');
		$db->setQuery($query);
		($db->loadResult() > 0) ? $result = 0 : $result= 1;
		return $result;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetName()
	 */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_secretary.business.' . (int) $this->$k;
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
	protected function _getAssetParentId(Jtable $table = NULL, $id = NULL)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.business');
		return $asset->id;
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
        if ($result) {
					
			$this->deleteCompanyData($pk, 'activities');
			$this->deleteCompanyData($pk, 'accounts');
			$this->deleteCompanyData($pk, 'documents');
			$this->deleteCompanyData($pk, 'folders');
			$this->deleteCompanyData($pk, 'subjects');
			$this->deleteCompanyData($pk, 'messages');
			$this->deleteCompanyData($pk, 'products');
			$this->deleteCompanyData($pk, 'tasks');
			$this->deleteCompanyData($pk, 'uploads');

			// Save activity
			\Secretary\Helpers\Activity::set('businesses', 'deleted', 0, $pk);
	
        }
        return $result;
    }
	
    /**
     * Delete company data in another table 
     * 
     * @param int $company_id
     * @param string $table
     * @return boolean
     */
	private function deleteCompanyData($company_id,$table) {
	    
		$query        = $this->_db->getQuery(true);
		$conditions   = array( $this->_db->qn('business') . ' = '. intval($company_id) );
	
		$query->delete($this->_db->qn('#__secretary_'. $table));
		$query->where($conditions);
		
		try {
		    $this->_db->setQuery($query);
		    $this->_db->query();
		    return true;
		} catch (Exception $ex) {
			throw new $ex->getMessage();
			return false;
		}
	}
	
}
