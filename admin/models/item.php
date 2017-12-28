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

jimport('joomla.application.component.modeladmin');
jimport('joomla.form.form');

class SecretaryModelItem extends JModelAdmin
{
    protected $app;
	protected $business;
	protected $extension;
	protected $item;
	
	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array())
	{
		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension','status');
		$this->business		= Secretary\Application::company();
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\AdminModel::populateState()
     */
	protected function populateState()
	{
	    $pk = $this->app->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);
		
		$params = Secretary\Application::parameters();
		$this->setState('params', $params);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Status', $prefix = 'SecretaryTable', $config = array())
	{
		$type = $this->extension;
		return JTable::getInstance( $type , $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.'. $this->extension, $this->extension, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false; 
		
		if($this->extension == 'settings') {
			foreach( $this->getItem()->params as $key => $val) {
				$form->setFieldAttribute($key, 'default', $val);
			}
		}
			
		return $form;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
	    $data = $this->app->getUserState('com_secretary.edit.'.$this->extension.'.data', array());
		if (empty($data)) {
			$data = $this->getItem();
			
			if(isset($data->title)) {
				$data->title = Secretary\Utilities::cleaner($data->title,true);
			}
		}
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{  
	    
		if (empty($this->_item) && ($item = parent::getItem($pk))) {
		
			if(empty($item->business))
				$item->business = $this->business['id'];
		
			switch ($this->extension) {
				case ('fields') :
					$item->title = JText::_($item->title);
					if(!empty($item->values) && ($values = json_decode($item->values))) {
					    if(is_array($values)) {
    						$newValues = array();
    						foreach($values AS $value) 
    							$newValues[] = JText::_($value);
    						$item->values = json_encode($newValues);
					    }
					}

					if($item->type === 'html') {
					    $item->standard = strip_tags($item->standard,\Secretary\Helpers\Items::$allowedHTML);
					} else {
					    $item->standard = strip_tags($item->standard);
					}
					
					break;
					
				case ('status') :
					$item->title = JText::_($item->title);
					$item->description = JText::_($item->description);
					
					if(empty($item->extension)) {
					    $module	= $this->app->input->getCmd('module', 'system');
						$item->extension = $module;
					}
					break;
					
				case ('uploads') :
					$item->upload = $item->id;
					if(!empty($item->id) && !empty($item->title)) {	
						$item->link = '/administrator/components/com_secretary/uploads/'.$item->business.'/'. $item->folder.'/'.$item->title;
					}
					break;
					
				case 'settings' : 

				    $item->params['products_columns'] = (isset($item->params['products_columns'])) ? ($item->params['products_columns']) : (\Secretary\Helpers\Products::$selectedColumns);
				    
				    if(is_array($item->params['products_columns']))
				        $item->params['products_columns'] = json_encode($item->params['products_columns']);

			        $item->params['contacts_columns'] = (isset($item->params['contacts_columns'])) ? ($item->params['contacts_columns']) : (\Secretary\Helpers\Subjects::$selectedColumns);
			        
			        if(is_array($item->params['contacts_columns']))
			            $item->params['contacts_columns'] = json_encode($item->params['contacts_columns']);
			        
				    break;
			}
			$this->_item = $item;
		}
		
		return $this->_item;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::save()
	 */
	public function save($data)
	{
		// Initialise variables; 
		$user	= JFactory::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access checks.
		if (!$user->authorise('core.admin', 'com_secretary'))
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			return false;
		}
			
		// Allow an exception to be thrown.
		try
		{ 
    		// Load the row if saving an existing record.
    		if ($pk > 0) { $table->load($pk); }
    		
			switch ($this->extension) {
					
				case 'activities' :
					$data['business'] = $this->business['id'];
					break;
					
				case 'fields' :
					if($data['type'] == 'list') {
						$reOrder = array();
						foreach($data['values'] as $value) {
							$reOrder[$value['key']] = $value['value'];
						}
						$data['values']	= json_encode($reOrder);
					}
					
					if(empty($data['title'])) {
            			$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', 'No Title'));
					    return false;
					}
					
					if(empty($data['hard'])) {
					    $data['hard'] = str_replace(' ','',strtolower($data['title']));
					}
					break;
					
				case 'settings':
					
				    // Get the original POST data
				    $data	= $this->app->input->post->getVar('jform', array(), 'post', 'array');
					  
					unset($data['id']); 
					
					if(empty($data['downloadID'])) {
						$this->setError('Download ID missing');
						return false;
					} else {
						$this->updateDownloadId($data['downloadID']);
					}
					
					$data['products_columns'] = $this->getAcceptedCols($data, 'products_columns',\Secretary\Helpers\Products::$selectedColumns);
					$data['contacts_columns'] = $this->getAcceptedCols($data, 'contacts_columns',\Secretary\Helpers\Subjects::$selectedColumns);
					$data['params'] = json_encode($data);
					
					\Secretary\Helpers\Access::restoreDefaultSectionAssets();
					break;
					
				case 'status' :
					if(empty($data['id']) && $data['extension'] == 'accountings') {
						$this->setError(JText::_('Not possible to create more status for accounting. Edit the existing'));
						return false;	
					}
					break;
					
				case 'uploads' :
					$data['created'] = date('Y-m-d hh:mm:ss');
					if($data['id'] > 0) {
					    $item = Secretary\Database::getQuery('uploads',$this->item->id,'id','id,extension,itemID,business,title,folder');
						$data['created'] = $item->created;
						$data['folder'] = $item->folder;
						$data['title'] = $item->title;
					}
				
					$data['business'] = $this->business['id'];
					// Update Upload Document 
					if( $user->authorise('core.upload', 'com_secretary') )
					{
					    $files	= $this->app->input->files->get('jform');
						if(isset($files['upload']['name']) && !empty($files['upload']['name']))
						{
							$data['title'] = date('Y-m-d') .'_'. $files['upload']['name'];
							$data['folder'] = Secretary\Application::getSingularSection($data['extension']);
							\Secretary\Helpers\Uploads::upload( $data['folder'], $data['extension'], $data['upload_title'], $pk );
						} elseif(!isset($data['upload_title'])) {
							$this->setError(JText::_('COM_SECRETARY_NO_FILE_SELECTED'));
							return false;
						}
					} else {
						$this->setError(JText::_('JERROR_ALERTNOAUTHOR'));
						return false;
					}
					$data['title'] = (isset($table->title)) ? $table->title : $data['title'];
					$data['folder'] = (isset($table->folder)) ? $table->folder : $data['folder'];
					$data['extension'] = (isset($table->extension)) ? $table->extension : $data['extension'];
					break;
				
			}
			
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
	 * Method to update download id and enable joomla updater
	 */
	private function updateDownloadId($downloadID) {
		$hasValue = null;
		$db = \Secretary\Database::getDBO();
		$extra_query = "dlid=".($downloadID);
		
		$query = $db->getQuery(true);
		$query->select('name')
		->from($db->qn('#__update_sites'))
		->where($db->qn('name').'='.$db->quote('com_secretary'));
		$db->setQuery($query);
		$hasValue = $db->loadResult();
		
		if(!empty($hasValue )) {
			$query = $db->getQuery(true);
			$query->update('#__update_sites');
			$query->set('extra_query = '.$db->quote($extra_query));
			$query->where('name ='.$db->quote('com_secretary'));
			$db->setQuery($query);
			$db->execute();
		} else {
			$object = new stdClass();
			$object->name = 'com_secretary';
			$object->type = 'extension';
			$object->enabled = 1;
			$object->extra_query= $extra_query;
			$object->location='https://www.schefa.com/updates/secretary.xml';  
			$result = $db->insertObject('#__update_sites', $object);
		}
	}
	
	private function getAcceptedCols(&$data, $dataFieldName, $allCols, $standardIfEmpty = 'title') {

	    $ac = array();
	    if(isset($data[$dataFieldName])) {
    	    foreach($allCols as $name => $value) {
    	        if((in_array($name,$data[$dataFieldName])))
    	           $ac[] = $name;
    	    }
	    }
	    if(empty($ac)) { $ac = array($standardIfEmpty); }
	    	
	    return ($ac);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::cleanCache()
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_secretary');
	}
	
}