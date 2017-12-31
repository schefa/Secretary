<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Helpers;
 
use Secretary\Exception;
use DateTime;
use JHtml;
use JObject;
use JRoute;
use JTable;
use JText;
use stdClass;

// No direct access
defined('_JEXEC') or die; 

class Times
{
	
	public static function secondsToWorktime($seconds, $format = 'h')
	{
		$hours = round( ($seconds / 3600) , 2);
		$result = $hours . ' h';
		return $result;
	}
	
	public static function getWeekOfDayMonth($day,$month,$year)
	{
		$ddate = $year.'-'.$month.'-'.$day;
		$date = new DateTime($ddate);
		return $date->format("W");
	}
	
	public static function getProjectStatus($itemID, $projectID)
	{
		$status =  '';
		$business	= \Secretary\Application::company('id');
		if(isset($itemID)) {
				
			// Project Timer
			$user = \Secretary\Joomla::getUser();
			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->qn(array('id','action','created')))
					->from($db->qn('#__secretary_activities'))
					->where($db->qn('extension').'= '.$db->quote("tasks"))
					->where($db->qn('created_by').'='.$db->escape($user->id))
					->where($db->qn('itemID').'='.$db->escape($itemID))
					->where($db->qn('catid').'='.$db->escape($projectID))
					->where($db->qn('business')."=".intval($business['id']))
					->where('('. $db->qn('action').' = '.$db->quote('start').' OR '.$db->qn('action').' = '.$db->quote('stop').')')
					->order('created DESC');
					
			$db->setQuery($query);
			$status = $db->loadObject();
			
		}
		
		if(empty($status)) {
			$status = new JObject;
			$status->id = 0;
			$status->action = 'stop';
			$status->created = NULL;
		}
		
