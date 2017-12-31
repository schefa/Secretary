<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SecretaryModelFolders extends JModelList
{
    private $app;
	protected $extension;
	protected $business;
	
	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'state', 'a.state',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_time', 'a.created_time',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'level', 'a.level',
			);
		}

		$this->app        = JFactory::getApplication();
		$this->extension  = $this->app->input->getCmd('extension');
		$this->business   = Secretary\Application::company();
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
		
		$search = $this->getUserStateFromRequest($this->context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('a.ordering', 'asc');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::getStoreId()
	 */
	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		return parent::getStoreId($id);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db       = $this->getDbo();
		$query    = $db->getQuery(true); 

		// Select
		$query->select('a.*');
		$query->from($db->qn('#__secretary_folders','a'));

		// Join users
		$query->select('ua.name AS editor');
		$query->join('LEFT', $db->qn('#__users','ua').' ON ua.id = a.checked_out');

		$query->where('a.business = ' . $db->escape($this->business['id']));
		
		if($this->extension == 'messages') {
			$query->where(' ( a.extension = ' . $db->quote($this->extension) .' OR  a.extension = '.$db->quote("newsletter").')');
		} else {
			$query->where('a.extension = ' . $db->quote($this->extension));
		}
		$query->where($db->qn('extension').' != '.$db->quote('system'));
			
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('a.title LIKE ' . $search  );
			}
		}
		
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.extension');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		
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
	        if(($user->id == $item->created_by && $user->authorise('core.edit.own', 'com_secretary.folder'))
	            || $user->authorise('core.edit', 'com_secretary.folder')) {
                $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
            }
            
            if(!$item->canCheckin) $item->canCheckin = $user->authorise('core.admin', 'com_secretary');
            if(!$item->canChange) $item->canChange = $user->authorise('core.edit.state', 'com_secretary.folder');
            if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.folder.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission
            
            if(strpos($item->title,'COM_') !== false) $item->title = JText::_($item->title);
            
            $result[] = $item;
	    }
	    return $result;
	}
}
