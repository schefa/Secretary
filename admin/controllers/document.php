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

jimport('joomla.application.component.controllerform');

class SecretaryControllerDocument extends JControllerForm
{
    
    protected $app;
    protected $catid;
    protected $fileId;
    protected $id;
    protected $location;
    protected $subject;
    protected $view_list;
    
    /**
     * Class constructor
     * 
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->app          = \Secretary\Joomla::getApplication();
        $this->id		    = $this->app->input->getInt('id');
        $this->catid		= $this->app->input->getInt('catid', 0);
        $this->fileId		= $this->app->input->getInt('secf');
        $this->location		= $this->app->input->getInt('location');
        $this->subject		= $this->app->input->getVar('subject');
        $this->view_list	= 'documents';
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::getModel()
     */
    public function getModel($name = 'Document', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
    {
        return Secretary\Model::create($name,$prefix,$config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::allowEdit()
     */
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $return = \Secretary\Helpers\Access::allowEdit('document',$data, $key);
		return $return;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToItemAppend()
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&catid=' . $this->catid;
		if(!empty($this->fileId)) $append .= '&secf=' . $this->fileId;
		if(!empty($this->location)) $append .= '&location=' . $this->location;
		if(empty($this->id) && !empty($this->subject)) $append .= '&subject=' . $this->subject;
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToListAppend()
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		if(!empty($this->location)) $append .= '&location=' . $this->location;
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::batch()
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('Document');
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::save()
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		parent::save($key, $urlVar);
		
		if($this->subject) {
		    $subjects = json_decode($this->subject);
		    $this->setMessage(JText::sprintf('COM_SECRETARY_DOCUMENTS_X_CREATED', count($subjects)));
		}
		
		$task = $this->getTask();
		if($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='.$this->view_list.'&catid=' . $this->catid, false));
		}
	}
	
	/**
	 * Send Document via Email
	 */
	public function email()
	{ 
		$id		= $this->app->input->getInt('id',0);
		if($id > 0) {
			$data	= Secretary\Database::getQuery('documents',$id, 'id', '*', 'loadAssoc');
			$data['subject'] = json_decode( $data['subject'] );
			$data['fields'] = json_decode( $data['fields'], true);
			$sent	= \Secretary\Email::emailDocument( $data );
			echo json_encode($sent);
		}
		$this->app->close();
	}
	
	/**
	 * Send a testemail to current user
	 */
	public function testemail()
	{ 
	    $id		= $this->app->input->getInt('id',0);
	    if($id > 0) {
	        $user  = JFactory::getUser();
			$data  = Secretary\Database::getQuery('documents',$id, 'id', '*', 'loadAssoc');
			$data['subject'] = json_decode( $data['subject'] );
			$data['subject'][6]	= $user->email;
			$data['subject'][1]	= $user->name;
			$data['fields'] = json_decode( $data['fields'], true);
			$sent	= \Secretary\Email::emailDocument( $data );
			echo json_encode($sent);
		}
		$this->app->close();
	}
	
	/**
	 * Create new Message out of document
	 */
	public function message()
	{ 
	    $id		= $this->app->input->getInt('id',0);
		if($id > 0) {
			$data	= Secretary\Database::getQuery('documents',$id, 'id', '*', 'loadAssoc');
			$data['fields'] = json_decode( $data['fields'], true);
			$result = \Secretary\Helpers\Messages::createFromDocument( $data );
			echo json_encode($result);
		}
		$this->app->close(); 
	}

}