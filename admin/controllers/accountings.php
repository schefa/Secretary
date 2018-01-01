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

class SecretaryControllerAccountings extends Secretary\Controller\Admin
{
    
    protected $app;
    protected $accountId;
    protected $extension;
    protected $view;
	protected $redirect_url = 'index.php?option=com_secretary';
	
	public function __construct() {
	    $this->app		    = \Secretary\Joomla::getApplication();
		$this->accountId	= $this->app->input->getInt('account');
		$this->view			= $this->app->input->getCmd('view');
		$this->extension	= $this->app->input->getCmd('extension');
		
		$this->redirect_url .= '&amp;view='.$this->view.'&amp;account='. $this->accountId;
		if(isset($this->extension)) $this->redirect_url .= '&extension='. $this->extension;
		parent::__construct();
	}
	
	public function getModel($name = 'Accounting', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function buchen()
	{
		$pks	= $this->app->input->get('cid', array(), 'array');
		
		$return = \Secretary\Helpers\Accounts::book($pks);
		
		$this->setMessage($return);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
	
	public function storno()
	{
		$pks	= $this->app->input->get('cid', array(), 'array');
		
		$return = \Secretary\Helpers\Accounts::storno($pks);
		
		$this->setMessage($return);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
	
	public function setStates()
	{
	    $pks	= $this->app->input->get('cid', array(), 'array');
		parent::setStatus($pks, 'accounting');
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
}