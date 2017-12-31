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

jimport('joomla.application.component.modeladmin');

class SecretaryModelTime extends JModelAdmin
{
    
    protected $app;
    protected $business;
    protected $catid;
    protected $locationid;
    protected $pid;
    protected $text_prefix = 'com_secretary';
	
	public $extension;
	public $tableName;
	public $type;
	private static $_item;
	
	protected static $whiteTasks = array(
	    'time',
	    'times',
	    'events',
	    'location',
	    'locations',
	    'locations_products',
	    'task',
	    'tasks',
	    'projects'
	);

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
	    
	    $this->app         = \Secretary\Joomla::getApplication();
		$this->business    = \Secretary\Application::company();
	    $this->catid       = $this->app->input->getInt('catid');
	    $this->locationid  = $this->app->input->getInt('location_id');
	    $this->pid		   = $this->app->input->getInt('pid');
	    $this->extension   = $this->app->input->getCmd('extension');
		//if(isset($this->extension) && !in_array($this->extension,self::$whiteTasks)) die;
		
		if($this->extension == 'tasks')
			$this->tableName = 'task';
		else {
			$this->tableName = 'time';
		}
		
		parent::__construct();
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Time', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($this->tableName, $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'time');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.'.$this->tableName, $this->tableName, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) { return false; }
		return $form;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
	    $item = $this->app->getUserState('com_secretary.edit.'.$this->tableName.'.data', array());
		if (empty($item)) {
			$item = $this->getItem();
			
			$item->title		= Secretary\Utilities::cleaner($item->title,true);
			if(empty($item->catid) && ($this->catid > 0)) $item->catid = $this->catid;
			
			if($item->extension === 'tasks') {
				if(empty($item->projectID))
				    $item->projectID = $this->app->input->getInt('pid');
			
			} 
			
			if(isset($item->calctime)) $item->calctime = $item->calctime / 3600;
		}
		return $item;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		if(empty(self::$_item[$pk]) && ($item = parent::getItem($pk)))
		{
			
		    $user				= \Secretary\Joomla::getUser();
			$item->extension	= (empty($item->extension)) ? $this->extension : $item->extension;
			$item->created_by	= (isset($item->created_by)) ? $item->created_by : $user->id;
			
			// Default Team Member is User that creates the Task
			if(($this->extension == 'tasks') && empty($item->id)) {
				if(empty($item->projectID)) {
					$item->projectID = $this->pid;
					$item->contacts = Secretary\Database::getQuery('times',(int) $this->pid,'id','contacts','loadResult');
				}
			}
			
			if(empty($item->location_id) && !empty($this->locationid)) $item->location_id = $this->locationid;
			
			if( $fields = json_decode($item->fields) ) {
				if(isset($fields->repetition))
					$item->repetition = (array) $fields->repetition;
			}
			
			if(!isset($item->parentID)) $item->parentID = 0;
			
			if($item->id > 0 && $item->extension == 'projects')
			{
				$countTasks = 0;
				$countSubTasks = 0;
				$item->tasks = \Secretary\Helpers\Times::getProjectTasks($item->id);
			}
			self::$_item[$pk] = $item;
		}
		
		return self::$_item[$pk];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::save()
	 */
	public function save($data)
	{
		
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$db     = \Secretary\Database::getDBO(); 
		$user	= \Secretary\Joomla::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			if ( !$user->authorise('core.create', 'com_secretary.time') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.time') ) )
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}
		
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0) {
				$table->load($pk);
			}
			
			if(strlen($data['title'])<1){
			    $errTitle = JText::_('COM_SECRETARY_TITLE');
			    $this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
			    return false;
			}
			
			if($data['extension'] === 'tasks') {
				// Get Level of parent
				$parent = Secretary\Database::getQuery('tasks',(int) $data['parentID'],'id',$db->qn(array('level','parentID')));
				$data['level'] =  $parent->level + 1;
				if(($pk > 0 && $pk === $data['parentID'])) {
					$this->setError(JText::_('COM_SECRETARY_CATEGORIES_FIELD_PARENT_DESC') ); return false;
				}
			} else {
				if ($data['ordering'] <= 0) {
					// get last order
					$db->setQuery('SELECT ordering 
								FROM #__secretary_times 
								WHERE extension = '.$db->quote($data['extension']).' 
								ORDER BY ordering DESC',0,1);
					$lastOrder = $db->loadResult();
					$data['ordering'] = ($lastOrder) ? $lastOrder + 1 : 1;
				}
			}
			
			if(isset($data['calctime'])) $data['calctime'] = $data['calctime'] * 3600;
		
			// Teilnehmer
			$features = array();
			if(isset($data['features'])) {
				foreach($data['features'] as $idx => $feature) {
					foreach($feature as $key => $value) {
						$features[(int) $idx][$key] = Secretary\Utilities::cleaner($value); 
					}
				}
			}
			$data['contacts']	= (!empty($features)) ? json_encode($features) : '';
			$data['business']	= (int) $this->business['id'];
			$data['created']	= (!empty($table->created)) ? $table->created : date("Y-m-d H:i:s");
			$data['created_by']	= (!empty($table->created_by)) ? $table->created_by : $user->id;
			
			// Repetition
			if(isset($data['repetition'])) {
				$this->cleanRepetitions($data['id'], $data['startDate']);
				if(!empty($data['id']) && $data['repetition']['check'] == 1 && $data['repetition']['zyklus'] > 0) {
					\Secretary\Helpers\Times::saveRepetition("times", $data['id'], $data['startDate'], $data['repetition'] );
					$data['fields']['repetition'] = $data['repetition'];
				}
			}
			$data['fields']		= (isset($data['fields'])) ? \Secretary\Helpers\Items::saveFields($data['fields']) : '';
			
			// Bind the data.
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
			
			if($data['extension'] === 'tasks') { 
				if (!$table->rebuildLevel($table->id, $data['parentID'], $data['level'] )) {
					$this->setError($table->getError()); return false; }
					
				if (!$table->reorder($table->business)) {
					$this->setError($table->getError()); return false; }
			} 
			
			// Update Upload Document 
			$newID = (int) $table->id;
			if( $user->authorise('core.upload', 'com_secretary') ) {
				\Secretary\Helpers\Uploads::upload( 'time', 'times', $data['upload_title'], $newID );
			}
			
			// Aktivität
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set($this->extension, $activityAction, $data['catid'], $newID, $user->id);

			// Update Connections so that subjects can access
			$connection = new \Secretary\Helpers\Connections($this->extension, $newID);
			$connection->deleteConnections(false);
			if(!empty($data['features'])) {
			    foreach($data['features'] as $idx => $feature) {
			        foreach($feature as $key => $value) {
			            if($key =='id') {
			                $connection->addConnection($value, $data['features'][$idx]['note']);
			            }
			        }
			    }
			}
				
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
			$this->setState($this->getName().'.id', $table->$pkName);
		}

		$this->cleanCache();
		return true;
		
	} 
	
	public function getProjects( $id = NULL )
	{
		
		if(empty($this->pid)) $this->pid = (int) $id;
		
		$user	= \Secretary\Joomla::getUser();
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		$query->select("id,title")
				->from($db->qn('#__secretary_times'))
				->where($db->qn('extension').'='.$db->quote("projects"));
				
		$db->setQuery($query);
		$projects = $db->loadObjectList();
		
		$result = array('items'=>array(), 'default' => $this->pid );
		foreach($projects as $project) {
			if($user->authorise('core.show','com_secretary.time.'.$project->id) 
			|| $user->authorise('core.show.other','com_secretary.time.'.$project->id))
			{
				$result['items'][]	= JHtml::_('select.option',	$project->id, JText::_($project->title));
			}
		}
		
		return $result;
	}
	
	public function cleanRepetitions( $id = '', $created = NULL )
	{
		if(!empty($id) && !empty($created)) {
			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__secretary_repetition'));
			$query->where($db->quoteName('time_id').' = '. $db->escape($id));
			$query->where($db->quoteName('extension').' = '. $db->quote("times"));
			$query->where($db->quoteName('startTime').' = '. $db->escape( strtotime($created) ));
			$db->setQuery($query);
			$db->query();
		}
		return;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		$tks = array();
		if(isset($pks['task'])) {
			$tks = $pks['task'];
		}

		$this->cleanCache();
		 
		// Attempt to run the batch operation.
		if (empty($tks) && \Secretary\Helpers\Batch::batch( 'times', $commands, $pks, $contexts)) {
			return true;
		} elseif(!empty($tks) && \Secretary\Helpers\Batch::batch( 'tasks', $commands, $tks, $contexts)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::delete()
	 */
	public function delete(&$pks)
	{
		$tks = array();
		if(isset($pks['task'])) {
			$tks = $pks['task'];
			$this->extension = 'tasks';
			$this->tableName = 'task';
			$table = $this->getTable('Task');
			foreach ($tks as $i => $pk)
			{
				if ($table->load($pk) && $this->canDelete($table))
				{
					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
				}
			}
			
		} else {
			parent::delete($pks);
		}
	}
}