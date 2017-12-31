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

jimport('joomla.application.component.modellist');

class SecretaryModelBusinesses extends JModelList
{

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
	{
        parent::populateState('a.title', 'asc');
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
    protected function getListQuery() {
		
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        
        $query->select( 'a.*' );
        $query->from($db->quoteName('#__secretary_businesses', 'a'));

        return $query;
    }
    
    /**
     * Method to prepare items
     *
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getItems()
     */
    public function getItems() {
        $user = \Secretary\Joomla::getUser();
        $items = parent::getItems();
        $result = array();
        foreach($items as $item) {
            
            // START Permission
            $canSee = false; $item->canChange = false; $item->canCheckin = false; $item->canEdit = false;
            if(($user->id == $item->created_by && $user->authorise('core.edit.own', 'com_secretary.business'))
            || $user->authorise('core.edit', 'com_secretary.business')) {
                $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
            }
            
            if(!$item->canCheckin) $item->canCheckin = $user->authorise('core.admin', 'com_secretary');
            if(!$item->canChange) $item->canChange = $user->authorise('core.edit.state', 'com_secretary.business');
            if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.business.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission
                
            $result[] = $item;
        }
        return $result;
    }
}
