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

class SecretaryModelTimes extends JModelList
{
    
    protected $app;
    private $business;
	private $extension;
	private static $_startHoursOffset = 6;

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array())
	{
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'time_title',
                'startDate', 
                'endDate', 
                'ordering',
            );
        }
        
        $this->app       = \Secretary\Joomla::getApplication();
		$this->business  = \Secretary\Application::company();
		$this->extension = $this->app->input->getCmd('extension');
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
     */
    protected function populateState($ordering = null, $direction = null)
	{
    	parent::populateState('ordering', 'asc');
        
        $dir = $this->app->getUserStateFromRequest($this->context . '.list.direction', 'filter_order_Dir', 'asc', 'word');
        $this->setState('list.direction', (($dir != 'asc') ? 'desc' : $dir));

        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
        $this->setState('list.limit', $limit);

        $published = $this->app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
		$categoryId = $this->app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category');
		$this->setState('filter.category', $categoryId);
		
        $search = $this->app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
		
        $day = $this->app->getUserStateFromRequest($this->context . '.filter.day', 'filter_day');
        $this->setState('filter.day', $day);
		
        $month = $this->app->getUserStateFromRequest($this->context . '.filter.month', 'filter_month');
        $this->setState('filter.month', $month);
		
        $week = $this->app->getUserStateFromRequest($this->context . '.filter.week', 'filter_week');
        $this->setState('filter.week', $week);
		
        $listscale = $this->app->getUserStateFromRequest($this->context . '.filter.scale', 'filter_scale');
        $listpast = $this->app->getUserStateFromRequest($this->context . '.filter.past', 'filter_past');
        $listfuture = $this->app->getUserStateFromRequest($this->context . '.filter.future', 'filter_future');
		
		if( empty($listpast)  && empty($listscale) && empty($listfuture) ) {
			$listpast = 1;
			$listscale = "days";
			$listfuture = 3;
		} else {
			if ($listpast < 0) $listpast = 0;
			if ($listfuture < 0) $listfuture = 0;
		}
		
        $this->setState('filter.past', $listpast);
        $this->setState('filter.scale', $listscale);
        $this->setState('filter.future', $listfuture);
		
        $params = Secretary\Application::parameters();
        $this->setState('params', $params);
		
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getStoreId()
     */
    protected function getStoreId($id = '')
	{
		$id.= ':' . $this->getState('filter.category');
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.day');
        $id.= ':' . $this->getState('filter.month');
        $id.= ':' . $this->getState('filter.week');
        $id.= ':' . $this->getState('filter.scale');
        $id.= ':' . $this->getState('filter.past');
        $id.= ':' . $this->getState('filter.future');
        return parent::getStoreId($id);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
     */
	protected function getListQuery()
	{
        $db		= \Secretary\Database::getDBO();
        $query	= $db->getQuery(true);

        $wheres = array();
        $where1 = array();
        $where2 = array();
		
        $countTasks = 'select COUNT(*) from #__secretary_tasks where '.$db->qn('projectID').' = time.id';
        
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$where1[] = ' ( time.title LIKE ' . $search .')';
			$where2[] = ' ( time.title LIKE ' . $search .') OR ( task.title LIKE ' . $search .')';
        }
        
		// Filter by published state
		$published = $this->getState('filter.state'); 
		if (is_numeric($published)) {
			if(count($where1) > 0) {
				$where1[] = ' AND ';
				$where2[] = ' AND ';
			}
			$where1[] = ' (time.state = ' . (int) $published.' )';
			$where2[] = ' (task.state = ' . (int) $published.' )';
		}
			
        // Filter by search in title
        $month = $this->getState('filter.month');
        $day = $this->getState('filter.day');
        /*if (!empty($month) || !empty($day)) {
			if(empty($month)) { $month = date('m');} else { $month = str_pad($month, 2 ,'0', STR_PAD_LEFT); }
			if(empty($day)) { $day = date('d');} else { $day = str_pad($day, 2 ,'0', STR_PAD_LEFT); }
			$date = $db->quote( date('Y').'-'. $month ."-". $day."%");
			if(count($where1) > 0) {
				$where1[] = ' AND ';
				$where2[] = ' AND ';
			}
			$where1[] = ' (time.endDate >= '. $date.' )';
			$where2[] = ' (time.endDate >= '. $date.' )';
		}*/
		
		// Filter by a single or group of folders.
		$categoryId = $this->getState('filter.category');
		if (!empty($categoryId)) {
			if(count($where1) > 0) {
				$where1[] = ' AND ';
				$where2[] = ' AND ';
			}
			$where1[] = ' time.catid IN (' . $categoryId . ')'; 
			$where2[] = ' time.catid IN (' . $categoryId . ')'; 
		}
		
		if(!empty($this->extension)) {
			$additionalExtension = ($this->extension === 'tasks') ? ',"projects"' :'';
			if(count($where1) > 0) {
				$where1[] = ' AND ';
				$where2[] = ' AND ';
			}
			$where1[] = ' time.extension IN ('.$db->quote($this->extension).$additionalExtension.')'; 
			$where2[] = ' time.extension IN ('.$db->quote($this->extension).$additionalExtension.')';
		}

		if(count($where1) > 0) {
			$where1 = array_merge(array('WHERE ',implode($where1)));
			$where2 = array_merge(array('WHERE ',implode($where2)));
		}
		
		
        $db		= \Secretary\Database::getDBO();
        if(Secretary\Database::getDbType() == 'postgresql') {
        	$timeOrderingStr = "  time.ordering || '_' ||  0 ";
	        $taskOrderingStr= " time.ordering || '_' || lpad( CAST(task.ordering AS CHAR) , 3,'0') ";
        } else {
        	$timeOrderingStr = " CONCAT( time.ordering ,'_',  0)";
        	$taskOrderingStr= " CONCAT( time.ordering,'_', lpad(task.ordering , 3,'0') )";
        }
        
        $sql = "(SELECT
				time.id as time_id, ". $timeOrderingStr ." as ordering,time.extension,".$db->qn('time.startDate').','.$db->qn('time.endDate').',time.created_by,
        				time.fields as fields, time.title as time_title,
				null as task_id, null as parent_id, null as task_title,time.contacts as contacts,('.$countTasks.') as tasks_count,(
        				select SUM(totaltime) from #__secretary_tasks where '.$db->qn('projectID').' = time.id
        				) as worktime,null as totaltime,null as calctime,null as level,null as progress
				FROM #__secretary_times time 
        		'.implode($where1)."
				)
				UNION
				(SELECT
				time.id as time_id,".$taskOrderingStr." as ordering,time.extension,".$db->qn('task.startDate').",".$db->qn('task.endDate').",time.created_by,
        				task.fields as fields, time.title as time_title,
				task.id as task_id,".$db->qn('task.parentID')." as parent_id, task.title as task_title,task.contacts as contacts,(".$countTasks.') as tasks_count, 
						null as worktime,task.totaltime as totaltime,task.calctime as calctime,task.level as level,task.progress as progress
				FROM #__secretary_times time
				RIGHT JOIN #__secretary_tasks task ON ('.$db->qn('task.projectID').'  = time.id)
        		'.implode($where2).'
				)
		';
        
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering','ordering');
        $orderDirn = $this->state->get('list.direction','ASC');
        if ($orderCol && $orderDirn) {
        	$sql .= ' ORDER BY '. $db->escape($orderCol . ' ' . $orderDirn); 
        }
        
        return $sql;
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
		
		if(!empty($items)) {
				
			// ExtraRows
			$countItems =0;
			$countSubTasks = array();
			foreach($items as $x => $item)
			{
			    
			    // START Permission
			    $canSee = false; $item->canChange = false; $item->canCheckin = false; $item->canEdit = false;
			    if(($user->id == $item->created_by && $user->authorise('core.edit.own', 'com_secretary.time'))
		        || $user->authorise('core.edit', 'com_secretary.time')) {
		            $canSee = true; $item->canEdit = true; $item->canChange	= true; $item->canCheckin = true;
		        }
		        
		        if(!$item->canCheckin) $item->canCheckin = $user->authorise('core.admin', 'com_secretary');
		        if(!$item->canChange) $item->canChange = $user->authorise('core.edit.state', 'com_secretary.time');
		        if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.time.'.$item->time_id);
		        
		        if(!$canSee) {
		            unset($items[$x]);
		            continue;
		        }
		        // END Permission
			    
				if(!isset($item->time_id)) {
					unset($items[$x]);
					continue;
				}
				
				if($item->time_id > 0 && (is_null($item->task_id)))
				$countItems++;
								
				// Attendees
				if(!empty($item->contacts)) {
					$contacts = json_decode($item->contacts);
					$itemcontacts = array();
					foreach($contacts as $contact) {
						if(is_object($contact)) {
						    $dat = Secretary\Database::getQuery('subjects', $contact->id);
							if(!empty($dat->firstname) || !empty($dat->lastname)) 
								$itemcontacts[] = $dat->firstname . ' '. $dat->lastname;
						}
					}
					$item->contactsNames = implode(", ",$itemcontacts);
					
				}
				
				$item->additionalTitle = '';
				if($item->extension == 'locations_products' || $item->extension == 'locations_subjects')
				{
					$item->additionalTitle = '<a href="index.php?option=com_secretary&task=time.add&extension='.$item->extension.'&pid='. $item->id .'" class="secretary-time-title-add hasTooltip btn btn-small" title="'.JText::_('COM_SECRETARY_PROJECT_NEW_TASK').'"><i class="fa fa-plus"></i></a>';
					$item->timeColor = '';
				} 
				elseif($item->extension == 'projects')
				{	
					$item->timeColor = \Secretary\Helpers\Times::getTimeColor($item->fields);
					$item->tasks = array('count'=>0);
					
					if( $user->authorise('core.create','com_secretary.time')) {
						$item->additionalTitle .= '<a href="index.php?option=com_secretary&task=time.add&extension=tasks&pid='. $item->time_id .'" class="secretary-time-title-add hasTooltip btn btn-small" title="'.JText::_('COM_SECRETARY_PROJECT_NEW_TASK').'"><i class="fa fa-plus"></i></a>';
					}
					$item->additionalTitle .= '<div class="btn-group pull-right">
                        <div class="btn"><i class="fa fa-clock-o"></i>
                        </div>
                        <div class="btn">'. \Secretary\Helpers\Times::secondsToWorktime($item->worktime, 'h') .'</div>
                    </div>';
					
					$countSubTasks[$item->time_id] = $item->tasks_count;
					//unset($item->tasks_count);
				}
				else 
				{
					$item->timeColor = '';
				}
				
				
				$result[] = $item;
			}
			
			$items = array_values($result);
			
			if(!empty($items)) {
				$items[0]->countItems = $countItems;
				$items[0]->countTasks = array_sum($countSubTasks);
			}
			
		} 
		
		return $items;
    }

	/**
	 * Month Items
	 * 
	 * @param number $month
	 * @param number $year
	 * @param boolean $setitems
	 * @return mixed[] 
	 */
	public function getMonthItems( $month, $year, $setitems = false )
	{
		$result = array();
		
		$result['running_day'] = date('w',mktime(0,0,0,$month,1,$year));
		$result['days_in_month'] = date('t',mktime(0,0,0,$month,1,$year));
		$result['days_in_this_week'] = 1;
		$result['day_counter'] = 0;
		
		if($setitems) {
			
			// First Day of Month
			$startDay = $year .'-'.$month.'-01';
			
			// Last Day of Month
			$endDay = date('Y-m-t', strtotime($startDay));
			
			// Create a new query object.
			$db		= \Secretary\Database::getDBO();
			$query	= $db->getQuery(true);
			
			$query->select($db->qn(array("id","extension","title","startDate","endDate","fields","created_by")))
					->from($db->qn("#__secretary_times"))
					->where("
						(".$db->qn("startDate")." BETWEEN '".$startDay." 00:00:00' AND '".$endDay." 23:59:59') OR 
						(".$db->qn("endDate")." BETWEEN '".$startDay." 00:00:00' AND '".$endDay." 23:59:59') OR
						(". $db->quote($startDay)."
							BETWEEN 
								( SELECT ".$db->qn("startDate")." FROM #__secretary_times WHERE ".$db->qn("startDate")." <= ".$db->quote($startDay)." ORDER BY ".$db->qn("startDate")." ASC LIMIT 1) 
							AND 
								( SELECT ".$db->qn("endDate")." FROM #__secretary_times WHERE ".$db->qn("endDate")." >= ".$db->quote($endDay)." ORDER BY ".$db->qn("endDate")."  DESC LIMIT 1)
						)
					");
					
			if(!empty($this->extension)) {
				$query->where('extension ='.$db->quote($this->extension));
			}	
			
			$query->order($db->qn("startDate").' ASC');
			
			$db->setQuery($query);
			$events = $db->loadObjectList();
			/*
			$numberOfDays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
			$items = array();
			for($i = 1; $i <= $numberOfDays; $i++) {
				
			}
			
			foreach($events as $event) {
				$start = explode(" ",$event->startDate);
				$ende = explode(" ",$event->endDate);
				$items[$start[0].':'.$ende[0]] = $event;
			}
			*/	
			$result['items'] = $events;
		}
		
		return $result;
	}

	/**
	 * Week Items
	 * 
	 * @param number $week
	 * @param number $year
	 * @return array|stdClass
	 */
	public function getWeekItems( $week, $year )
	{ 
		$db		= \Secretary\Database::getDBO();
		$user	= \Secretary\Joomla::getUser();
		
		// First Day of Week
		$days	= date('d', strtotime($year."W".$week. 1));
		$months	= date('m', strtotime($year."W".$week. 1));
		$startWeekDay = $year .'-'.$months.'-'.$days;
		
		// Last Day of Week
		$days	= date('d', strtotime($year."W".$week. 7));
		$months	= date('m', strtotime($year."W".$week. 7));
		$endWeekDay = $year .'-'.$months.'-'.$days;
		
		$query	= $db->getQuery(true);
		$query->select("id,extension,title,startDate,endDate,fields,created_by");
		$query->from($db->qn("#__secretary_times"));
		if(!empty($this->extension)) {
			$query->where('extension ='.$db->quote($this->extension));
		}
		$query->where("(startDate BETWEEN '".$startWeekDay." 00:00:00' AND '".$endWeekDay." 23:59:59')
                    OR (endDate BETWEEN '".$startWeekDay." 00:00:00' AND '".$endWeekDay." 23:59:59')
                    OR (". $db->quote($startWeekDay)."
						BETWEEN 
                            ( SELECT startDate FROM #__secretary_times WHERE startDate <= ".$db->quote($startWeekDay)." ORDER BY startDate ASC LIMIT 1) 
						AND 
							( SELECT endDate FROM #__secretary_times WHERE endDate >= ".$db->quote($endWeekDay)." ORDER BY endDate DESC LIMIT 1)
					)");
		
		$query->order('startDate ASC');
		
		$db->setQuery($query);
		$events = $db->loadObjectList();
		
		$dates = array();
		if(!empty($events))
		{
			// Resort the Events, split events over days
			foreach($events as $eventKey => $event)
			{
				// Permission
				$canSee = false;
				if(($user->id == $event->created_by && $user->authorise('core.edit.own', 'com_secretary.time')) 
					|| $user->authorise('core.edit', 'com_secretary.time')) {
					$canSee = true;
				}
				if(!$canSee) $canSee = $user->authorise('core.show.other','com_secretary.time.'.$event->id);
				if(!$canSee) continue;
				
				$timeColor = \Secretary\Helpers\Times::getTimeColor($event->fields);
			
				// Precalculation
				$startDate = explode(" ",$event->startDate);
				$endDate = explode(" ",$event->endDate);
				if($endDate[0] < $startDate[0]) $endDate[0] = $startDate[0];
				if($startDate[0] == '0000-00-00 00:00:00') $startDate[0] = $endDate[0];
				$date1 = new DateTime($startDate[0]);
				$date2 = new DateTime($endDate[0]);
				$event->period = $date2->diff($date1)->format("%a");
				
				// Schleife solange durchlaufen bis der Zeitraum der Veranstaltung getestet wurde
				$currentDay = $startDate[0];
				for($abc = 0; $abc <= $event->period; $abc++)
				{
					
					// Ganztägige Veranstaltungen
					if($currentDay > $startDate[0]) {
						$newStartTime = "00:00:00";
					} else {
						$newStartTime = $startDate[1];
					}
					if($currentDay < $endDate[0]) {
						$newEndTime = "23:59:59";
					} else {
						$newEndTime = $endDate[1];
					}
					//////////
					
					// Bug mit bloßer Zuweisung von startDate und endDate,
					// weil Objectreferenz und die neuen Datum quasi immer neuer werden (keine Überschreibung in der Schleife)
					$splittedEvent = new stdClass();
					$splittedEvent->id = $event->id;
					$splittedEvent->title = $event->title;
					$splittedEvent->extension = $event->extension;
					$splittedEvent->timeColor = $timeColor;
					$splittedEvent->startTime = $newStartTime;
					$splittedEvent->endTime = $newEndTime;
					
					// Berechnungen für Balken
					$startHours = explode(":", $newStartTime ); 
					$startHours = $startHours[0] + ( $startHours[1] / 60 ) + ( $startHours[1] / 3600 ) - self::$_startHoursOffset;
					if($startHours < 0) { $startHours = 0; } else { $startHours = round($startHours,2); }
					$splittedEvent->startHours = $startHours;
					
					$endHours = explode(":", $newEndTime );
					$endHours = $endHours[0] + ( $endHours[1] / 60 ) + ( $endHours[1] / 3600 ) - self::$_startHoursOffset;
					$splittedEvent->endHours = round( $endHours - $startHours , 2);
					
					// Zusammenführen
					$dates[$currentDay][$eventKey] = $splittedEvent;
					
					// Nächstes Datum
					$currentDay = date("Y-m-d", strtotime("+1 day", strtotime( $currentDay )));
					
					// Hilfsvariable killen
					unset($splittedEvent);
					unset($newStartTime);
					unset($newEndTime);
				}
				unset($timeColor);
			}
			ksort($dates);
		}
		return $dates;
	} 
	
}
