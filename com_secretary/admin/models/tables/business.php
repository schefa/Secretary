<?php

defined('_JEXEC') or die;

class SecretaryTableBusiness extends \Joomla\CMS\Table\Table
{

    /**
     * Class constructor
     * 
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_businesses', 'id', $db);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.business.' . $array['id']))
		{
            $actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='business']/");
            $default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.business.' . $array['id'])->getData();
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

        // Bind the rules for ACL where supported. $array['rules'] is either the raw
        // per-group asset rules just computed above, or raw rules submitted from the
        // Permissions tab form - either way this is the single place that normalizes
        // them via JAccessRulestoArray() before applying, so it must run exactly once.
        if (isset($array['rules']) && is_array($array['rules']))
		{
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
    public function prepareStore(&$array)
    {

        $array['created_by']        = (!empty($this->created_by)) ? $this->created_by : \Secretary\Joomla::getUser()->id;
        $array['fields']            = (isset($array['fields'])) ? \Secretary\Helpers\Items::saveFields($array['fields']) : FALSE;
        $array['guv1']              = (!empty($array['guv1'])) ? json_encode($array['guv1']) : FALSE;
        $array['guv2']              = (!empty($array['guv2'])) ? json_encode($array['guv2']) : FALSE;
        $array['selectedFolders']   = (isset($array['selectedFolders'])) ? json_encode($array['selectedFolders'], JSON_NUMERIC_CHECK) : FALSE;

        if (empty($array['home']))
        {
            $array['home'] = $this->checkIfStandard();
        }
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
        ($db->loadResult() > 0) ? $result = 0 : $result = 1;
        
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetName()
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        
        return 'com_secretary.business.' . (int) $this->$k;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
    protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = NULL, $id = NULL)
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
        
        if ($result)
		{
            $this->deleteCompanyData($pk, 'activities');
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
    private function deleteCompanyData($company_id, $table)
    {

        $query        = $this->_db->getQuery(true);
        $conditions   = array($this->_db->qn('business') . ' = ' . intval($company_id));

        $query->delete($this->_db->qn('#__secretary_' . $table));
        $query->where($conditions);

        try
		{
            $this->_db->setQuery($query);
            $this->_db->execute();
            
            return true;
        }
        catch (Exception $ex)
		{
            throw new $ex;
            
            return false;
        }
    }
}
