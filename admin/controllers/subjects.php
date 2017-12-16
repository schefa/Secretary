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

jimport('joomla.application.component.controlleradmin'); 

class SecretaryControllerSubjects extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $catid;
    protected $view;
    protected $redirect_url;
    
	public function __construct() {
	    $this->app		= JFactory::getApplication();
	    $this->catid	= $this->app->input->getInt('catid');
		$this->view		= $this->app->input->getCmd('view');
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}
	
	public function getModel($name = 'Subject', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function importUsers()
	{
	    $msg = \Secretary\Helpers\Subjects::importUsers();
	    $this->setMessage($msg);
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}

	public function addDocuments()
	{

	    $pks	= JFactory::getApplication()->input->get('cid', array(), 'array');
	    $this->setRedirect(JRoute::_('index.php?option=com_secretary&amp;view=document&layout=edit&amp;catid='. $this->catid.'&amp;subject=['.implode(",",$pks).']', false));
	} 
	
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function applyColumns()
	{
	    $app = JFactory::getApplication();
	
	    $stockcolumns	= $app->input->get('chk_group', array(), 'array');
	
	    if(empty($stockcolumns)) $stockcolumns = array(0=>"lastname");
	     
	    $app->setUserState('filter.contacts_columns', $stockcolumns);
	
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	    return true;
	}
		
}