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

class SecretaryModelProduct extends JModelAdmin
{
    
    protected $app;
    protected $catid;
    protected $fileId;
    protected $text_prefix = 'com_secretary';
    private $business = array();
    private static $_item;

    /**
     * Class constructor
     * 
     * @param array $config
     */
	public function __construct($config = array())
	{
	    $this->app          = \Secretary\Joomla::getApplication(); 
	    $this->fileId		= $this->app->input->getInt('secf');
	    $this->catid		= $this->app->input->getInt('catid');
		$this->business		= Secretary\Application::company();
		parent::__construct();
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
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'product');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Product', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form	= $this->loadForm('com_secretary.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) return false;
		return $form;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		$item	= $this->app->getUserState('com_secretary.edit.product.data', array());
		if (empty($item)) {
			$item = $this->getItem();
			
			// Create document from an existing upload
			if(empty($item->upload) && !empty($this->fileId)) {
			    $item->upload = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);
			}
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
	        $item->title		= Secretary\Utilities::cleaner((string) $item->title,true);
	        $item->description	= Secretary\Utilities::cleaner((string) $item->description,true);
	        $item->business		= (isset($item->business)) ? $item->business : (int) $this->business['id'];
	        $item->taxRate		= (isset($item->taxRate)) ? $item->taxRate : (int) $this->business['taxvalue'];
	        $item->year		    = (isset($item->year)) ? $item->year : (int) date('Y');	        
	           
	        if(empty($item->catid) && !empty($this->catid)) {
	            $item->catid = $this->catid;
	            $item->category = Secretary\Database::getQuery('folders', $item->catid);
			} 
			
	        if( !isset($item->fields))
	        {
	            $catid = $item->catid;
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
	        
	        $item->template = (empty($item->template)) ? 0 : $item->template;
	        
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
		// Initialise variables; 
	    $user	= \Secretary\Joomla::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			if ( !$user->authorise('core.create', 'com_secretary.product') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.product.'.$pk) ) )
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}
		
		// Allow an exception to be thrown.
		try
		{
			// Load existing record.
			if ($pk > 0) { $table->load($pk); }
			
			$table->prepareStore($data);

			// Bind
			if (!$table->bind($data)) { $this->setError($table->getError()); return false; }
			
			// Check
			if (!$table->check()) { $this->setError($table->getError()); return false; }
			
			// Store
			if (!$table->store()) { $this->setError($table->getError()); return false; }
			
			// Update Upload Document 
			$newID = (int) $table->id;
			
			if(!empty($this->fileId)) {
				// create document from file
				// ToDo : Unternehmensunabhängig
			    $fileId = \Secretary\Helpers\Uploads::checkEmptyFileId($this->fileId);
				if($fileId > 0) {
				    \Secretary\Helpers\Uploads::connectFileWithSheet($newID, $fileId, 'products');
				}
			}
			elseif(empty($this->fileId) && $user->authorise('core.upload', 'com_secretary') )
			{
				\Secretary\Helpers\Uploads::upload( 'product', 'products', $data['upload_title'], $newID );
			}
			
			// Activity
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set('products', $activityAction, $data['catid'], $newID );
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
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		\Secretary\Helpers\Batch::batch( 'products', $commands, $pks, $contexts);
		$this->cleanCache();
		return true;
	}
	
}