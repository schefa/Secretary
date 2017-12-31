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

class SecretaryControllerTemplates extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $catid;
    protected $extension;
    protected $view;    
    protected $redirect_url;
    
	public function __construct() {
	    $this->app		= \Secretary\Joomla::getApplication();
	    $this->catid	= $this->app->input->getInt('catid');
	    $this->extension	= $this->app->input->getCmd('extension');
		$this->view		= 'templates';
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}
	
	public function getModel($name = 'Template', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
	    $append = parent::getRedirectToItemAppend($recordId);
	    $append .= '&catid=' . $this->catid;
	    $append .= '&extension=' . $this->extension;
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		$append .= '&extension=' . $this->extension;
		return $append;
	}
	
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	/**
	 * Method to send newsletter to subscribed contacts
	 */
	public function sendLetter()
	{
	    if (\Secretary\Joomla::getUser()->authorise('com_secretary.message','core.create')) {
		    $pks	= $this->app->input->get('cid', array(), 'array');
		    $count	= 0;
		     
			foreach($pks as $pk) {
				$count += \Secretary\Helpers\Newsletter::sendNewsletter((int) $pk);
			}
		}
		
		$this->setMessage(JText::sprintf('Newsletter an %s Kontakte gesendet', $count));
		$this->setRedirect(JRoute::_($this->redirect_url. '&amp;extension=newsletters', false));
	}
	    
}