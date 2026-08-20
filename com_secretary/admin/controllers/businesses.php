<?php

defined('_JEXEC') or die;


class SecretaryControllerBusinesses extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;

	public function __construct()
	{
		$this->app		= \Secretary\Joomla::getApplication();
		$this->catid	= $this->app->input->getInt('catid');
		$this->view		= $this->app->input->getCmd('view');
		parent::__construct();
	}

	public function getModel($name = 'Business', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
        return $model;
	}

	public function postDeleteUrl()
	{
		$this->setRedirect('index.php?option=com_secretary&view=businesses');
	}

	public function setDefault()
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$pks = $this->input->post->get('cid', array(), 'array');

		if (!(\Secretary\Helpers\Access::checkAdmin()))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			
            return false;
		}

		try
		{
			if (empty($pks))
			{
				throw new Exception(\Joomla\CMS\Language\Text::_('COM_SECRETARY_NO_ITEM_SELECTED'));
			}

			\Joomla\Utilities\ArrayHelper::toInteger($pks);

			// Pop off the first element.
			$id = array_shift($pks);
			$model = $this->getModel();
			$model->setHome($id);
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_SUCCESS_HOME_SET'));
		}
        catch (Exception $e)
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_secretary&view=businesses');
	}
}
