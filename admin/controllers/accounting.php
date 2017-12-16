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

jimport('joomla.application.component.controllerform');

class SecretaryControllerAccounting extends JControllerForm
{
    protected $app;
    
    /**
     * Class constructor
     */
    public function __construct() {
		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension', 'accounting');
        $this->view_list    = 'accountings';
        parent::__construct();
    }
	
    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::getModel()
     */
	public function getModel($name = 'Accounting', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
	    return Secretary\Model::create($name,$prefix,$config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::allowEdit()
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $return = \Secretary\Helpers\Access::allowEdit('accounting',$data, $key);
		return $return;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToItemAppend()
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		if(isset($this->extension) && $this->extension != 'accounting') $append .= '&extension=' . $this->extension;
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToListAppend()
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		if(isset($this->extension) && $this->extension != 'accounting') $append .= '&extension=' . $this->extension;
		return $append;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::batch()
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('Accounting', '', array());
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
}