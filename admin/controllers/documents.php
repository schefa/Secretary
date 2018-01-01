<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin'); 

class SecretaryControllerDocuments extends Secretary\Controller\Admin
{
    protected $app;
	protected $catid;
	protected $view;
	protected $redirect_url;
	
	public function __construct() {
	    $this->app		= \Secretary\Joomla::getApplication(); 
	    $this->catid	= $this->app->input->getInt('catid');
	    $this->view		= $this->app->input->getCmd('view', 'documents');
		$this->redirect_url	= 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}
		
	public function getModel($name = 'Document', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	} 
	
	public function acquit()
	{
		
	    if( !\Secretary\Joomla::getUser()->authorise('core.edit','com_secretary.document') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED'));
			return false;
		}
		
		$pk 	= $this->app->input->getInt('cid');
		$return = \Secretary\Helpers\Documents::acquit($pk);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function updateProducts()
	{
	    if( !\Secretary\Joomla::getUser()->authorise('core.delete','com_secretary.product') ) {
			throw new Exception( JText::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			return false;
		}
	
		$pks	= $this->app->input->get('cid', array(), 'array');
	
		if(empty($pks)) {
			$this->setMessage(JText::_('COM_SECRETARY_NO_ITEM_SELECTED'), 'error');
		} else {
		    $return = \Secretary\Helpers\Products::updateProducts($pks);
			$msg = JText::sprintf('COM_SECRETARY_DOCUMENTS_PRODUCTS_UPDATE', implode(', ',$return));
			$this->setMessage($msg);
		}
		
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function updateRepetitions()
	{
	    $user = \Secretary\Joomla::getUser();
	    if( !$user->authorise('core.create','com_secretary.document') || !$user->authorise('core.create','com_secretary.time') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED')); return false; }
		
			$pks	= $this->app->input->get('cid', array(), 'array');
		
		if(!empty($pks)) {
			$msg = \Secretary\Helpers\Times::updateRepetitions("documents",$pks);
			$this->setMessage($msg);
		}
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function postDeleteUrl()
	{
	    $this->setRedirect(JRoute::_($this->redirect_url, false));
	}
	
	public function deleteRepetitions()
	{
	    if( !\Secretary\Joomla::getUser()->authorise('core.delete','com_secretary.time') ) {
			JError::raiseError(100, JText::_('COM_SECRETARY_PERMISSION_FAILED')); return false; }
		
		$pks	= $this->app->input->get('cid', array(), 'array');
		
		if(!empty($pks)) {
			$table = JTable::getInstance('Repetition','SecretaryTable');
			foreach($pks as $pk) {
				$table->delete((int) $pk);
				$table->reset();	
			}
		}
		
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	} 
	
}