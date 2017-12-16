<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\Helpers;
 
// No direct access
use Secretary\Joomla;
use JText;

defined('_JEXEC') or die;

abstract class Access
{
	private static $storedAssetRules = array();
	private static $actions = array();
	private static $missing = array();

	public static function checkAdmin()
	{
	    $result =  \Secretary\Joomla::getUser()->get('isRoot');
	    return (bool) $result;
	}
	
    public static function getActions($section = 'component', $id = null)
	{
		if(!isset(self::$actions[$section])) {
			
			if(isset($section))
				$section = \Secretary\Application::getSingularSection( $section );
			
			$user	= Joomla::getUser();
			$result	= new \JObject;
			$id		= (!empty($id)) ? ( '.'. $id ) : '';
			
			$actions = \JAccess::getActions('com_secretary',$section);
			foreach ($actions as $action) {
				$permission = $user->authorise($action->name, 'com_secretary.'. $section . $id);
				$result->set($action->name, $permission);
				if($permission < 1)
					self::$missing[$section][] = $action->name;
			}
			
			self::$actions[$section] = $result;
		}
		
        return self::$actions[$section];
	}
	
	/**
	 * Checks if current user has limited access and displays a message
	 * 
	 * @param string $section the current section view
	 * @return NULL|mixed
	 */
    public static function getAccessMissingMsg($section = 'component')
	{
	    $params = \Secretary\Application::parameters();
		$accessMissingNote = boolval( $params->get('accessMissingNote') );
		
		if(!$accessMissingNote)
			return NULL;
			
		$section = \Secretary\Application::getSingularSection( $section );
		
		if(isset(self::$missing[$section])) {
            return '<div class="secretary-access-warning">'.JText::_('COM_SECRETARY_ACCESS_LIMITED_ACCESS') . "</div>";
		}
    }
	
	public static function show($section, $id = 0, $created_by = '' )
	{
		$user = \JFactory::getUser();
		$section = (!empty($section)) ? ( '.'.$section) : '';
		
		if(\Secretary\Helpers\Access::checkAdmin())
			return true;
		
		
		if( $id > 0 && ($created_by > 0) 
			&& ( ( $created_by === $user->id || $user->authorise('core.show.other', 'com_secretary'.$section.'.'.$id) || $user->authorise('core.show.other', 'com_secretary'.$section)) ) ) {
			return true;
		}
		elseif( $id < 1 && $user->authorise('core.show', 'com_secretary'.$section) ) 
		{
			return true;
		}	
		return false;
    }
	
	public static function edit($section = '', $id = '', $created_by = '' )
	{
		$test = false;
		$user = \JFactory::getUser();
		$section = (!empty($section)) ? ( '.'.$section) : '';
		
		// Edit
		if(isset($id) && isset($created_by)) {
			if( ($user->id == $created_by && $user->authorise('core.edit.own', 'com_secretary'. $section )) 
				|| $user->authorise('core.edit', 'com_secretary'.$section))
			{
				$test = true;
			}
		}
		// Create
		else {
			if($user->authorise('core.create', 'com_secretary'.$section)) 
			{
				$test = true;
			}
		}
		
		if(!$user->authorise('core.show', 'com_secretary'.$section))
			return false;
		
		return $test;
	}

	public static function allowEdit($section, $data = array(), $key = 'id')
	{
		 
		$recordId = (int) isset($data[$key]) ? $data[$key] : '';
		$user     = \JFactory::getUser();
		$asset    = 'com_secretary.'.$section;
		
		if ($user->authorise('core.edit', $asset)) {
			return true;
		}

		if ($user->authorise('core.edit.own', $asset))
		{
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				$table = \Secretary\Application::$sections[$section];
				$record = \Secretary\Database::getQuery($table, $recordId);
				if (empty($record)) 
					return false;

				$ownerId = (int) isset($record->created_by) ? $record->created_by : 0;
			}

			if ($ownerId == $user->id)
				return true;
		}
		
