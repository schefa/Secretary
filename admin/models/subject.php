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

class SecretaryModelSubject extends JModelAdmin
{
    private static $_item;
    
    protected $app;
    protected $text_prefix = 'com_secretary';
    protected $params;
    protected $business;
    protected $fileId;
    
    public $userprofile = 0;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array())
	{
		$this->app	= \Secretary\Joomla::getApplication();
		if ($this->app->isClient('site')) {
		    $menu = $this->app->getMenu()->getActive();
		    $pk = (int) $menu->params->get('userprofile');
            if($pk > 0)
                $this->userprofile = Secretary\Database::getQuery('subjects', \Secretary\Joomla::getUser()->id,'created_by', 'id','loadResult');
        }
		
		$this->params	= Secretary\Application::parameters();
		$this->business	= Secretary\Application::company('id,fields');
		$this->catid    = $this->app->input->getInt('catid');
		$this->fileId	= $this->app->input->getInt('secf');
		parent::__construct();
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'subject');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Subject', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.subject', 'subject', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) { return false; }
		return $form;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = $this->app->getUserState('com_secretary.edit.subject.data', array());

		if (empty($data)) {
			$data = $this->getItem(); 
				
			// Create document from an existing upload
			if(empty($data->upload) && !empty($this->fileId))
			    $data->upload = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);
			
			$catid=	$this->app->input->getInt('catid');
			if(empty($data->catid) && !empty($catid)) {
				$data->catid = $catid;
				$data->category = Secretary\Database::getQuery('folders', $data->catid );
			}
			
			// Nr
			$data->number = Secretary\Utilities::cleaner($data->number ,true);
			if(empty($data->nr) && !empty($data->category->number)) {
			    $cntDocs = \Secretary\Helpers\Folders::countCategoryEntries('subjects',$data->catid) + 1;
				$startCnt = 0;
				$match 	= array();
				preg_match('#\{CNT([^}]*)\}#siU',$data->category->number,$match);
				if (!empty($match))
				{
					if(!empty($match[1]) && strpos($match[1],'start=') !== false) {
						$startCnt = substr($match[1],7); }
				}
				$cntDocs += $startCnt;
		
				$data->number = preg_replace('#\{CNT([^}]*)\}#siU', $cntDocs, $data->category->number);
			}
		}
		
		
		return $data;
	}
	
	/**
	 * Method to get the connection for the contact 
	 */
	public function getConnections($myId) {
		return \Secretary\Helpers\Connections::getConnections('subjects', $myId, false);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
	    if($this->userprofile >= 1) {
	        $pk = $this->userprofile;
	    }
	    
		if(empty(self::$_item[$pk]) && ($item = parent::getItem($pk)))
		{
		
			$item->firstname	= Secretary\Utilities::cleaner($item->firstname,true);
			$item->lastname		= Secretary\Utilities::cleaner($item->lastname,true);
			$item->street		= Secretary\Utilities::cleaner($item->street,true);
			$item->zip			= Secretary\Utilities::cleaner($item->zip,true);
			$item->location		= Secretary\Utilities::cleaner($item->location,true);
			$item->phone		= Secretary\Utilities::cleaner($item->phone,true);
			$item->email		= Secretary\Utilities::cleaner($item->email,true);
			$item->country		= !empty($item->country) ? Secretary\Utilities::cleaner($item->country,true) : '';
			$item->fullname     = trim($item->firstname .' '.$item->lastname);
			
            if($item->id > 0)
                $item->connections = $this->getConnections($item->id);
		
            $item->projects = \Secretary\Helpers\Connections::getContactProjects($item->id);
            $item->messages = $this->getContactMessages((int) $item->id);
            
			if( !isset($item->fields))
			{
			    $catid = (!empty($item->catid)) ? $item->catid : $this->catid;
			    
				if(!empty($catid)) {
					$item->fields = Secretary\Database::getQuery('folders', $catid, 'id', 'fields', 'loadResult');
					if(!empty($item->fields) && $catFields = json_decode($item->fields )) {
					    $newFields = array();
					    foreach($catFields AS $key => $value) {
					        if('template'===$value[3] && empty($item->template)) {
					            $item->template = $value[2];
					        } else {
					            $newFields[$key] = $value;
					        }
					    }
					    $item->fields = \Secretary\Helpers\Items::rebuildFieldsForDocument($newFields);
					}
				} 
				
			}
			
			if($item->created == '0000-00-00') $item->created = NULL;
		
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
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Initialise variables; 
		$user	= \Secretary\Joomla::getUser();
		$table	= $this->getTable('Subject');
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access
		$show = false;
		if(\Secretary\Helpers\Access::checkAdmin()) {
		    $show = true;
		}
		
		if ( !$show && ($user->authorise('core.create', 'com_secretary.subject') || ($pk > 0 && $user->authorise('core.edit.own', 'com_secretary.subject.'.$pk) ) || ( $pk > 0 && $this->userprofile == $pk)) ) {
			$show = true;
		}
		
		if(!$show) {
		    throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED')); return false;
		}
		
		// Allow an exception to be thrown.
		try
		{
			// Load existing record.
			if ($pk > 0) $table->load($pk);
			
			$table->prepareStore($data);
			
			// Bind
			if (!$table->bind($data)) { $this->setError($table->getError()); return false; }
			
			// Check
			if (!$table->check()) { $this->setError($table->getError()); return false; }
			
			// Store
			if (!$table->store()) { $this->setError($table->getError()); return false; }
 
			// synchronization
			$sync = (int) $this->app->input->post->get('sync');
			if((\Secretary\Helpers\Access::checkAdmin()) && $sync === 1 && $table->created_by > 0) {
				if (!$this->syncUserData($table->created_by,$data)) { 
					$this->setError('Synchronization failed');
					return false;
				} 
			}
			
			// Update Upload Document 
			$contactID = (int) $table->id;
			
			if(!empty($this->fileId)) {
				// create document from file
				// ToDo : Unternehmensunabhängig
			    $fileId = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);
			    if($fileId > 0) {
				    \Secretary\Helpers\Uploads::connectFileWithSheet($contactID, $fileId, 'subjects');
				}
			}
			elseif(empty($this->fileId) && $user->authorise('core.upload', 'com_secretary') )
			{
				$uploadTitle = (isset($data['upload_title'])) ? $data['upload_title'] : '';
				\Secretary\Helpers\Uploads::upload( 'contact', 'subjects', $uploadTitle, $contactID );
			}
			
			// Activity
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set('subjects', $activityAction, $data['catid'], $contactID, $user->id);
			
			// Newsletter
			\Secretary\Helpers\Newsletter::removeContactFromAllNewsletters($contactID);
			if(strpos($data['fields'],"newsletter") !== false) {
				if($fields = json_decode($data['fields'])) {
					foreach($fields as $key => $field) {
						if(is_numeric($key) && $field[3] == 'newsletter' && $field[2]) {
							\Secretary\Helpers\Newsletter::addContactToNewsletter( $contactID, $field[2] );
						}
					}
				}
			}
			
			$this->rebuildConnections((int) $pk, $data, (int) $contactID);

		
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
    
    /**
     * Synchronize Secretary contact with Joomla user
     * 
     * @param int $userId
     * @param array $data
     * @return boolean
     */
    private function syncUserData($userId,array $data) {
    	
    	$fullname = $data['firstname'].' '.$data['lastname'];
    	
        $db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
		$query->update("#__users");
		$query->set('name='.$db->quote($fullname));
		$query->set('email='.$db->quote($data['email']));
		$query->where('id='.$db->escape($userId));
				
		try {
			$db->setQuery($query);
			$db->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
    }
    
    /**
     * Method to rebuild the connections after storing data
     * 
     * @param int $pk
     * @param array $data
     * @param int $contactID
     */
    private function rebuildConnections($pk,array $data,$contactID) {

        $oldConnections = ($pk > 0 ) ? \Secretary\Helpers\Connections::getConnections('subjects', $pk, false) : array();
        
		// Connections
		$connection = new \Secretary\Helpers\Connections('subjects', $contactID);
		
		// Prepare
		if(!empty($oldConnections)) {
			foreach($oldConnections as $connIdx => $conn) {
			    if(!empty($data['features'])) {
    			    foreach($data['features'] as $idx => $feature) {
    			        foreach($feature as $key => $value) {
    			            if($key =='id' && $value == $conn->two) {
    			                unset($oldConnections[$connIdx]);
    			            }
    			        }
    			    }
			    }
			}
		}
		
		// Delete old
		$connection->deleteConnectionsSubjects($oldConnections);
		
		if(!empty($data['features'])) {
		    foreach($data['features'] as $idx => $feature) {
		        foreach($feature as $key => $value) {
		            if($key =='id') {
		                $connection->addConnection($value, $data['features'][$idx]['note'],true);
		            }
		        }
		    }
		}
        
	} 

	/**
	 * Method to get messages from contact
	 * 
	 * @param int $contact_id
	 * @return array list of messages
	 */
	public function getContactMessages($contact_id) {
	    $result = array();
	    if($contact_id > 0) {
	        $user = \Secretary\Joomla::getUser();
	        $contact = \Secretary\Database::getQuery('subjects', $user->id, 'created_by');
	        
	        $contactToStr = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (i.contact_to AS INTEGER)': 'i.contact_to';
	        $createdByToStr = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (i.created_by AS INTEGER)': 'i.created_by';
	        
	        if(isset($contact->id)) {
    	        $db = \Secretary\Database::getDBO();
    	        $query = $db->getQuery(true);
    	        $query->select("i.*");
    	        $query->from($db->qn('#__secretary_messages','i'));
    	        $query->where($contactToStr.'='.$db->escape($contact->id));
    	        $query->where($createdByToStr.'='.$db->escape($contact_id));
    
    	        $db->setQuery($query);
    	        $result = $db->loadObjectList();
	        }
	    }
	    return $result;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		\Secretary\Helpers\Batch::batch('subjects', $commands, $pks, $contexts);
		$this->cleanCache();
		return true;
	}
	
}