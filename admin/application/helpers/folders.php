<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Helpers;

// No direct access
defined('_JEXEC') or die;

class Folders
{
    
    private static $_lastNr = array(); 
	protected static $subCategoryIds = array();
	
	/**
	 * Method to get a list of Categories
	 * 
	 * @param string $extension
	 * @return array list with id,title
	 */
	public static function getList($extension)
	{
	    $items = array();
	    $db    = \Secretary\Database::getDBO();
	    $query = $db->getQuery(true);
	    $query->select("id,title");
	    $query->from($db->qn('#__secretary_folders'));
	    $query->where($db->qn('extension').'='.$db->quote($extension));
	    $query->order('title ASC');
	    
	    $db->setQuery($query);
	    $items = $db->loadObjectList();
	    return $items;
	}
	
	/**
	 * Method to get sub categories Ids of a parent category
	 * 
	 * @param int $parent_id parent category id
	 * @return array list of sub categories Ids
	 */
	public static function subCategories( $parent_id )
	{
		if(empty(self::$subCategoryIds[$parent_id]))
		{
			$result = array();
			$childs = \Secretary\Database::getQuery('folders', intval($parent_id), 'parent_id', 'id', 'loadColumn');
			
			// Loop through childs to get their childs
			if(!empty($childs)) {
				foreach($childs as $child) {
					$grands = self::subCategories( $child );
					if(!empty($grands)) {
						foreach($grands as $grand)
							$result[] = $grand;	
					}
				}
			}
			
			$result[] = $parent_id;
			self::$subCategoryIds[$parent_id] = $result;
		}
		
		return self::$subCategoryIds[$parent_id];
		
    }
    
    /**
     * Counts the total amount of entries to get the latest number
     * 
     * @param string $table name of the Secretary table
     * @param int $catid 
     * 
     * @return number total entries in category
     */
    public static function countCategoryEntries($table,$catid)
    {
        // only secretary table
        if(!in_array($table, \Secretary\Database::$secretary_tables)) {
            throw new \Exception ('Query failure: '. $table);
            return false;
        }
        
        if(!isset(self::$_lastNr[$catid])) {
            $business	= \Secretary\Application::company();
            $db		= \Secretary\Database::getDBO();
            $query	= $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->qn('#__secretary_'.$db->escape($table)))
            ->where($db->qn('business')."=". intval( $business['id'] ))
            ->where($db->qn('catid')."=". intval( $catid ));
            $db->setQuery($query);
            $result =  $db->loadResult();
            self::$_lastNr[$catid] = $result;
        }
        return (int) self::$_lastNr[$catid];
    }
    
    /**
     * Reorders the folder hierarchy
     * 
     * @param object[] $oldItems
     * @param boolean $getOrderedValues if true, then return the reordered ids
     * @return array|object[]
     */
	public static function reorderFolderItems( $oldItems, $getOrderedValues = FALSE )
	{
		// Preprocess the list of items to find ordering divisions.
		$result = array();
		
		// Items in correct order
		// There's a bug with array (slice), so we need to do it naive
		$ordered = "";
		
		// dummy
		$items = array();
		
		// parent ids
		$levelParentIds = array();
		foreach ($oldItems as $item)
		{
			$ordered .= '-'.$item->id.'-';
			$items[$item->id] = $item ;
			$levelParentIds[$item->level][$item->parent_id][] = $item->id;
		}
		
		// sort by level
		ksort($levelParentIds);
		
		// Loop parent ids
		if(!empty($levelParentIds))
		{
			foreach($levelParentIds AS $level => $parentIds) 
			{	
			
				foreach($parentIds as $pid => $childIds)
				{
					// Search value parent_id
					$search = '-'. (string) $pid.'-';
					// Length
					$searchlen = strlen($search);
					// Position found or nah?
					// Found : There are childs 
					if(strpos($ordered, $search) !== false)
					{
						// Loop through childs
						foreach($childIds as $childId)
						{
							// Input value
							$input = '-'.$childId.'-'; 
							
							// First Remove
							$ordered = str_replace($input, "", $ordered);
		
							// Insert but search new structure
							$newPos = strpos($ordered, $search);
							$ordered = substr_replace($ordered, $input, ($newPos + $searchlen) , 0);
							
						}
					}
				}
			}
		}
		
		// Finalize
		$ordered = array_filter(explode("-",$ordered));
		if($getOrderedValues)
		{
			// Returns the reordered Ids of all Items
			return array_values($ordered);
		}
		else
		{
			// Returns the reordered items
			foreach($ordered as $id) {
				$result[] = $items[$id];
			}
			return $result;
		}
		
	}
	
}
