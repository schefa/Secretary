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

class SecretaryControllerMessages extends Secretary\Controller\Admin
{
    protected $app;
    protected $catid;
    protected $view;
    protected $redirect_url;
    
	public function __construct() {
		$this->app		    = \Secretary\Joomla::getApplication();
		$this->catid	    = $this->app->input->getInt('catid');
		$this->view_list	= 'messages';
		$this->redirect_url = 'index.php?option=com_secretary&amp;view='.$this->view_list.'&amp;catid='. $this->catid;
		parent::__construct();
	}
	
	public function getModel($name = 'Message', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function changeState()
	{
		$data   = $this->app->input->post; 
	    $return = \Secretary\Helpers\Messages::changeMessageState($data);
	    if(false !== $return)
	       echo $return;
		$this->app->close();
	    return true;
	}

	public function addMessage()
	{
		$data       = $this->app->input->post;
	    $return = \Secretary\Helpers\Messages::addChatMessage($data);
	    if(false !== $return)
	       echo $return;
	    $this->app->close();
	    return true;
	}

	public function deleteMessage()
	{
		$id        = $this->app->input->getInt('id');
		
		$result = \Secretary\Helpers\Messages::deleteChatMessage((int) $id);
		echo $result;

		$this->app->close();
	    return true;
	}

	public function getMessages()
	{
		$ref        = $this->app->input->getInt('rid');
	    $messages = \Secretary\Helpers\Messages::getChatMessages($ref);
	    header('Content-Type: application/json');
	    echo json_encode($messages);
	    $this->app->close();
	    return true;
	}
	
	public function ping()
	{ 
		$ref   = $this->app->input->getInt('rid');
		$results = \Secretary\Helpers\Messages::getChatOnlineUsers($ref);
	    $onlines = array('list'=>$results,'total'=>count($results));
	    header('Content-Type: application/json');
	    echo json_encode($onlines);
	    $this->app->close();
	    return true;
	}
	
}
