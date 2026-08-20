<?php

defined('_JEXEC') or die;


class SecretaryControllerLocations extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;
	protected $redirect_url;

	public function __construct()
	{
		$this->app		= \Secretary\Joomla::getApplication();
		$this->catid	= $this->app->input->getInt('catid', 0);
		$this->view		= 'locations';
		$this->redirect_url  = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;catid=' . $this->catid;
		parent::__construct();
	}

	public function getModel($name = 'Location', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
        return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&catid=' . $this->catid;
		
        return $append;
	}

	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		
        return $append;
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}
}
