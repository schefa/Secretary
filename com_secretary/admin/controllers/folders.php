<?php

defined('_JEXEC') or die;


class SecretaryControllerFolders extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;
	protected $extension;
	protected $redirect_url;

	public function __construct()
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->catid = $this->app->input->getInt('catid');
		$this->view = $this->app->input->getCmd('view');
		$this->extension = $this->app->input->getCmd('extension');
		$this->redirect_url = 'index.php?option=com_secretary&amp;view=folders&amp;extension=' . $this->extension;
		parent::__construct();
	}

	public function getModel($name = 'Folder', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
        return $model;
	}

	public function rebuild()
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));

		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Rebuild succeeded.
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_REBUILD_SUCCESS'));
			
            return true;
		}
        else
		{
			// Rebuild failed.
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_REBUILD_FAILURE'));
			
            return false;
		}
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

}