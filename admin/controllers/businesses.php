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

jimport('joomla.application.component.controlleradmin'); 

class SecretaryControllerBusinesses extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $catid;
    protected $view;
    
	public function __construct() {
	    $this->app		= \Secretary\Joomla::getApplication();
		$this->catid	= $this->app->input->getInt('catid');
		$this->view		= $this->app->input->getCmd('view');
		parent::__construct();
	}
	
	public function getModel($name = 'Business', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function postDeleteUrl()
	{
	    $this->setRedirect('index.php?option=com_secretary&view=businesses');
	}
	
	public function setDefault()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$pks = $this->input->post->get('cid', array(), 'array');
		
		if( !(\Secretary\Helpers\Access::checkAdmin()) ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}
			
		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_SECRETARY_NO_ITEM_SELECTED'));
			}

			JArrayHelper::toInteger($pks);

			// Pop off the first element.
			$id = array_shift($pks);
			$model = $this->getModel();
			$model->setHome($id);
			$this->setMessage(JText::_('COM_SECRETARY_SUCCESS_HOME_SET'));
		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_secretary&view=businesses');
	}
        
}