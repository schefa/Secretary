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

class SecretaryControllerProducts extends Secretary\Controller\Admin
{
    protected $app;
    protected $catid;
    protected $view;
    protected $redirect_url;
	
	public function __construct() {
		$this->app		= JFactory::getApplication();
		$this->catid	= $this->app->input->getInt('catid', 0);
		$this->view		= $this->app->input->getCmd('view');
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}
	
	public function getModel($name = 'Product', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function buy()
	{
		
		if( !JFactory::getUser()->authorise('core.create','com_secretary.document') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}
		
		$input	= $this->app->input;
		$pks	= $input->get('cid', array(), 'array');
		// $suppliers = Secretary\Database::getQuery('products',$pks);
		
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=document&layout=edit&pusage=2&pid='.implode(',',$pks), false));
		return true;
	}
	
	public function sell()
	{
		if( !JFactory::getUser()->authorise('core.create','com_secretary.document') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}
		
		$input	= $this->app->input;
		$pks	= $input->get('cid', array(), 'array');
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=document&layout=edit&pusage=1&pid='.implode(',',$pks), false));
		return true;
	}
		
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function applyColumns()
	{
	    $stockcolumns	= $this->app->input->get('chk_group', array(), 'array');
	    if(empty($stockcolumns)) $stockcolumns = array(0=>"title");
	    $this->app->setUserState('filter.products_columns', $stockcolumns);
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	    return true;
	}
	
}