		return $status;
	}
	
	public static function cleanRepetitions( $id = '', $created = NULL )
	{
		if(!empty($id)) {
			$db = \Secretary\Database::getDbo();
			$query = $db->getQuery(true);
			$query->delete($db->qn('#__secretary_repetition'));
			$query->where($db->qn('time_id').' = '. $db->escape($id));
			$query->where($db->qn('extension').' = '. $db->quote("documents"));
			$query->where($db->qn('startTime').' = '. $db->escape( strtotime($created) ));
			$db->setQuery($query);
			$db->query();
		}
		return;
	}
	
	public static function getMonthname($id = null)
	{ 

		if(!empty($id)) {
			$dateObj   = DateTime::createFromFormat('!m', $id);
			$result = $dateObj->format('F');
		}else {	
			$result	= array();
			for ($m=1; $m<=12; $m++) {
				$month = date('F', mktime(0,0,0,$m, 1, date('Y')));
				$result[]	= JHtml::_('select.option',	$m, $month);
			}
		}
		return $result;
	}
	
	public static function getWeekDayname($id = null)
	{
		if(!empty($id)) {
			$result = date('D', strtotime("Sunday +{$id} days"));
		}else {	
			$result	= array();
			foreach($rows AS $key => $value) {
				$result[]	= JHtml::_('select.option',	$key, JText::_($value));
			}
		}
		return $result;
	}
	
	public static function getIsoWeeksInYear($year)
	{
		$date = new DateTime;
		$date->setISODate($year, 53);
		$weeks = ($date->format("W") === "53" ? 53 : 52);
		return array_combine(range(1, $weeks), range(1, $weeks) );
	}
	
	public static function getTimesToolbar($task)
	{
		
		$array 	= array();
		$array[] = array( 'link'=>'index.php?option=com_secretary&view=times&section=week', 'title'=>'COM_SECRETARY_TIMES_WEEKS_VIEW' );
		$array[] = array( 'link'=>'index.php?option=com_secretary&view=times&section=month', 'title'=>'COM_SECRETARY_TIMES_MONTH_VIEW' );
		$array[] = array( 'link'=>'index.php?option=com_secretary&view=times&section=year', 'title'=>'COM_SECRETARY_TIMES_YEARS_VIEW' );
		// $array[] = array( 'link'=>'index.php?option=com_secretary&view=events&section=day', 'title'=>'COM_SECRETARY_TIMES_DAY_VIEW' );
		
		$result = '';
		for($x = 0; $x < count($array); $x++) {
			$active = '';
			if($task && strpos($array[$x]['link'], $task) !== false)
				$active = 'active';
			$result .= '<a href="'. JRoute::_($array[$x]['link']).'" class="btn btn-default '.$active.'">'. JText::_($array[$x]['title']) .'</a>';
		}
		
		return $result;
	}
   
	public static function getLookAfterRepetitions( $extension = 'documents', $date = NULL, $ids = array())
	{
		
		// Format should be Y-m-d
		if(is_null($date)) $date = date('Y-m-d');
		$nextTime = strtotime( $date );
		
		$db		= \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);
		
		// Select
		$query->select( "r.*,r.id as repetition_id" );
		$query->from($db->qn('#__secretary_repetition','r'));
		
		// Join extension
		$query->select("item.*");
		$query->leftJoin($db->qn('#__secretary_'.$db->escape($extension),'item') .' ON item.id = r.time_id');
		
		// Join category
		$query->select("category.title as category_title");
		$query->leftJoin($db->qn('#__secretary_folders','category').' ON category.id = item.catid');
			
		if($extension === 'documents') {
		    // Join contact
			$query->select("CONCAT_WS(' ', firstname, lastname) as contact");
			$query->leftJoin($db->qn('#__secretary_subjects','subject').' ON subject.id = item.subjectid');
			
			// Join currency
			$query->select('cur.symbol as currencySymbol');
			$query->leftJoin('#__secretary_currencies AS cur ON item.currency = cur.currency');
		}
		
		if(!empty($ids)) {
			$query->where($db->qn('r.id').' IN ('.implode(",",$ids).')');	
		}
		
		$query->where($db->qn("r.extension")." =". $db->quote($extension)); 
		// <= look after past entries that have not been updated
		// Note : we need a while( date < nextDate) and increase nextDate with intervall from ...entries_repetition
		$query->where($db->qn("r.nextTime")." <=".$db->escape($nextTime));
		$query->order($db->qn("r.startTime")." ASC"); 
		
		$db->setQuery($query);
		return $db->loadObjectList();
		
	}
     
	public static function saveRepetition($extension, $id, $created, $repetition, $zyklus = false)
	{
		
		
		$table		= '#__secretary_repetition';
		if($zyklus == false)
			$zyklus		= '+'. $repetition['zyklus'] .' '. $repetition['type'];
		
		
		$startTime	= strtotime($created);
		$nextTime	= strtotime( $zyklus , $startTime );
		$nextInt	= $nextTime - $startTime;
		if(!empty( $repetition )) $endTime	= strtotime( '+'. ( $repetition['zyklus'] * $repetition['endTime']) .' '. $repetition['type'] , $startTime );
		
		$db		= \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);
		$sql = $db->getQuery(true);
		
		$query->select("*");
		$query->from($table);
		$query->where($db->qn('extension').'='. $db->quote($extension)); 
		$query->where($db->qn('time_id').'='.$db->escape($id)); 
		
		$db->setQuery($query);
		$saved = $db->loadObject();
		
		if( $saved->time_id > 0) {
			$sql->update($table);
			$sql->set($db->qn('startTime').' = '. $db->escape($startTime) );
			$sql->set($db->qn('nextTime').' = '. $db->escape($nextTime) );
			if(!empty($endTime)) $sql->set('endTime = '. $db->escape($endTime) );
			
			$sql->set($db->qn('intervall').' = '. $db->escape($nextInt) );
			$sql->set($db->qn('int_in_words').' = '. $db->quote($zyklus) );
			$sql->where($db->qn('time_id').' = '. $db->escape($id));
			$sql->where($db->qn('extension').' ='.$db->quote($extension));
			
			try {
				$db->setQuery($sql);
				$db->query();
			} catch(\Exception $e) {
				throw new \Exception($e->getMessage());	
			}
		} else {
			 
			$object = new stdClass();
			$object->time_id = $id;
			$object->startTime= $startTime;
			$object->nextTime= $nextTime;
			$object->extension= $extension;
			if(!empty($endTime)) $object->endTime= $endTime;
			$object->intervall= $nextInt;
			$object->int_in_words= $zyklus;
			$object->time_id = $id;
			$object->time_id = $id;
			
			try {
			    $result = $db->insertObject($table, $object);
			} catch(Exception $e) {
				throw new Exception($e->getMessage());	
			}
			// $newId = $dbaseConnection->insertid();
		}
		return true;
	}
	
	public static function updateRepetitions($extension, $ids = array())
	{
		// Naive Loesung
	    JTable::addIncludePath(SECRETARY_ADMIN_PATH.'/models/tables');
		$section = \Secretary\Application::getSingularSection( $extension );
		$table = JTable::getInstance($section, "SecretaryTable" );
 
		// Alle wo ein Update nötig ist
		$results = self::getLookAfterRepetitions($extension,null,$ids);
		
		$db			= \Secretary\Database::getDbo();
		$endDate	= strtotime( date('Y-m-d') );
		$user		= \Secretary\Joomla::getUser();
		
		$msgResult = array();
		// Jedes einzeln betrachten und in ihr das intervall so lang durchspielen bis Jetziges Datum erreicht ist, vorausgesetzt es bleibt unter endTime
		foreach($results AS $result) {
			
			$x = 1;
			
			$endDate = ($result->endTime < $endDate) ? $result->endTime : $endDate;
			if($extension == 'times') $nextEndTime = strtotime($result->int_in_words, strtotime( $result->endDate ));
			
			// Alle bis Heute erstellen, dh. auch solche, die 
			while($result->nextTime <= $endDate) {
				
				// Erzeugen einer fast identischen Kopie
				$data = (array) $result;
				
				if(!empty($data['fields'])) {
				    $fields = json_decode($data['fields']);
				    if(isset($fields->repetition))
				        unset($fields->repetition);
				    $data['fields'] = json_encode($fields);
				}
				
				switch($extension) {
					case 'documents' :
						$data['id'] = '';
						$data['accounting_id'] = 0;
						$data['nr'] = $result->nr.'-'.$x;
						$data['createdEntry'] = $result->nextTime;
						$data['created'] = date('Y-m-d', $result->nextTime);
						$data['created_by'] = $user->id;
						$msgResult[] = $data['nr'];
						break;
						
					case 'times' :
						$data['id'] = '';
						$data['created'] = date('Y-m-d H:i:s');
						$data['created_by'] = $user->id;
						$data['startDate'] = date('Y-m-d H:i:s', $result->nextTime);
						$data['endDate'] = date('Y-m-d H:i:s', $nextEndTime);
						$nextEndTime = strtotime($result->int_in_words, $nextEndTime);
						break;
						
				}
				
				try {
					$table->save($data);
				} catch(Exception $e) {
					throw new Exception($e->getMessage());	
				}
			
				// $newId = $table->id();
				$result->startTime = $result->nextTime;
				$result->nextTime = strtotime($result->int_in_words, $result->nextTime);
				$x++;
			}
			
			// Update der Wiederholungstabelle
			self::saveRepetition($extension, $result->time_id, date('Y-m-d', $result->startTime) , false, $result->int_in_words);
		}
		
		if(empty($msgResult)) {
			$msg = JText::sprintf("COM_SECRETARY_REPETITION_SAVED",0) .'<br>'. JText::_("COM_SECRETARY_REPETITION_NON");
		} else {
			$msg = JText::sprintf("COM_SECRETARY_REPETITION_SAVED",count($msgResult)) .'<br>'. implode("<br>", $msgResult);
		}
		return $msg;
	}
	
    public static function getTimeColor( $fieldss )
	{
		$result = "";
		if( $fields = json_decode($fieldss) ) {
			foreach($fields as $field) {
				if(is_array($field) && isset($field[3]) && ($field[3] == 'timeColor' OR $field[3] == 'color')) 
					$result = 'background-color:'. trim($field[2]) .';';
			}
		}
		return $result;
    }
	
	public static function reorderTasks( $oldItems, $getOrdered = FALSE )
	{
		// Preprocess the list of items to find ordering divisions.
		$result = array();
		
		// Items in correct order
		// There's a bug with array (slice), so we need to do it naive
		$ordered = "";
		
		// dummy
		$items = array();
		
		// parent ids
		$levelParentIds = array();
		foreach ($oldItems as $item)
		{
			$ordered .= '-'.$item->id.'-';
			$items[$item->id] = $item ;
			$levelParentIds[$item->level][$item->parentID][] = $item->id;
		}
		
		// sort by level
		ksort($levelParentIds);
		
		// Loop parent ids
		if(!empty($levelParentIds))
		{
			foreach($levelParentIds AS $level => $parentIds) 
			{	
			
				foreach($parentIds as $pid => $childIds)
				{
					// Search value parent_id
					$search = '-'. (string) $pid.'-';
					// Length
					$searchlen = strlen($search);
					// Position found or nah?
					// Found : There are childs 
					if(strpos($ordered, $search) !== false)
					{
						// Loop through childs
						foreach($childIds as $childId)
						{
							// Input value
							$input = '-'.$childId.'-'; 
							
							// First Remove
							$ordered = str_replace($input, "", $ordered);
		
							// Insert but search new structure
							$newPos = strpos($ordered, $search);
							$ordered = substr_replace($ordered, $input, ($newPos + $searchlen) , 0);
							
						}
					}
				}
			}
		}
		
		// Finalize
		$ordered = array_filter(explode("-",$ordered));
		if($getOrdered)
		{
			// Returns the reordered Ids of all Items
			return array_values($ordered);
		}
		else
		{
			// Returns the reordered items
			foreach($ordered as $id) {
				$result[] = $items[$id];
			}
			return $result;
		}
		
	}
	
	protected static $getProjectTasks = array();
	public static function getProjectTasks($pid, $parentID = 0)
	{ 
		
		if(empty(self::$getProjectTasks[$pid]))
		{
            $tasks  = array();
            $app    = \Secretary\Joomla::getApplication();
            $db     = \Secretary\Database::getDbo();
			
			// Alle Aufgaben des Projekts
			$sql	= $db->getQuery(true);
			$sql->select($db->qn(array('id','projectID','parentID','level','title','ordering','progress','contacts','startDate','endDate','calctime','totaltime','fields')))
					->from($db->qn('#__secretary_tasks'))
					->where($db->qn('projectID').' = '. $db->escape($pid))
			         ->order('ordering ASC');
 
			// Filter by search in title
			$search = $app->getUserStateFromRequest('com_secretary.times.filter.search', 'filter_search');;
			if (!empty($search)) {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$sql->where(' ( title LIKE ' . $search .') OR ( text LIKE ' . $search .')');
			}
			         
			$db->setQuery($sql);
			$tasks = $db->loadObjectList();
			
			//$tasks = self::reorderTasks($tasks);
			 
			$count = 0;
			$worktime = 0;
			foreach($tasks as $result)
			{
				if(!empty($result->fields)) {
					$result->timeColor = self::getTimeColor($result->fields);
				}
				$worktime += $result->totaltime;
				$count++;	
			}
			$tasks['worktime'] = $worktime;
			$tasks['count'] = $count;
			
			self::$getProjectTasks[$pid] = $tasks;
		} 
		return self::$getProjectTasks[$pid];
	}
	
	public static function subscription() {
		
	    JTable::addIncludePath(SECRETARY_ADMIN_PATH.'/models/tables');
		
		$app 	= \Secretary\Joomla::getApplication();
		
		$extension	= $app->input->getCmd('extension');
		if(empty($extension))
			return JText::_('COM_SECRETARY_NOT_FOUND').': '.JText::_('COM_SECRETARY_EVENT');
			
		$id		= $app->input->getString('id');
		$id		= \Secretary\Security::encryptor('open',$id);
		
		$section	= ($extension == 'tasks') ? 'Task' : 'Time';
		$event		= JTable::getInstance($section,"SecretaryTable");
		
		if($id > 0) {
			$event->load($id);
		} else {
			return JText::_('COM_SECRETARY_NOT_FOUND').': '.JText::_('COM_SECRETARY_EVENT');
		}
		
		$cid	= $app->input->getString('cid');
		if(!empty($cid)) {
		    $contactid	= \Secretary\Security::encryptor('open',$cid);
			$contact = \Secretary\Database::getQuery('subjects',$contactid);
			if(empty($contact->id)) {
				return JText::_('COM_SECRETARY_NOT_FOUND').': '.JText::_('COM_SECRETARY_SUBJECT');
			}
		} else {
			// Kontakt neu erstellen
			
			// Name
			$cname	= $app->input->getString('cname');
			$contactName = \Secretary\Utilities::cleaner($cname);
			$cleanname	= explode(" ", trim($contactName));
			$lastname	= trim(array_pop($cleanname));
			$firstname	= trim(str_replace($lastname, '', $cname));
			
			// Email
			$cemail	= $app->input->getString('cemail');
			$contactEmail = \Secretary\Utilities::cleaner($cemail);
			if(!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
				return JText::_('COM_SECRETARY_EMAIL_INVALID');
			}
		
			// Data
			$data = array('id'=>0,'business'=>$event->business,'firstname'=>$firstname,'lastname'=>$lastname,'email'=>$contactEmail);
			
			$subjectTable = JTable::getInstance("Subject","SecretaryTable");
			
			$subjectTable->prepareStore($data);
			if (!$subjectTable->bind($data)) { return $subjectTable->getError(); }
			if (!$subjectTable->check()) { return $subjectTable->getError(); }
			if (!$subjectTable->store($data)) { return $subjectTable->getError(); }
			
			$contactid = (int) $subjectTable->id;
		}
		
		// Anmelden auf Event
		$input = array($contactid => array('id'=>$contactid,'note'=>''));
		if( $oldContacts = json_decode($event->contacts,true) ) {
			foreach($oldContacts as $oldContact) {
				$input[ $oldContact['id'] ] = $oldContact;
			}
		}
		
		$input = array_values($input);
		$event->contacts = json_encode($input, JSON_NUMERIC_CHECK);
		
		// Update
		$db		= \Secretary\Database::getDbo();
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_'.$db->escape($section).'s'))
				->set('contacts = '.$db->quote($event->contacts))
				->where('id = '.$event->id);
		
		try {
			$db->setQuery($query);
			$result = $db->query();
		} catch (\Exception $ex) {
			return $ex->getMessage();	
		}
		
		return JText::_('COM_SECRETARY_TIME_SUBSCRIPTION_SUCCESS');
	}
	
	public static function getListScaleOptions()
	{
	    $result	= array();
	    $rows = array(
	        "hours"   =>  "COM_SECRETARY_HOURS",
	        "days"    =>  "COM_SECRETARY_DAYS",
	        "weeks"   =>  "COM_SECRETARY_WEEKS",
	        "months"  =>  "COM_SECRETARY_MONTHS"
	    );
	    foreach($rows AS $key => $value) {
	        $result[]	= JHtml::_('select.option',	$key, JText::_($value));
	    }
	    return $result;
	}
	
}

	
