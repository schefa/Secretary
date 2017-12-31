<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

class SecretaryTableLocation extends JTable
{
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_locations', 'id', $db);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
	{  
	    if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.location.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_secretary', 'location');
            $default_actions = JFactory::getACL()->getAssetRules('com_secretary.location.' . $array['id'])->getData();
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
     * @see \Joomla\CMS\Table\Table::_getAssetName()
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_secretary.location.' . (int) $this->$k;
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.location');
		return $asset->id;
	}
	
	/** 
	 * Method to delete and save the activity
	 * 
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
    public function delete($pk = null) {
        $this->load($pk);
        $result = parent::delete($pk);
        if ($result) { 
            \Secretary\Helpers\Activity::set('locations', 'deleted', $this->catid, $pk); 
        }
        return $result;
    }
    
    /**
     * Validation check for input data
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::check()
     */
    public function check()
    {
        // No Title
        if (empty($this->title)) {
            $errTitle = JText::_('COM_SECRETARY_TITLE');
            $this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
            return false;
        }
        
        return true;
    }
}
