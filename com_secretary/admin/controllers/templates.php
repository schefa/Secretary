<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerTemplates extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $extension;
	protected $view;
	protected $redirect_url;

	public function __construct()
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->catid = $this->app->input->getInt('catid');
		$this->extension = $this->app->input->getCmd('extension');
		$this->view = 'templates';
		$this->redirect_url = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;catid=' . $this->catid;
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
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

}