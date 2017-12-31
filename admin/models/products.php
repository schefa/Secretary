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

class SecretaryModelProducts extends JModelList
{

    private $app;
    private $business;
    private $catId;
    private $letter;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'year', 'a.year',
                'nr', 'a.nr',
                'taxRate', 'a.taxRate',
                'quantityMin', 'a.quantityMin',
                'quantityMax', 'a.quantityMax',
                'priceCost', 'a.priceCost',
                'priceSale', 'a.priceSale',
                'quantityBought', 'a.quantityBought',
                'quantity', 'a.quantity',
                'totalBought', 'a.totalBought',
                'total', 'a.total',
                'category', 'category'
            );
        }
        
        $this->app      = \Secretary\Joomla::getApplication();
		$this->catId	= $this->app->input->getInt('catid', 0);
		$this->letter	= $this->app->input->getVar('letter');
		$this->business	= Secretary\Application::company();
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState('a.title', 'asc');
        
        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $limit);
        
	    $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
	    $this->setState('filter.state', $published);
	    
	    $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
	    $this->setState('filter.search', $search);
	    
	    $year = $this->getUserStateFromRequest($this->context . '.filter.year', 'filter_year', date('Y'));
	    $this->setState('filter.year', $year); 
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getStoreId()
     */
    protected function getStoreId($id = '')
	{
        $id.= ':' . $this->getState('filter.search');
        return parent::getStoreId($id); 
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
    protected function getListQuery() {
		
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        $query->select($this->getState( 'list.select', 'a.*' ));
        $query->from($db->qn('#__secretary_products','a'));
        
        // Join users
        $query->select("uc.name AS editor");
        $query->join("LEFT", $db->qn('#__users','uc').' ON uc.id=a.checked_out');
        
        // Join locations
        $query->select("l.title as location");
        $query->join("LEFT", $db->qn('#__secretary_locations','l').' ON l.id=a.location');
        
        // Join categories
        $query->select("c.title as category");
        $query->join("LEFT", $db->qn('#__secretary_folders','c').' ON c.id=a.catid');
        
		$query->where($db->qn('a.business').'='. (int) $this->business['id']); 
		
		// Filter by year
		$year = $this->getState('filter.year');
		if (!empty($year)) {
		    $query->where($db->qn('a.year').'='.$year);
		}
		
		if($this->catId > 0) {
		    $query->where($db->qn('a.catid').'='. (int) $this->catId );
		}
		
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where($db->qn('a.state').'='. (int) $published);
        }
		  	
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			if(!empty($this->letter))
				$search = $db->Quote(strtoupper($this->letter) . $search . '%');
			$query->where($db->qn('a.title').' LIKE ' . $search);
        }
		
		if(!empty($this->letter)) {
			$search = $db->quote(strtoupper($this->letter) . '%');
			$query->where('a.title LIKE ' . $search );
		}
		
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

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
            if(($user->id == $item->created_by && $user->authorise('core.edit.own', 'com_secretary.product'))
            || $user->authorise('core.edit', 'com_secretary.product')) {
                $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
            }
            
            if(!$item->canCheckin) $item->canCheckin = $user->authorise('core.admin', 'com_secretary');
            if(!$item->canChange) $item->canChange = $user->authorise('core.edit.state', 'com_secretary.product');
            if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.product.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission
                
            $result[] = $item;
        }
        return $result;
    }
}
