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

class SecretaryControllerFolders extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $catid;
    protected $view;
    protected $extension;
    protected $redirect_url;
    
	public function __construct() {
	    $this->app        = \Secretary\Joomla::getApplication();
	    $this->catid      = $this->app->input->getInt('catid');
	    $this->view       = $this->app->input->getCmd('view');
	    $this->extension  = $this->app->input->getCmd('extension');
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view=folders&amp;extension='. $this->extension;
		parent::__construct();
	}
	
	public function getModel($name = 'Folder', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function rebuild()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
 
		$this->setRedirect(JRoute::_($this->redirect_url, false));

		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_SECRETARY_CATEGORIES_REBUILD_SUCCESS'));
			return true;
		}
		else
		{
			// Rebuild failed.
			$this->setMessage(JText::_('COM_SECRETARY_CATEGORIES_REBUILD_FAILURE'));
			return false;
		}
	}
	
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function checkin()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$ids = $this->input->post->get('cid', array(), 'array');
		
		$return = Secretary\Controller::checkin($this->view_list,$ids);
		if ($return === false)
		{
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($this->redirect_url, false), $message, 'error');
		}
		else
		{
			// Checkin succeeded.
			$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(JRoute::_($this->redirect_url, false), $message);
		}
		return $return;
	}
}
