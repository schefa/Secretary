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

class SecretaryControllerTemplate extends JControllerForm
{

    protected $app;
    
    function __construct() {
        $this->app          = JFactory::getApplication();
        $this->catid		= $this->app->input->getInt('catid');
        $this->extension	= $this->app->input->getCmd('extension');
        $this->view_list    = 'templates';
        parent::__construct();
    } 
	
	public function getModel($name = 'Template', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
	    return Secretary\Model::create($name,$prefix,$config);
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&catid=' . $this->catid;
		if(!empty($this->extension)) $append .= '&extension=' . $this->extension;
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		if(!empty($this->extension)) $append .= '&extension=' . $this->extension;
		return $append;
	}

	public function batch($model = null)
	{
	    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	    $model = $this->getModel('Template');
	    $this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
	    return parent::batch($model);
	}
}