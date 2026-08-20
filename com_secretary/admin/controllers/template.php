<?php

defined('_JEXEC') or die;


class SecretaryControllerTemplate extends \Joomla\CMS\MVC\Controller\FormController
{

	protected $app;

	function __construct()
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->catid = $this->app->input->getInt('catid');
		$this->extension = $this->app->input->getCmd('extension');
		$this->view_list = 'templates';
		parent::__construct();
	}

	public function getModel($name = 'Template', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&catid=' . $this->catid;
		
        if (!empty($this->extension))
		{
			$append .= '&extension=' . $this->extension;
		}
		
        return $append;
	}

	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		
        if (!empty($this->extension))
		{
			$append .= '&extension=' . $this->extension;
		}
		
        return $append;
	}

	public function batch($model = null)
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$model = $this->getModel('Template');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
		
        return parent::batch($model);
	}
}