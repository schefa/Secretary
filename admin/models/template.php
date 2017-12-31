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

jimport('joomla.application.component.modeladmin');

class SecretaryModelTemplate extends JModelAdmin
{
	
    protected $app;
    protected $extension;
    protected static $_item;

    public function __construct($config = array())
	{
        $this->app          = \Secretary\Joomla::getApplication();
		$this->extension    = $this->app->input->getCmd('extension','documents');
        parent::__construct($config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
     */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'template');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Template', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;
		return $form;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{ 
		// Session
		$data = $this->app->getUserState('com_secretary.edit.template.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			$data->title	= Secretary\Utilities::cleaner($data->title,true);
			$data->language	= Secretary\Utilities::cleaner($data->language,true);
		}

		$catid = $this->app->input->getInt('catid');
		if(empty($data->catid) && !empty($catid)) {
			$data->catid = $catid;
			$data->category = Secretary\Database::getQuery('folders', $data->catid );
		}
		
		return $data;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		if(empty(self::$_item[$pk]) && ($item = parent::getItem($pk)))
		{
		    
			$item->text		= Secretary\Utilities::cleaner($item->text,true);
			$item->dpi      = (isset($item->dpi)) ? $item->dpi : 96;
			
			$margins = explode(";",$item->margins);
			$item->margins = (isset($margins[3])) ? $margins : array();
			
			$item->dim = new stdClass();
			$item->dim->formate = array( 
			    array('value'=>'210mm;297mm','title'=>'A4 - Portrait'),
			    array('value'=>'297mm;210mm','title'=>'A4 - Landscape'),
			    array('value'=>'148mm;210mm','title'=>'A5 - Portrait'),
			    array('value'=>'210mm;148mm','title'=>'A5 - Landscape'),
			    array('value'=>'100%;100%','title'=>'100%')
			);
			$item->dim->dpis = array(72,96,150,300);
			$item->dim->formatWidth = 210;
			$item->dim->formatHeight = 297;
			$item->dim->proportion = "mm";
			
			$format = explode(";", $item->format);
			if(!empty( $format[0] ) && !empty( $format[1] )) {
				$item->dim->formatWidth = intval( $format[0] );
				$item->dim->formatHeight = intval( $format[1] );
				$item->dim->proportion = preg_replace('/[0-9]+/', '', $format[0]);
			}
			
			if(!empty($item->title)) $item->title = JText::_($item->title);
			
			if($this->extension == 'newsletters') { 
				$item->contacts = \Secretary\Helpers\Newsletter::getNewsletterContacts($item->catid);	
			}
			
			if(empty($item->extension)) $item->extension = $this->extension;
			$item->templateInfoFields	= array( 'created'=> date("d.m.Y") );
			
			$item->extrafields = \Secretary\Helpers\Templates::getExtraFields($item->fields, array('id'=> $item->id));
			
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
		$user	= JFactory::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			if ( !$user->authorise('core.create', 'com_secretary.template') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.template.'.$pk) ) ) {
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED')); return false;
			}
		}
		
		// Allow an exception to be thrown.
		try
		{
			// Load existing record.
			if ($pk > 0) { $table->load($pk); }

			// Prepare
			$this->prepareData($data);
			
			// Bind
			if (!$table->bind($data)) { $this->setError($table->getError()); return false; }
			
			// Store
			if (!$table->store()) { $this->setError($table->getError()); return false; }
			
			// Activity
			$newID = (int) $table->id;
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set('templates', $activityAction, $data['catid'], $newID, $user->id);
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
	 * Method to prepare data
	 * 
	 * @param array $data
	 */
	protected function prepareData(&$data)
	{
		if(empty($data['extension'])) $data['extension'] = $this->extension;
		 
		$data['fields']	= isset($data['fields']) ? \Secretary\Helpers\Items::saveFields($data['fields']) : FALSE;
		
		$margins = "";
		if(is_array($data['margins'])) {
		    for($x = 0; $x <= 3; $x++) {
		        $margins .= intval($data['margins'][$x]);
		        if($x != 3) $margins .= ";";
		    }
		}
		$data['margins'] = $margins;
		
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
	    \Secretary\Helpers\Batch::batch( 'templates', $commands, $pks, $contexts);
	    $this->cleanCache();
	    return true;
	}
	

}