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

class SecretaryControllerLocation extends JControllerForm
{
    
    protected $app;
    protected $catid;
    protected $extension;
    
    /**
     * Class constructor
     */
    public function __construct() {
        $this->app          = JFactory::getApplication();
        $this->catid		= $this->app->input->getInt('catid',0);
        $this->extension	= $this->app->input->getCmd('extension');
        parent::__construct();
    }
    
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::getModel()
     */
    public function getModel($name = 'Location', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
    {
        return Secretary\Model::create($name,$prefix,$config);
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::allowEdit()
     */
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $return = \Secretary\Helpers\Access::allowEdit('location',$data, $key);
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
		if(!empty($this->extension)) $append .= '&extension=' . $this->extension;
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
		if(!empty($this->extension)) $append .= '&extension=' . $this->extension;
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::batch()
	 */
	public function batch($model = null)
	{
	    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	    $model = $this->getModel('Location');
	    $this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
	    return parent::batch($model);
	}
	
}