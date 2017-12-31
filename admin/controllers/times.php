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
jimport('joomla.application.component.helper'); 

class SecretaryControllerTimes extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $catid;
    protected $view;
    protected $redirect_url;
	
	public function __construct() {
	    $this->app		= \Secretary\Joomla::getApplication();
	    $this->catid	= $this->app->input->getInt('catid');
	    $this->view		= $this->app->input->getCmd('view', 'times');
		$this->redirect_url = 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}
    
	public function getModel($name = 'Time', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function saveOrder()
	{

	    $canDo	= \Secretary\Helpers\Access::getActions($this->view); 
	    $order = $this->app->input->get('order', array(), 'array');
	    $msg   = JText::_('COM_SECRETARY_ORDERING_SAVED_FAILED');
	    
	    if($canDo->get('core.edit') && !empty($order)) {
	         
	        $db = \Secretary\Database::getDBO();
	
	        $oldOrders = array();
	        $oldOrdersTasks = array();
	
	        foreach($order as $id => $value) {
	            if(is_numeric($id)) {
	                $oldOrders[]  = Secretary\Database::getQuery('times', (int) $id,'id','ordering','loadResult');
	            }
	            if(is_array($value)) {
	            	
	            	foreach($value as $taskID) {
	            		$oldOrdersTasks[]  = Secretary\Database::getQuery('tasks', (int) $taskID,'id','ordering','loadResult');
	            	}
	            	
			        $start = min($oldOrdersTasks);
			        $start = ($start <= 0) ? 1 : $start;
			        
			        foreach($value as $taskID ) {
			        	$query = $db->getQuery(true);
			        	$query->update($db->qn('#__secretary_tasks'))
			        			->set($db->qn('ordering').'='.$start)
			        			->where($db->qn('id').'='. (int) $taskID);
	                    $db->setQuery($query);
	                    $db->execute();
	                    $start++;
	                }
	            }
	        }
	        
	        $startOrder = min($oldOrders);
	        $startOrder = ($startOrder <= 0) ? 1 : $startOrder;
	
	        foreach($order as $id => $value) {
	            if(is_numeric($id)) {
	            	$query = $db->getQuery(true);
	            	$query->update($db->qn('#__secretary_times'))
	            		->set($db->qn('ordering').'='.$startOrder)
	            		->where($db->qn('id').'='. (int) $id); 
	                $db->setQuery($query);
	                $db->execute();
	                $startOrder++;
	            }
	        }
	        $msg = JText::_('COM_SECRETARY_ORDERING_SAVED');
	    }
	    
	    $this->setMessage( $msg);
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function updateRepetitions()
	{
		
	    if( !\Secretary\Joomla::getUser()->authorise('core.create','com_secretary.time') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}
		
		$msg = \Secretary\Helpers\Times::updateRepetitions("times");
		$this->setMessage($msg);
		
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	} 
	
	
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}

}