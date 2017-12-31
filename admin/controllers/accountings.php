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

class SecretaryControllerAccountings extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $accountId;
    protected $extension;
    protected $view;
	protected $redirect_url = 'index.php?option=com_secretary';
	
	public function __construct() {
	    $this->app		    = \Secretary\Joomla::getApplication();
		$this->accountId	= $this->app->input->getInt('account');
		$this->view			= $this->app->input->getCmd('view');
		$this->extension	= $this->app->input->getCmd('extension');
		
		$this->redirect_url .= '&amp;view='.$this->view.'&amp;account='. $this->accountId;
		if(isset($this->extension)) $this->redirect_url .= '&extension='. $this->extension;
		parent::__construct();
	}
	
	public function getModel($name = 'Accounting', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function buchen()
	{
		$pks	= $this->app->input->get('cid', array(), 'array');
		
		$return = \Secretary\Helpers\Accounts::book($pks);
		
		$this->setMessage($return);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
	
	public function storno()
	{
		$pks	= $this->app->input->get('cid', array(), 'array');
		
		$return = \Secretary\Helpers\Accounts::storno($pks);
		
		$this->setMessage($return);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
	
	public function setStates()
	{
	    $pks	= $this->app->input->get('cid', array(), 'array');
		parent::setStatus($pks, 'accounting');
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
}