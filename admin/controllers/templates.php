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