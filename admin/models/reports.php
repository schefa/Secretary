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

class SecretaryModelReports extends JModelItem
{
    
    protected $app;
    protected $context;
	protected $zeitraum;

	public $total = array();
	public $zeitraumoptions = array(
	    1 => 'COM_SECRETARY_TIMES_WEEKS_VIEW', 
	    2 => 'COM_SECRETARY_TIMES_MONTH_VIEW', 
	    3 => 'COM_SECRETARY_TIMES_YEARS_VIEW'
	);
	
	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array())
	{
		$this->context = 'com_secretary';
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'state', 'a.state',
                'created', 'a.created',
                'zeitraum', 'zeitraum',
            );
        }
        $this->app = \Secretary\Joomla::getApplication();
		
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ItemModel::getStoreId()
     */
    protected function getStoreId($id = '')
	{
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.docsstate');
        $id.= ':' . $this->getState('filter.docscurrency');
        $id.= ':' . $this->getState('filter.contstate');
        $id.= ':' . $this->getState('filter.zeitraum');
		$id.= ':' . $this->getState('filter.start_date');
		$id.= ':' . $this->getState('filter.end_date');
        return parent::getStoreId($id);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
	{
        $search = $this->app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $docsstate = $this->app->getUserStateFromRequest($this->context . '.filter.docsstate', 'filter_docs_state', '', 'string');
        $this->setState('filter.docsstate', $docsstate);
        
        $docscurrency = $this->app->getUserStateFromRequest($this->context . '.filter.docscurrency', 'filter_docs_currency', '', 'string');
        $this->setState('filter.docscurrency', $docscurrency);

        $contstate = $this->app->getUserStateFromRequest($this->context . '.filter.contstate', 'filter_cont_state', '', 'string');
        $this->setState('filter.contstate', $contstate);

        $prodStates = $this->app->getUserStateFromRequest($this->context.'.filter.prodStates','filter_prodStates');
        $this->setState('filter.prodStates', $prodStates);
        
        $prodBestseller = $this->app->getUserStateFromRequest($this->context.'.filter.prodBestseller','filter_prodBestseller');
        $this->setState('filter.prodBestseller', $prodBestseller);
		
        $zeitraum = $this->app->getUserStateFromRequest($this->context . '.filter.zeitraum', 'filter_zeitraum', '', 'string');
        $this->setState('filter.zeitraum', $zeitraum);

		$start_date = $this->app->getUserStateFromRequest($this->context . '.reports_start_date', 'reports_start_date');
		$this->setState('reports_start_date', $start_date);
		
		$end_date = $this->app->getUserStateFromRequest($this->context . '.reports_end_date', 'reports_end_date');
		$this->setState('reports_end_date', $end_date);
		
		if($start_date > $end_date) $this->setState('reports_start_date', $end_date);
		
		if(empty($start_date)) $this->setState('reports_start_date', date("Y").'-01-01');
		if(empty($end_date)) $this->setState('reports_end_date', date("Y-m-d"));
		
        parent::populateState('a.created', 'ASC');
    }
	
	private function dateDiffWeeks($interval, $datefrom, $dateto, $using_timestamps = false)
	{
		/*
		$interval can be:
		yyyy - Number of full years
		q - Number of full quarters
		m - Number of full months
		y - Difference between day numbers
		(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		d - Number of full days
		w - Number of full weekdays
		ww - Number of full weeks
		h - Number of full hours
		n - Number of full minutes
		s - Number of full seconds (default)
		*/
		if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		}
		$difference = $dateto - $datefrom; // Difference in seconds
		switch($interval) {
			case 'yyyy': // Number of full years
				$years_difference = floor($difference / 31536000);
				if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
				$years_difference--;
				}
				if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
				$years_difference++;
				}
				$datediff = $years_difference;
				break;
			case "q": // Number of full quarters
				$quarters_difference = floor($difference / 8035200);
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
				}
				$quarters_difference--;
				$datediff = $quarters_difference;
				break;
			case "m": // Number of full months
				$months_difference = floor($difference / 2678400);
				while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
				}
				$months_difference--;
				$datediff = $months_difference;
				break;
			case 'y': // Difference between day numbers
				$datediff = date("z", $dateto) - date("z", $datefrom);
				break;
			case "d": // Number of full days
				$datediff = floor($difference / 86400);
				break;
			case "w": // Number of full weekdays
				$days_difference = floor($difference / 86400);
				$weeks_difference = floor($days_difference / 7); // Complete weeks
				$first_day = date("w", $datefrom);
				$days_remainder = floor($days_difference % 7);
				$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
				if ($odd_days > 7) { // Sunday
				$days_remainder--;
				}
				if ($odd_days > 6) { // Saturday
				$days_remainder--;
				}
				$datediff = ($weeks_difference * 5) + $days_remainder;
				break;
			case "ww": // Number of full weeks
				$datediff = floor($difference / 604800);
				break;
			case "h": // Number of full hours
				$datediff = floor($difference / 3600);
				break;
			case "n": // Number of full minutes
				$datediff = floor($difference / 60);
				break;
			default: // Number of full seconds (default)
				$datediff = $difference;
				break;
		}
		return $datediff;
	}
	
    public function getProductsGrowth( array $business )
	{
		 
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);

        $query->select('p.year,p.title,p.total,p.catid,f.title as category')
        		->from($db->quoteName('#__secretary_products','p'));
        	
        $query->leftJoin($db->quoteName('#__secretary_folders','f').' ON f.id = p.catid');
        
        $query->where('p.business = ' . (int) $business['id']);
    
        $published = $this->getState('filter.prodStates');
        if (is_numeric($published)) {
            $query->where('p.state = ' . (int) $published);
        }
        $query->order('p.total DESC');

        try
        {
            $limit = $this->getState('filter.prodBestseller',10);
            $db->setQuery($query,0,$limit);
            $items = $db->loadObjectList();
            	
            $tmp = array(
                'growth'=>array(
                    'series'=>array(),
                    'labels'=>array()
                ),
                'total'=>0
            );

            $i = 0;
            for ($x = 0; $x < count($items); $x++ ) {
 
                $tmp['growth']['labels'][$x][0] = $items[$x]->title;
                $tmp['growth']['classes'][$x]   = "secretary-piechart-section section-".$i;
                
                if(!isset($tmp['growth']['series'][$x])) {
                    $tmp['growth']['series'][$x] = round($items[$x]->total,2);
                } 
                	
                $tmp['total'] += $items[$x]->total;
                if($i >= 7) $i = 0; else $i++;
            }
            $tmp['total'] = Secretary\Utilities\Number::getNumberFormat($tmp['total']);
            array_values($tmp['growth']['labels']);
            	
            return $tmp;
        }
        catch (Exception $e)
        {
            $this->app->enqueueMessage($e->getMessage(),'error');
        }
        
    } 
    
    public function getContactsGrowth( array $business )
    {
     
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);
        
        $yearStr = (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(YEAR FROM s.created)' : 'YEAR(s.created)';
        $weekStr = (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(WEEK FROM s.created)' : 'WEEK(s.created)';
        $monthStr= (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(MONTH FROM s.created)' : 'MONTH(s.created)'; 

        $query->select('s.created,'.$yearStr.' AS year,s.gender,s.state,s.catid,s.zip,s.location,f.title as category')
		->from($db->quoteName('#__secretary_subjects','s'));

		$query->select('status.title AS statusTitle,status.class AS cssClass')
			->leftJoin($db->quoteName('#__secretary_status','status').' ON status.id = s.state');
				
		$query->leftJoin($db->quoteName('#__secretary_folders','f'). ' ON f.id = s.catid');
			
		// Filter by Zeitraum
		$zeitraum = $this->getState('filter.zeitraum');
		switch ($zeitraum) {
			default : case 1 : $query->select($weekStr.' AS zeitraum'); break;
		    case 2 : $query->select($monthStr.' AS zeitraum'); break;
		    case 3 : $query->select($yearStr.' AS zeitraum'); break;
		}
		
		$query->where('s.business = ' . (int) $business['id']);
	
		// Filter by published state
		$published = $this->getState('filter.contstate');
		if (is_numeric($published)) {
			$query->where('s.state = ' . (int) $published);
		}
		
		if ($start_date = $this->getState('reports_start_date'))
			$query->where('s.created >='.$db->quote($start_date));
			
		if ($end_date = $this->getState('reports_end_date'))
			$query->where('s.created <='.$db->quote($end_date));
		
		// BUG: wenn keine fetch dann wird kein Zeitraum gespeichert, so entstehen Löcher
		$query->order('s.created ASC');
			
		try
		{
			$db->setQuery($query);
			$items = $db->loadObjectList();

			$tmp = array(
			    'genders'=>array(
			        'series'=>array(),
			        'labels'=>array(),
			        'classes'=>array()
			    ),
			    'location'=>array(
			        'series'=>array(),
			        'labels'=>array(),
			        'classes'=>array()
			    ),
			    'growth'=>array(
			        'series'=>array(),
			        'labels'=>array()
			    ),
			    'categories'=>array(
			        'series'=>array(),
			        'labels'=>array(),
			        'classes'=>array()
			    ),
			    'total'=>0
			);
			 
			$a = 0;
			$b = 0;
			$c = 0;
			for ($x = 0; $x < count($items); $x++ ) {
			    // Categories
			    if(!isset($tmp['categories']['series'][$items[$x]->catid])) {
			        $tmp['categories']['series'][$items[$x]->catid] = 0;
			        if(strlen($items[$x]->category) <= 1) $items[$x]->category = 'COM_SECRETARY_NONE';
			        $tmp['categories']['labels'][$items[$x]->catid] = JText::_( $items[$x]->category );
			        $tmp['categories']['classes'][$items[$x]->catid] =  "secretary-piechart-section section-".$a;
			        if($a >= 7) $a = 0; else $a++;
			    }
			    $tmp['categories']['series'][$items[$x]->catid] += 1;

			    // Location
			    $zip = '"'. $items[$x]->zip .'"';
			    if(!isset($tmp['location']['series'][$zip])) {
			        
			        if($b >= 7) $b = 0; else $b++;
			        $tmp['location']['series'][$zip] = 0;
			        if(strlen($zip) <= 2) 
			            $tmp['location']['labels'][$zip] = JText::_('COM_SECRETARY_NONE');
			        else
    			        $tmp['location']['labels'][$zip] =  ( $zip .' '.$items[$x]->location ) ;
			        $tmp['location']['classes'][$zip] =  "secretary-piechart-section section-".$b;
			    }
			    $tmp['location']['series'][$zip] += 1;

			    // Gender
			    if(!isset($tmp['genders']['series'][$items[$x]->gender])) {
			        $tmp['genders']['series'][$items[$x]->gender] = 0;
			        $title = Secretary\Utilities::getGender( $items[$x]->gender );
			        $tmp['genders']['labels'][$items[$x]->gender] = (strlen($title)>0) ? $title : JText::_('COM_SECRETARY_NONE');
			        $tmp['genders']['classes'][$items[$x]->gender] =  "secretary-piechart-section section-".$c;
			        if($c >= 7) $c = 0; else $c++;
			    }
			    $tmp['genders']['series'][$items[$x]->gender] += 1;
			
			    // Growth
	            $zeitraumKey = $items[$x]->year.':'.str_pad($items[$x]->zeitraum,2,0,STR_PAD_LEFT);
			    if ($zeitraum == 2) {
			        $dateObj   = DateTime::createFromFormat('!m', $items[$x]->zeitraum);
			        $tmp['growth']['labels'][$zeitraumKey][0] = $dateObj->format('F');
			        $tmp['growth']['labels'][$zeitraumKey][1] = $items[$x]->zeitraum .' / '. $items[$x]->year;
			    } elseif ($zeitraum == 3) {
			        $tmp['growth']['labels'][$zeitraumKey][0] = $items[$x]->zeitraum;
			        $tmp['growth']['labels'][$zeitraumKey][1] = JText::_('COM_SECRETARY_YEAR') .': '. $items[$x]->zeitraum;
			    } else {
			        $weekHTML = array();
			        $week_start = new DateTime();
			        $week_start->setISODate($items[$x]->year,$items[$x]->zeitraum);
			        $week_end = new DateTime();
			        $week_end->setISODate($items[$x]->year,$items[$x]->zeitraum+1,0);
			        $weekHTML[] = $week_start->format('d.m.') . ' - ' ;
			        $weekHTML[] = $week_end->format('d.m.');
			        $week = implode('',$weekHTML);
			        $tmp['growth']['labels'][$zeitraumKey][0] = $items[$x]->zeitraum;
			        $tmp['growth']['labels'][$zeitraumKey][1] = $week . $items[$x]->year;
			    }
			    
			    $statusTitle = JText::_($items[$x]->statusTitle);
			    if(!isset($tmp['growth']['series'][$zeitraumKey][$statusTitle])) {
			        $tmp['growth']['series'][$zeitraumKey][$statusTitle] = 0;
			    }
			    $tmp['growth']['series'][$zeitraumKey][$statusTitle] += 1;
			
			    $tmp['total']++;
			}
			
			array_multisort($tmp['categories']['series'], SORT_NUMERIC, SORT_DESC, $tmp['categories']['labels'],$tmp['categories']['classes']);
			array_multisort($tmp['genders']['series'], SORT_NUMERIC, SORT_DESC, $tmp['genders']['labels'], $tmp['genders']['classes']);
			array_multisort($tmp['location']['series'], SORT_NUMERIC, SORT_DESC, $tmp['location']['labels'], $tmp['location']['classes']);
			
			return $tmp;
		}
		catch (Exception $e) 
		{
			$this->app->enqueueMessage($e->getMessage(),'error');
		}
		
    }
	
    public function getStats( array $business, $guv = NULL  )
	{ 
        $db		= JFactory::getDbo();
        $query	= $db->getQuery(true);
		
		if (!empty($guv)) 
		{
			try
			{
				
			    $yearStr = (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(YEAR FROM a.created)' : 'YEAR(a.created)';
			    $weekStr = (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(WEEK FROM a.created)' : 'WEEK(a.created)';
			    $monthStr= (Secretary\Database::getDbType() == 'postgresql') ? 'EXTRACT(MONTH FROM a.created)' : 'MONTH(a.created)';
				
				$query->select($yearStr.' AS year,a.created,a.state,a.total,a.subtotal,a.currency');
				
				// Filter by Zeitraum
				$zeitraum = $this->getState('filter.zeitraum');
				switch ($zeitraum) {
					default : case 1 : $query->select($weekStr.' AS zeitraum'); break;
					case 2 : $query->select($monthStr.' AS zeitraum'); break;
					case 3 : $query->select($yearStr.' AS zeitraum'); break;
				}
				
				$query->from($db->quoteName("#__secretary_documents","a"));
				
				$query->select('s.title AS status,s.class AS cssClass')
					->leftJoin($db->quoteName("#__secretary_status","s").' ON s.id = a.state')
					// Keine stornierten
					->where("s.class != ".$db->quote('canceled'));
				
				$query->select('c.parent_id');
				$query->leftJoin($db->quoteName("#__secretary_folders","c").' ON c.id = a.catid');
				$query->where('a.business = ' . (int) $business['id']);
				$query->where('c.extension = '.$db->quote("documents"));
				
				JArrayHelper::toInteger($guv);
				$guv = implode(',', $guv);
				$query->where(' (a.catid IN ('.$db->escape($guv).') OR c.parent_id IN ('.$db->escape($guv).'))');
				
				if ($start_date = $this->getState('reports_start_date')) 
					$query->where('a.created >='.$db->quote($start_date));
					
				if ($end_date = $this->getState('reports_end_date'))
					$query->where('a.created <='.$db->quote($end_date));

				// Filter by published state
				$published = $this->getState('filter.docsstate');
				if (is_numeric($published) && $published > 0) {
				    $query->where('a.state = ' . (int) $published);
				}

				// Filter by published state
				$docscurrency = $this->getState('filter.docscurrency');
				if (!empty($docscurrency)) {
				    $query->where('a.currency = ' . $db->quote($docscurrency));
				}
				
				$orderBY = 's.ordering,zeitraum ASC';
				$orderCol = $this->getState('list.ordering');
				if(!empty($orderCol))
				    $orderBY = $orderCol .','. $orderBY;
				$query->order($orderBY);
				
				$db->setQuery($query);
				$items = $db->loadObjectList();
				
				return $items;
				
			}
			catch (Exception $e) 
			{
			    $this->app->enqueueMessage($e->getMessage(),'error');
			}
		}
		
		return false;
		
    }

    public function getStates( $extension = 'documents' )
    {
        if($extension === 'documents')
            $state = $this->state->get('filter.docsstate');
        if($extension === 'subjects')
            $state = $this->state->get('filter.contStates');
        if($extension === 'products')
            $state = $this->state->get('filter.prodStates');
             
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select("id AS value,title AS text,class")
            ->from($db->quoteName("#__secretary_status"))
            ->where($db->quoteName('extension').' = '. $db->quote($extension));

        if(!empty($state))
            $query->where('id = '.intval($state));

        $query->where("class != ".$db->quote('canceled'))
        ->order('ordering ASC, id ASC');

        $db->setQuery($query);
        $states = $db->loadObjectList();

        return $states;
    }
    
    public function rebuildDocumentsItems($incomesObj = false,$costsObj = false) {

        // Merge Income + Costs
        ($incomesObj) ? $incomeCount	= count($incomesObj) : $incomeCount = 0;
        ($costsObj) ? $costsCount		= count($costsObj) : $costsCount = 0;
        $count    = ($incomeCount > $costsCount) ? $incomeCount : $costsCount;
        
        $items = array('data'=>array(),'states'=>array(),'currency'=>array(),'tops'=>0);
        
        /* Poetry */
        for($i = 0; $i < $count; $i++)
        {
            // Income
            if(isset($incomesObj[$i]->zeitraum)) {
                $this->remakeItem( $items, $i, 'i', $incomesObj);
            }
        
            // Costs
            if(isset($costsObj[$i]->zeitraum)) {
                $this->remakeItem( $items, $i, 'c', $costsObj);
            }
        
            if(isset($incomesObj[$i]->currency) && !isset($this->total[$incomesObj[$i]->currency])) {
                $this->total[$incomesObj[$i]->currency] = array(
                    'einnahmen' => 0,
                    'einnahmenNetto' => 0,
                    'einnahmenUmst' => 0,
                    'ausgaben' => 0,
                    'ausgabenNetto' => 0,
                    'ausgabenUmst' => 0,
                );
            }  
                	
            if(isset($incomesObj[$i]->total)) {
                $this->total[$incomesObj[$i]->currency]['einnahmen']		+= $incomesObj[$i]->total;
                $this->total[$incomesObj[$i]->currency]['einnahmenNetto']	+= $incomesObj[$i]->subtotal;
                $this->total[$incomesObj[$i]->currency]['einnahmenUmst']	+= ($incomesObj[$i]->total - $incomesObj[$i]->subtotal);
    
                // Höchstwert
                if($incomesObj[$i]->total > $items['tops'])
                    $items['tops'] = $incomesObj[$i]->total;
    
                    if(!in_array($incomesObj[$i]->currency, $items['currency']))
                        $items['currency'][] = $incomesObj[$i]->currency;
            }
            	
            if(isset($costsObj[$i]->currency) && !isset($this->total[$costsObj[$i]->currency])) {
                $this->total[$costsObj[$i]->currency] = array(
                    'einnahmen' => 0,
                    'einnahmenNetto' => 0,
                    'einnahmenUmst' => 0,
                    'ausgaben' => 0,
                    'ausgabenNetto' => 0,
                    'ausgabenUmst' => 0,
                );
            }
            
            if(!empty($costsObj[$i]->total)) {
                $this->total[$costsObj[$i]->currency]['ausgaben']		+= $costsObj[$i]->total;
                $this->total[$costsObj[$i]->currency]['ausgabenNetto']	+= $costsObj[$i]->subtotal;
                $this->total[$costsObj[$i]->currency]['ausgabenUmst']	+= ($costsObj[$i]->total - $costsObj[$i]->subtotal);
    
                // Highest value
                if($costsObj[$i]->total > $items['tops']) {
                    $items['tops'] = $costsObj[$i]->total;
                }
            
                if(!in_array($costsObj[$i]->currency, $items['currency'])) {
                    $items['currency'][] = $costsObj[$i]->currency;
                }
            }
                    	
        }
        
        // Fehlende Zeiträume ergänzen
        $last_key = key( array_slice( $items, -1, 1, TRUE ) );
        foreach ($items as $i => $item) {
            if(is_numeric($i) && ($i !== $last_key) && (!empty($items[$i]) && empty($items[$i+1])) ) {
                $items[$i+1] = array( 'year' => $items[$i]['year'] );
            }
        }
        
        ksort($items['data']);
        
        return $items;
    }
    
    private function remakeItem(&$newItems, $i, $type, $item) {
        //echo $item[$i]->state;
        $zeitraum = $item[$i]->year.':'.str_pad($item[$i]->zeitraum,2,0,STR_PAD_LEFT);
        $currency = $item[$i]->currency;
         
        if(!isset($newItems['data'][$zeitraum])) {
            $newItems['data'][$zeitraum] = array('year'=>$item[$i]->year, $type => array($currency=>array()));
        }
        if(!isset($newItems['data'][$zeitraum][$type][$currency][$item[$i]->state])) {
            $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state]['css'] = $item[$i]->cssClass;
            $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state][0] = JText::_($item[$i]->status);
            $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state][1] = 0;
            $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state][2] = 0;
            if(!in_array($item[$i]->state, $newItems['states'])) {
                $newItems['states'][$item[$i]->state] = $item[$i]->state;
            }
        }
        $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state][1] += $item[$i]->total;
        $newItems['data'][$zeitraum][$type][$currency][$item[$i]->state][2] += $item[$i]->subtotal;
    
        if(!isset($newItems['data'][$zeitraum][$type][$currency]['total']))
            $newItems['data'][$zeitraum][$type][$currency]['total'] = 0;
            $newItems['data'][$zeitraum][$type][$currency]['total'] += $item[$i]->total;
    }
    
}
