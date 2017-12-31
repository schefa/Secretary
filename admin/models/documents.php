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

class SecretaryModelDocuments extends JModelList
{
	private $locationid = null;
	private $catid = null;
	
	protected $app = null;
	protected $user = null;
	protected static $items;
	protected $items_expired = array();
	protected $items_report = array(
	    'data'=>array(),
	    'totat_amount'=>0,
	    'currencies'=>array()
	);

	public $itemsFilter = 0;
	public $maxValue = 1;
	
	/**
	 * Constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'title', 'a.title',
                'nr', 'a.nr',
                'contact_name', 
                'total', 'a.total',
                'catid', 'a.catid',
                'state', 'a.state',
                'created', 'a.created',
                'created_by', 'a.created_by',
            );
        }
        
		if(!empty($config['contact_ids'])) {
		    $this->contact_ids = $config['contact_ids'];
		}
        
        $this->app        = \Secretary\Joomla::getApplication();
        $this->user       = \Secretary\Joomla::getUser();
		$this->business   = Secretary\Application::company();
		$this->catId      = $this->app->input->getInt('catid',0);
		$this->currencyId = $this->app->input->getVar('currency');
		$this->locationid = $this->app->input->getInt('location',0);
		
        if ($this->app->isClient('site')) {
            $menu = $this->app->getMenu()->getActive(); 
            $this->itemsFilter = (int) $menu->params->get('itemsFilter');
        }
        
        parent::__construct($config);
    }
    
    
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState('a.created', 'desc');
        
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $published = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
        $start_date = $this->getUserStateFromRequest($this->context . '.start_date', 'start_date');
        $this->setState('start_date', $start_date);
        
        $end_date = $this->getUserStateFromRequest($this->context . '.end_date', 'end_date');
        $this->setState('end_date', $end_date);
        
        if($start_date > $end_date) {
            $this->setState('start_date', $end_date);
        }
        
        if(empty($start_date))
            $this->setState('start_date', date("Y").'-01-01');
        
        if(empty($end_date))
            $this->setState('end_date', date("Y-m-d"));
        
        $currency	= $this->business['currencySymbol'];
        if(!empty($this->locationid)) {
            $locationCurrency = Secretary\Database::getQuery('locations',(int) $this->locationid,'id','currency','loadResult');
            $currency = Secretary\Database::getQuery('currencies',$locationCurrency,'currency','symbol','loadResult');
        }
        $this->setState('currency', $currency);
        
        $params =  Secretary\Application::parameters();
        $this->setState('params', $params);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getStoreId()
     */
    protected function getStoreId($id = '')
	{
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('start_date');
		$id .= ':' . $this->getState('end_date');
        return parent::getStoreId($id);
    }
    
