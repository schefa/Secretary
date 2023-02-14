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

class SecretaryControllerSubjects extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;
	protected $redirect_url;

	public function __construct()
	{
		$this->app		= \Secretary\Joomla::getApplication();
		$this->catid	= $this->app->input->getInt('catid');
		$this->view		= $this->app->input->getCmd('view');
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;catid=' . $this->catid;
		parent::__construct();
	}

	public function getModel($name = 'Subject', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function importUsers()
	{
		$msg = \Secretary\Helpers\Subjects::importUsers();
		$this->setMessage($msg);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}

	public function addDocuments()
	{
		$pks	= \Secretary\Joomla::getApplication()->input->get('cid', array(), 'array');
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&amp;view=document&layout=edit&amp;catid=' . $this->catid . '&amp;subject=[' . implode(",", $pks) . ']', false));
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(JRoute::_($this->redirect_url, false));
	}

	public function applyColumns()
	{
		$app = \Secretary\Joomla::getApplication();

		$stockcolumns	= $app->input->get('chk_group', array(), 'array');

		if (empty($stockcolumns)) $stockcolumns = array(0 => "lastname");

		$app->setUserState('filter.contacts_columns', $stockcolumns);

		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
}
