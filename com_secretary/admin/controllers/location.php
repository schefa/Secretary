<?php

defined('_JEXEC') or die;


class SecretaryControllerLocation extends \Joomla\CMS\MVC\Controller\FormController
{

	protected $app;
	protected $catid;
	protected $extension;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->app          = \Secretary\Joomla::getApplication();
		$this->catid		= $this->app->input->getInt('catid', 0);
		$this->extension	= $this->app->input->getCmd('extension');
		parent::__construct();
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getModel()
	 */
	public function getModel($name = 'Location', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::allowEdit()
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$return = \Secretary\Helpers\Access::allowEdit('location', $data, $key);
		
        return $return;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToItemAppend()
	 */
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

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::getRedirectToListAppend()
	 */
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

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Controller\FormController::batch()
	 */
	public function batch($model = null)
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$model = $this->getModel('Location');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
		
        return parent::batch($model);
	}
}