		return $user->authorise('core.edit', 'com_secretary');
	}
	
	public static function canDelete($record, $view )
	{
		$user = \JFactory::getUser();
		if (!empty($record->id))
		{
			return $user->authorise('core.delete', 'com_secretary.'.$view .'.' . (int) $record->id);
		}
		return $user->authorise('core.delete', 'com_secretary.'. $view );
	}
	
    public static function JAccessRulestoArray($jaccessrules)
	{
        $rules = array();
        foreach ($jaccessrules as $action => $jaccess) {
            $actions = array();
            foreach ($jaccess as $group => $allow) {
				if(is_string($allow)) {
					$allow = intval($allow);
					if($allow < 2)
						$actions[$group] = ((int) $allow);
				} elseif(is_bool($allow)) {
					$actions[$group] = ((int) $allow);
				}
            }
            $rules[$action] = $actions;
        }
        return $rules;
    }
     
    public static function getAssetRules ($assetId) {
        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('rules');
        $query->from($db->qn('#__assets'));
        $query->where($db->qn('id').'='.intval($assetId));
        $db->setQuery($query);
        $list = $db->loadResult();
        
        if (!empty($list) && !isset(self::$storedAssetRules[$assetId])) {
            $tmp = (array) json_decode($list);
            foreach ($tmp as $key => $arr) {
                foreach ($arr as $key2 => $val) {
                    self::$storedAssetRules[$assetId][$key][(int) $key2] = $val ;
                }
            }
        }
    }
    
    public static function checkAllow($assetId, $actionname, $group)
    {
        if (isset(self::$storedAssetRules[$assetId][$actionname][(int) $group])) {
            return  (bool) self::$storedAssetRules[$assetId][$actionname][(int) $group]  ;
        }
        return null;
    }
    
    /**
     * Method to restore both assets table and secretary rules for missing entries
     */
    public static function restoreDefaultSectionAssets()
    {
        if(!\JFactory::getUser()->authorise('core.admin', 'com_secretary') ) 
            return false;
        
        $db = \Secretary\Database::getDBO();
        
        $sections = \Secretary\Application::$sections;
        unset($sections['system']);
        unset($sections['item']);
        foreach($sections AS $singular => $plural)
        {
            $test = FALSE;
            if($singular == 'component') $assetName = 'com_secretary'; 
            else $assetName = 'com_secretary.'.$singular;
            
            // Check if has entry in assets 
            $query = $db->getQuery(true);
            $db->setQuery('SELECT name FROM #__assets WHERE name = '.$db->quote($assetName));
            $test = $db->loadResult();
            
            if(!$test) { 
                
                // Get Asset if exists
                $asset  = \JTable::getInstance('Asset');
                $asset->loadByName($assetName);
                
                $asset_id          = $asset->id;
                $asset->name       = $assetName;
                $asset->title      = JText::_('COM_SECRETARY_'.strtoupper($plural));
                $asset->rules      = '{}';
                $asset->store();
                
                // parent id
                if( !($asset_id > 0) ) {
                    \Secretary\Helpers\Access::setParentIdAssets($assetName,$singular);
                } 
            }
        }
        
        // Set Rules for secretary_settings
        \Secretary\Helpers\Access::updateSecretaryRules();
    }
    
    /**
     * Method to store parent_id and level in assets table
     * 
     * @param string $assetName
     * @param string $section
     * @return boolean
     */
    public static function setParentIdAssets($assetName,$section)
    {
        if(!\JFactory::getUser()->authorise('core.admin', 'com_secretary') )
            return false;
        
        $db = \Secretary\Database::getDBO();
        $db->setQuery('SELECT id FROM #__assets WHERE name LIKE "com_secretary"');
        $parentAssetId = $db->loadResult();
        $asset  = \JTable::getInstance('Asset');
        $asset->loadByName($assetName);
        $asset->parent_id  = ($section == 'component') ? 1 : $parentAssetId;
        $asset->level      = ($section == 'component') ? 1 : 2;
        if(!$asset->store()) {
            return false;
        }
        $asset->reset();
        return true;
    }
    
    /**
     * Method to set rules in settings by connecting them to assets table
     * 
     * @return boolean
     */
    public static function updateSecretaryRules()
    {
        if(!\JFactory::getUser()->authorise('core.admin', 'com_secretary') )
            return false;
        
        // Set Rules for secretary_settings
        $db = \Secretary\Database::getDBO();
        $sections = \Secretary\Application::$sections;
        $newRules = array();
        
        foreach($sections AS $section => $plural)
        {
            if(in_array($section,array('system','item'))) { continue; }
            $assetName = ($section == 'component') ? 'com_secretary' : 'com_secretary.'. $section;
            $db->setQuery('SELECT id FROM #__assets WHERE name LIKE '.$db->quote($assetName));
            $section_id = (int) $db->loadResult(); 
            $newRules[$section] = ($section_id > 0) ? $section_id : 0;
        }
        $newRules = json_encode($newRules,JSON_NUMERIC_CHECK);
        $query = 'UPDATE '.$db->qn('#__secretary_settings').' SET '.$db->qn('rules').'='.$db->quote($newRules).' WHERE '.$db->qn('id').' = 1';
        $db->setQuery($query);
        $db->execute();
    }
    
}