    /**
     * Method to get the items for view 
     */
    public function getListQuery()
    { 
        $db     = $this->getDbo();
		$query  = $db->getQuery(true);
		
		// Select 
		$query->select('a.id,a.subject,a.subjectid,a.nr,a.created,
                a.paid,a.deadline,a.created_by,a.currency,a.upload,
				a.createdEntry,a.subtotal,a.total,a.taxtotal,a.template,a.state,a.catid');
		$query->from($db->quoteName('#__secretary_documents','a'));
		
		// Join currency
		$query->select('cur.symbol as currencySymbol');
		$query->leftJoin('#__secretary_currencies AS cur ON a.currency = cur.currency');
		
		// Join status
		$query->select('s.title AS status_title,s.icon,s.description AS tooltip,s.class');
		$query->leftJoin($db->quoteName('#__secretary_status','s').' ON s.id = a.state');
				
		// Join contacts
		$query->select("CONCAT( c.firstname , ' ' , c.lastname ) AS contact_name, c.email");
		$query->leftJoin($db->quoteName('#__secretary_subjects','c')." ON c.id = a.subjectid");
		
		// Join categories
		$query->select('CASE WHEN LENGTH(d.alias) > 0 THEN d.alias ELSE d.title END AS category_title ');
		$query->leftJoin($db->qn('#__secretary_folders','d').' ON d.id = a.catid');
			
		// Join over the users for the checked out user
		$query->select("a.checked_out,a.checked_out_time,uc.name AS editor");
		$query->leftJoin($db->qn('#__users','uc')." ON uc.id=a.checked_out");
		
		$query->where($db->qn('a.business').' = ' . (int) $this->business['id']);
			
		// Filter by a single or group of folders.
		if (is_numeric($this->catId)) {
		    // Get children categories
		    $subCategories = \Secretary\Helpers\Folders::subCategories($this->catId);
		    if(is_array($subCategories)) $subCategories = implode(",", $subCategories);
		    $query->where($db->qn('a.catid').' IN (' . $subCategories . ')');
		}
		
		if (!empty($this->contact_ids)) { 
		    $query->where($db->qn('a.subjectid').' IN (' . implode(",", $this->contact_ids) . ')');
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
		    $query->where($db->qn('a.state').' = ' . (int) $published);
		}
		
		// Get Business Data
		$filterList	= $this->getState('params')->get('filterList');
		if($filterList == 1) {
			if ($start_date = $this->getState('start_date')) 
			    $query->where($db->qn('a.created').'>='.$db->quote($start_date));
			if ($end_date = $this->getState('end_date'))
			    $query->where($db->qn('a.created').'<='.$db->quote($end_date));
		}
		
		if(!empty($this->locationid)) {
		    $query->where($db->qn('a.office').'='. intval($this->locationid));
		}

		if(!empty($this->currencyId)) {
		    $query->where($db->qn('a.currency').'='. $db->quote($this->currencyId));
		}
		
		if($this->itemsFilter > 0) {
		    $subjectId = Secretary\Database::getQuery('subjects', $this->user->id,'created_by', 'id','loadResult');
		    $query->where($db->qn('a.subjectid').'='.intval($subjectId));
		}
		
		// Contacts or Customers see documents created for them
		$isAdmin = \Secretary\Helpers\Access::checkAdmin();
		$userContact = ($this->user->id > 0) ? Secretary\Database::getQuery('subjects',$this->user->id,'created_by') : (object) array('id'=>-1);
		if(!$isAdmin 
		    && (!$this->user->authorise('core.show.other', 'com_secretary.document') || !$this->user->authorise('core.show', 'com_secretary.document'))
		    && (!empty($userContact) && $userContact->id > 0)) {
	        $query->where($db->qn('a.subjectid').'='.intval($userContact->id));
	    }

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$searchText = $db->Quote('%' . $db->escape($search, true) . '%');
			$searchname = str_replace(" ","",strtolower($searchText)) ;
			$searchExp = ' LOWER(CONCAT(c.firstname,c.lastname)) LIKE ' . $searchname.' OR a.title LIKE '.$searchText;
			if(preg_match('/[0-9]+/', $search)) 
			    $searchExp .= ' OR a.nr LIKE '.$searchText;
			$query->where($searchExp);
		} 
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn) .',a.nr DESC');
		}
		
		return $query; 
    } 
    
    
    /**
     * Prepares the items and statistic
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getItems()
     */
    public function getItems()
    {
        if(!isset(self::$items)) {
            self::$items = parent::getItems();
        }
        
        $db		= \Secretary\Database::getDBO();
        $result = array();
         
        $userContact    = ($this->user->id > 0) ? Secretary\Database::getQuery('subjects',$this->user->id,'created_by') : (object) array('id'=>-1);
        $maxAmount      = 1; 
        
        foreach (self::$items as $item) {
            
            // START Permission Check
            $canSee = false; $item->canChange = false; $item->canCheckin = false; $item->canEdit = false;
            if($this->user->id == $item->created_by 
                || $this->user->authorise('core.show.other', 'com_secretary.document') 
                || (!empty($userContact) && $userContact->id == $item->subjectid))
            {
                $canSee = true;
                if( $this->user->authorise('core.edit.own', 'com_secretary.document.'.$item->id)
                    || $this->user->authorise('core.edit', 'com_secretary.document')) {
                $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
                }
            }
            
            if(!$canSee) $canSee = $this->user->authorise('core.show.other','com_secretary.document.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission Check
                        
            // Folder title
            $item->category_title = (empty($item->category_title)) ? JText::_('COM_SECRETARY_DOCUMENT') : JText::_($item->category_title);
           
            // Expired items, unpaid after due date
            if($item->paid != $item->total && ( $item->deadline > '1970-01-01') && ( $item->deadline < date('Y-m-d')) ) {
                $this->items_expired[] = $item;
            }
            
            // Maximum Value
            if($this->maxValue < $item->total)
                $this->maxValue = $item->total;
            
            $result[] = $item;
        } 
        
        return $result;
    }
    
    /**
     * Method to get the documents where the due date is expired
     * getItems() has to be called first
     * 
     * @return array expired documents
     */
    public function getItemsExpired()
    { 
		return $this->items_expired;
    }
    
    /**
     * Method to get summary report for selected filters
     * 
     * @return array[]|number[]
     */
    public function getSummary( )
    {
        $db		= \Secretary\Database::getDBO();
        $query  = $this->getListQuery();
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $totalSummaryItems  = 0;
        $summary            = array();
        
        foreach ($items as $item) {
            
            // START Permission Check
            $canSee = false; $item->canChange = false; $item->canCheckin = false; $item->canEdit = false;
            if($this->user->id == $item->created_by || ($this->user->authorise('core.show.other', 'com_secretary.document'))) {
                $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
            }
                
            if($this->itemsFilter > 0) $canSee = true;
            
            if(!$item->canCheckin) $item->canCheckin = $this->user->authorise('core.admin', 'com_secretary');
            if(!$item->canChange) $item->canChange = $this->user->authorise('core.edit.state', 'com_secretary.document');
            if(!$canSee) $canSee = $this->user->authorise('core.show.other','com_secretary.document.'.$item->id);
            
            if(!$canSee) continue;
            // END Permission Check
                
            // Summary report currency-dependant
            $currency_key = $item->currency.'_'.$item->state;
            if(!isset($summary[$currency_key])) {
                $totalSummaryItems++;
                $summary[$currency_key] = new stdClass();
                $summary[$currency_key]->state = $item->state;
                $summary[$currency_key]->status_title = $item->status_title;
                $summary[$currency_key]->class = $item->class;
                $summary[$currency_key]->total = $item->total;
                $summary[$currency_key]->paid = $item->paid;
                $summary[$currency_key]->currency = $item->currency;
                $summary[$currency_key]->currencySymbol = $item->currencySymbol;
            } else {
                $summary[$currency_key]->total += $item->total;
                $summary[$currency_key]->paid += $item->paid;
            }
            
            // Store all currencies
            if(!in_array($item->currency,$this->items_report['currencies']))
                $this->items_report['currencies'][] = $item->currency;
                     
        } 
        // Summary
        ksort($summary);
        $this->items_report['totat_amount'] = $totalSummaryItems;
        $this->items_report['data'] = array_values($summary);
        
        // Category title and alias
        if (!empty($this->catId) && is_numeric($this->catId)) {
            
            $db->setQuery('SELECT title,alias FROM '.$db->qn('#__secretary_folders') .'
					WHERE id = '.$db->escape($this->catId).' AND extension = '.$db->quote("documents"));
            
            $category	= $db->loadObject();
            if(!empty($category)) {
                $this->items_report['alias'] = JText::_($category->alias);
                $this->items_report['title'] = JText::_($category->title);
            }
        } else {
            $this->items_report['alias'] = JText::_('COM_SECRETARY_DOCUMENT') ;
            $this->items_report['title'] = JText::_('COM_SECRETARY_DOCUMENTS') ;
        }
        
        return $this->items_report;
    }
}
