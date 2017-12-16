<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SecretaryModelAccountings extends JModelList
{

    protected $app;
    protected $business;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array()) {
		
		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension','accounting');
		$this->business     = Secretary\Application::company();
		
		if(!in_array($this->extension, array('accounts','accounts_system','accounting'))) { 
		    throw new Exception('Extension not found',404);
		    return false;
		}
		
        if (empty($config['filter_fields'])) {
			switch($this->extension) {
				default: case 'accounting' : 
					$config['filter_fields'] = array(
						'id', 'a.id',
						'created', 'a.created',
						'total', 'a.total',
					);
					break;
				case 'accounts' : 
					$config['filter_fields'] = array(
						'id', 'a.id',
						'budget', 'a.budget',
					);
					break;
				case 'accounts_system' : 
					$config['filter_fields'] = array(
						'id', 'a.id',
						'ordering', 'a.ordering',
					);
					break;
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
	    $dir = $this->getUserStateFromRequest($this->context . '.list.direction', 'filter_order_Dir', 'asc', 'word');
	    $this->setState('list.direction', (($dir != 'asc') ? 'desc' : $dir));
	    
	    $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
	    $this->setState('list.limit', $limit);
	    
	    $start = $this->getUserStateFromRequest($this->context . '.list.start', 'limitstart', $this->app->get('list_limit'), 'int');
	    $this->setState('list.start', $start); 

	    $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
		
        $params = Secretary\Application::parameters();
        $this->setState('params', $params);

        parent::populateState('a.id', 'asc');
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
    protected function getListQuery()
    {
        $db			= $this->getDbo();
        $query		= $db->getQuery(true);

        $query->select($this->getState('list.select', 'a.*'));

		switch ($this->extension) {
			
			case 'accounts':
			
				$query->from($db->qn('#__secretary_accounts','a'));
				$query->select($db->qn(array('s.type','s.nr','s.title','s.level','s.parent_id')));
				$query->rightJoin($db->qn('#__secretary_accounts_system','s').' ON s.id = a.kid');
				$query->where('a.business = ' . (int) $this->business['id']);
				$query->where('a.year = '. (int) date('Y'));
				
				break;
			
			case 'accounts_system':
				
				$query->from($db->qn('#__secretary_accounts_system','a'));
				
				break;
				
			case 'accounting':
					
				$query->from($db->qn('#__secretary_accounting','a'));
				
				// Join over the status.
				$query->select('s.title AS status_title,s.icon,s.description AS tooltip,s.class');
				$query->leftJoin($db->qn('#__secretary_status','s') .' ON s.id = a.state');
						
				$query->where('a.business = ' . (int) $this->business['id']);
				
				$accountId	= $this->app->input->getInt('account');
				if($accountId > 0)
					$query->where(' ( a.soll LIKE '.$db->quote('%["'.$accountId.'",%') .') OR ( a.haben LIKE '.$db->quote('%["'.$accountId.'",%') .')');
				
				break;
				
		}
					
		if ($this->extension == 'accounts_system' || $this->extension == 'accounting') {		
			// Filter by search in title
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('a.title LIKE ' . $search);		
			}
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
     * Method to prepare the list items
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getItems()
     */
	public function getItems() {
		
		$items = parent::getItems();
		
		if ($this->extension == 'accounts') {
			$items = \Secretary\Helpers\Accounts::reorder($items);
		}
		
		return $items;
    }
	
}
