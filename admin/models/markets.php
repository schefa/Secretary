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
 

class SecretaryModelMarkets extends JModelList
{
    protected $app;
    
    private $extension;
    private $catid;

	private $selectedColumns = array(
	    "Ask"=>false,
	    "AverageDailyVolume"=>false,
	    "Bid"=>false,
	    "BookValue"=>true,
	    "Change"=>false,
	    "LastTradeDate"=>false,
	    "EBITDA"=>false,
	    "EarningsShare"=>false,
	    "EPSEstimateCurrentYear"=>false,
	    "EPSEstimateNextYear"=>false,
	    "EPSEstimateNextQuarter"=>false,

	    "TwoHundreddayMovingAverage"=>false,
	    "ChangeFromTwoHundreddayMovingAverage"=>false,
	    "PercentChangeFromTwoHundreddayMovingAverage"=>false,
	    "FiftydayMovingAverage"=>false,
	    "ChangeFromFiftydayMovingAverage"=>false,
	    "PercentChangeFromFiftydayMovingAverage"=>false,
	    
	    "Open"=>false,
	    "PreviousClose"=>false,
	    "PriceSales"=>false,
	    "PriceBook"=>false,
	    "PEGRatio"=>false,
	    "PriceEPSEstimateCurrentYear"=>false,
	    "PriceEPSEstimateNextYear"=>false,
	    "ShortRatio"=>false,
	    "OneyrTargetPrice"=>false,
	    
	    "DaysLow"=>false,
	    "DaysHigh"=>false,
	    "YearLow"=>false,
	    "ChangeFromYearLow"=>false,
	    "PercentChangeFromYearLow"=>false,
	    "YearHigh"=>false,
	    "ChangeFromYearHigh"=>false,
	    "PercebtChangeFromYearHigh"=>false,
	    "MarketCapitalization"=>true,
	    "DaysRange"=>true,
	    "YearRange"=>true,
	    "Volume"=>true,
	);
	
	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'name', 'a.name',
                'ek_price', 'a.ek_price',
                'state', 'a.state',
                'Watchlist'
            );
        }
        
        $this->app          = \Secretary\Joomla::getApplication();
        $this->extension    = $this->app->input->getCmd('extension',''); 
		$this->catid = $this->app->input->getInt('catid',0); 
        parent::__construct($config);
    }
    
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
	{
        parent::populateState('a.name', 'asc');
        
        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $limit);
        
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
        $id .= ':' . $this->getState('filter.search');
        return parent::getStoreId($id);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
    protected function getListQuery()
	{
 
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

		try
		{
			$query->select( $this->getState( 'list.select', 'a.*' ) )
				->from($db->qn('#__secretary_markets','a'));
				
			// Join over the status.
			$query->select('s.title AS status_title,s.icon,s.description AS tooltip,s.class')
				->leftJoin($db->qn('#__secretary_status','s').' ON s.id = a.state');
					
			// Join over the users for the checked out user
			$query->select(" CASE WHEN a.catid = 0 THEN '-' ELSE c.title END AS Watchlist,c.created_by")
				->leftJoin($db->qn('#__secretary_folders','c').' ON c.id=a.catid');
			
			// Join over the users for the checked out user
			//$query->select("uc.name AS editor")->leftJoin("#__users AS uc ON uc.id=a.checked_out");
			
			// Filter by search in title
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where(' ( a.name LIKE ' . $search .') OR ( a.symbol LIKE ' . $search .')');
			}
			
			// Filter by a single or group of folders.
			$categoryId = $this->catid;
			if (is_numeric($categoryId))
			{
			    // Unterkategorien holen
			    $subCategories = \Secretary\Helpers\Folders::subCategories($categoryId);
			    if(is_array($subCategories)) $subCategories = implode(",", $subCategories);
			    $query->where('a.catid IN (' . $subCategories . ')');
			}
					
				
			// Filter by published state
			$published = $this->getState('filter.state');
			if (is_numeric($published)) {
				$query->where('a.state = ' . (int) $published);
			}
			
			if(!empty($this->extension)) 
				$query->where('a.extension ='. $db->quote($this->extension));
			
			//$query->where('c.extension ='. $db->quote("locations"));
			
			// Add the list ordering clause.
			$orderCol = $this->state->get('list.ordering');
			$orderDirn = $this->state->get('list.direction');
			if ($orderCol && $orderDirn) {
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}
		}
		catch(Exception $e) 
		{
		    $this->app->enqueueMessage($e->getMessage,'error');
		}
        return $query;
    }
    
    public function getChartData()
    {
        $term	= $this->app->input->getString('s');
        
        if(strlen($term) < 2)
            return array('labels'=>array(),'series'=>array());
        
        $model = JModelAdmin::getInstance('Market','SecretaryModel');
        $items = $model->getStockHistorical($term);
         
        $labels = array();
        $series = array();
        
        for ($x = count($items) - 1; $x > 0; $x--) {
            $labels[] = $items[$x]['Date'];
            $series[] = $items[$x]['Close'];
        }
        
        return array('labels'=>$labels,'series'=>$series);
    }
	
    public function getColumns()
    {
        $selectedCols	= $this->app->getUserState('filter.stockcolumns');
        $result         = array();
        
        if(!empty($selectedCols)) { 
            foreach($this->selectedColumns as $name => $value) {
                $result[$name] = (in_array($name,$selectedCols)) ? true : false; 
            }
        } else {
            $result = $this->selectedColumns;
        }
        return $result;
    }
}
