<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class SecretaryModelLocation extends JModelAdmin
{
    
    protected $app;
    protected $business;
    protected $catid;
    protected $extension;
    protected $text_prefix = 'com_secretary';
    
    private static $_item; 

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
    public function __construct($config = array())
	{
	    $this->app          = \Secretary\Joomla::getApplication();
        $this->business     = \Secretary\Application::company();
        $this->extension    = $this->app->input->getCmd('extension');
        $this->catid        = $this->app->input->getInt('catid');
        parent::__construct($config);
    }
	
    /**
     * Method to check if user can delete
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
     */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'location');
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Location', $prefix = 'SecretaryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.location', 'location', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
	    $data = \Secretary\Joomla::getApplication()->getUserState('com_secretary.edit.location.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			$data->title		= Secretary\Utilities::cleaner($data->title,true);
			$data->street		= Secretary\Utilities::cleaner($data->street,true);
			$data->zip			= Secretary\Utilities::cleaner($data->zip,true);
			$data->location		= Secretary\Utilities::cleaner($data->location,true);
			$data->country		= Secretary\Utilities::cleaner($data->country,true);
		}
 
		if(empty($data->catid) && !empty($this->catid)) {
			$data->catid = $this->catid;
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
			if(!empty($item->title)) $item->title = JText::_($item->title);
			
			if(empty($item->extension)) $item->extension = $this->extension;
			
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
		$pk		= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		
		// Access
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			if ( !$user->authorise('core.create', 'com_secretary.location') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.location.'.$pk) ) )
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}
		
		// Allow an exception to be thrown.
		try
		{ 
			$this->prepareData($data);
				
			// Load the row if saving an existing record.
			if ($pk > 0) { $table->load($pk); }

			$data['created_by'] = (!empty($table->created_by)) ? $table->created_by : $user->id;
			
			// Bind
			if (!$table->bind($data)) { $this->setError($table->getError()); return false; }
			
			// Check
			if (!$table->check()) { $this->setError($table->getError()); return false; }
			
			// Store
			if (!$table->store()) { $this->setError($table->getError()); return false; }
			
			// Activity
			$newID = (int) $table->id;
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set('locations', $activityAction, $data['catid'], $newID);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		if (isset($table->id)) {
			$this->setState($this->getName().'.id', $table->id);
		}

		$this->cleanCache();
		return true;
		
	}
	
	/**
	 * Prepares the data input
	 * 
	 * @param array $data
	 */
	protected function prepareData(&$data)
	{
		if(empty($data['extension'])) $data['extension'] = $this->extension;
		
		// Geolocation
		if(!empty($data['location'])) {
			$coords = \Secretary\Helpers\Locations::getCoords($data['street'], $data['zip'], $data['location']);
			$data['lat'] = $coords['lat'];
			$data['lng'] = $coords['lng'];
		} else {
			$data['lat'] = 0.0;
			$data['lng'] = 0.0;
		}
		
		$data['business']	= $this->business['id'];
		$data['fields']     = (isset($data['fields'])) ? \Secretary\Helpers\Items::saveFields($data['fields']) : FALSE;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
	    \Secretary\Helpers\Batch::batch( 'locations', $commands, $pks, $contexts);
	    $this->cleanCache();
	    return true;
	}
	
}