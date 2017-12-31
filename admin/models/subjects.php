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

class SecretaryModelSubjects extends JModelList
{
    
    private $app;
    private $business;
    private $catId;
    private $contact_locations;
    private $layout;
    private $zip;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array())
    {
        
        $this->app        = \Secretary\Joomla::getApplication();
		$this->zip        = $this->app->input->getVar('zip');
		$this->catId      = $this->app->input->getInt('catid');
		$this->layout     = $this->app->input->getCmd('layout');
		$this->business   = Secretary\Application::company();
		 
        if (empty($config['filter_fields'])) {
            
            if($this->layout == 'modaljusers') {
                $config['filter_fields'] = array(
                    'username','a.username',
                    'name','a.name'
                );
            } else {
                $config['filter_fields'] = array(
                    'id', 'a.id',
                    'ordering', 'a.ordering',
                    'firstname', 'a.firstname',
                    'lastname', 'a.lastname',
                    'number', 'a.number',
                    'street', 'a.street',
                    'zip', 'a.zip',
                    'location', 'a.location',
                    'country', 'a.country',
                    'phone', 'a.phone',
                    'email', 'a.email',
                    'state', 'a.state',
                    'created_by', 'a.created_by',
                    'created', 'a.created',
                    'category'
                );
            }
        }
        
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState('a.lastname', 'asc');
        
        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $limit);
        
        if ($this->layout == 'modaljusers')
            $this->setState('list.ordering', null);
        
        $search = $this->app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

		$params = Secretary\Application::parameters();
		$this->setState('params', $params);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getStoreId()
     */
    protected function getStoreId($id = '')
	{
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
        return parent::getStoreId($id);
    }
    
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
    protected function getListQuery()
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true); 
		$search = $this->getState('filter.search');
        
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		
	    if($this->layout == 'modaljusers') {
	        
	    	$query->select('a.id,a.name,a.username,a.email');
	    	$query->from($db->qn('#__users','a'));
	        
	        if (!empty($search)) { 
	            $search = $db->Quote('' . $db->escape($search, true) . '%');
	            if(!empty($letter))
	                $search = $db->Quote(strtoupper($letter) . $db->escape($search, true) . '%'); 
                $query->where(' ( a.name LIKE ' . $search . ' OR a.username LIKE ' . $search . ')'); 
	        }
	        
            $query->select('map2.group_id');
            $query->join('LEFT', $db->qn('#__user_usergroup_map','map2').' ON map2.user_id = a.id');
            $query->group( $db->qn( array( 'a.id','a.name', 'a.username','a.email' )));
            
            $query->select('map3.title AS group_title');
            $query->join('LEFT',$db->qn('#__usergroups','map3').' ON map3.id = map2.group_id');
                  
	    } else {
		    
			// Select 
			$query->select('a.*');
			$query->from($db->qn('#__secretary_subjects','a'));
			
			// Join status
			$query->select('s.title AS status_title,s.icon,s.description AS tooltip,s.class');
			$query->leftJoin($db->qn('#__secretary_status','s').' ON s.id = a.state');
					
			// Join categories
			$query->select("c.title AS category");
			$query->join("LEFT", $db->qn('#__secretary_folders','c').' ON c.id=a.catid');
			
			// Join over the users for the checked out user
			$query->select("uc.name AS editor");
			$query->join("LEFT",  $db->qn('#__users','uc').' ON uc.id=a.checked_out');
			
			// Join over the user field 'created_by'
			$query->select('created_by.name AS created_by');
			$query->join('LEFT',  $db->qn('#__users','created_by').' ON created_by.id = a.created_by');
	
			$query->where($db->qn('a.business').' = ' . (int) $this->business['id']);
			
			// Filter by published state
			$published = $this->getState('filter.state');
			if (is_numeric($published)) {
			    $query->where($db->qn('a.state').'='.(int) $published);
			}
		    
			// Required Email
			$email = $this->app->input->getInt('email');
			if($email === 1) $query->where('a.email != ""');
			
			// Filter by search in title
			$letter	= $this->app->input->getCmd('letter');
			if (!empty($search)) {
				
				$search = $db->Quote('' . $db->escape($search, true) . '%');
				if(!empty($letter))
					$search = $db->Quote(strtoupper($letter) . $db->escape($search, true) . '%');
				
				$query->where(' ( (a.lastname LIKE ' . $search . ' OR a.firstname LIKE ' . $search . ' ) OR CONCAT(firstname,lastname) LIKE ' . str_replace(" ","",$search) .') ');
				
			}
			
			if(!empty($this->zip)) {
			    $query->where($db->qn('a.zip').'='.$db->quote( $this->zip ));	
			}
			
			if(!empty($letter)) {
				$search = $db->quote(strtoupper($letter) . '%');
				$query->where($db->qn('a.lastname').' LIKE ' . $search );
			}
			
			// Filter by all categories
			if (!empty($this->catId)) {  
			    $subCategories = \Secretary\Helpers\Folders::subCategories($this->catId);
			    if(is_array($subCategories)) $subCategories = implode(",", $subCategories);
			    $query->where($db->qn('a.catid').' IN (' . $subCategories . ')'); 
			}
		}
		
		// Add the list ordering clause.
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		} 
		
        return $query;
    }
    
    /**
     * Method to prepare the items
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getItems()
     */
    public function getItems() {
        $items = parent::getItems();
        $result = array();
        $contact_locations = array();
        $user = \Secretary\Joomla::getUser();
        
        foreach($items as $item) {
            
            // START Permission 
            $canSee = false; $item->canChange = false; $item->canCheckin = false; $item->canEdit = false;
            if(isset($item->created_by) && ($user->id == $item->created_by && $user->authorise('core.edit.own', 'com_secretary.subject'))
                || $user->authorise('core.edit', 'com_secretary.subject')) {
                $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
            }
            
            if(!$item->canCheckin) $item->canCheckin = $user->authorise('core.admin', 'com_secretary');
            if(!$item->canChange) $item->canChange = $user->authorise('core.edit.state', 'com_secretary.subject');
            if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.subject.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission
            
            if($this->layout !== 'modaljusers')
                $item->fullname = (!empty($item->firstname)) ? trim($item->firstname .' '.$item->lastname) :trim( $item->lastname );
            
            // Prepare contacts locations stats
            if($this->layout !== 'modaljusers') {
                $zip = trim($item->zip);
                if(!isset($contact_locations[$zip])) {
                    $contact_locations[$zip] = new stdClass();
                    $contact_locations[$zip]->zip = $zip;
                    $contact_locations[$zip]->location = $item->location;
                    $contact_locations[$zip]->total = 1;
                } else {
                    $contact_locations[$zip]->total += 1;
                }
            }
            
            $result[] = $item;
        }
        
        // Locations summary stats
        usort($contact_locations, function ($item1,$item2) {
            if ($item1->total == $item2->total) return 0;
            return ($item1->total < $item2->total) ? 1 : -1;
        });
        
        $this->contact_locations = $contact_locations;
         
        return $result;
    }
    
    /**
     * Method to get locations of contacts
     * 
     * @return stdClass[]
     */
    public function getStatsLocation()
    { 
        return $this->contact_locations;
    }
}
