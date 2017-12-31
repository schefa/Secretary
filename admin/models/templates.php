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

class SecretaryModelTemplates extends JModelList
{
    
    protected $app;
    private $extension;
    private $catId;
    
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
                'text', 'a.text',
                'state', 'a.state',
				'extension', 'a.extension',
                'category', 'category',
				'category_id',
            );
        }
        $this->app          = \Secretary\Joomla::getApplication();
        $this->extension    = $this->app->input->getCmd('extension');
        $this->catId        = $this->app->input->getInt('catid');
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
	{
        parent::populateState('a.title', 'asc');
        
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
		
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select
		$query->select( $this->getState( 'list.select', 'a.*' ) );
		$query->from($db->qn('#__secretary_templates','a'));
			
		// Join status.
		$query->select('s.title AS status_title,s.icon,s.description AS tooltip,s.class');
		$query->leftJoin($db->qn('#__secretary_status','s').' ON s.id = a.state');
				
		// Join categories
		$query->select("c.title AS category");
		$query->leftJoin($db->qn('#__secretary_folders','c').' ON c.id=a.catid');
		
		// Join users
		$query->select("uc.name AS editor");
		$query->leftJoin($db->qn('#__users','uc').' ON uc.id=a.checked_out');
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where(' ( a.title LIKE ' . $search .') OR ( a.desc LIKE ' . $search .')');
		}
		
		// Filter by a single or group of folders.
		if (is_numeric($this->catId)) {
		    // Get children categories
		    $subCategories = \Secretary\Helpers\Folders::subCategories($this->catId);
		    if(is_array($subCategories)) $subCategories = implode(",", $subCategories);
		    $query->where($db->qn('a.catid').' IN (' . $subCategories . ')');
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		
		if(!empty($this->extension)) {
			$query->where('a.extension ='. $db->quote($this->extension)); 
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
			
        return $query;
    }
	
}
