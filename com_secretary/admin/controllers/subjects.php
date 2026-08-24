<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


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
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function addDocuments()
	{
		$pks	= \Secretary\Joomla::getApplication()->input->get('cid', array(), 'array');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&amp;view=document&layout=edit&amp;catid=' . $this->catid . '&amp;subject=[' . implode(",", $pks) . ']', false));
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function applyColumns()
	{
		$app = \Secretary\Joomla::getApplication();

		$stockcolumns	= $app->input->get('chk_group', array(), 'array');

		if (empty($stockcolumns))
		{
			$stockcolumns = array(0 => "lastname");
		}

		$app->setUserState('filter.contacts_columns', $stockcolumns);

		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
		
        return true;
	}
}
