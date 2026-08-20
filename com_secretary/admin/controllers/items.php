<?php

defined('_JEXEC') or die;


class SecretaryControllerItems extends Secretary\Controller\Admin
{

	protected $app;
	protected $catid;
	protected $view;
	protected $extension;
	protected $redirect_url;

	public function __construct()
	{
		$this->app		  = \Secretary\Joomla::getApplication();
		$this->catid      = $this->app->input->getInt('catid');
		$this->view       = $this->app->input->getCmd('view');
		$this->extension  = $this->app->input->getCmd('extension');
		$this->redirect_url = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;extension=' . $this->extension;
		parent::__construct();
	}

	public function getModel($name = 'Item', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
        return $model;
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function saveOrder()
	{
		\Joomla\CMS\Session\Session::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$user  = \Secretary\Joomla::getUser();
		$order = $this->app->input->get('order', array(), 'array');
		$msg   = \Joomla\CMS\Language\Text::_('COM_SECRETARY_ORDERING_SAVED_FAILED');
		
        if ($user->authorise('core.admin', 'com_secretary') && !empty($order))
		{
			$db = \Secretary\Database::getDBO();
			$oldOrders = array();
			$oldOrdersTasks = array();
			$start = 1;
			
            foreach ($order as $key => $values)
			{
				foreach ($values as $id)
				{
					$query = "UPDATE `#__secretary_status` SET `ordering` = " . $start . " WHERE extension = " . $db->quote($key) . " AND id =" . (int) $id;
					$db->setQuery($query);
					$db->execute();
					$start++;
                }
            }
			$msg = \Joomla\CMS\Language\Text::_('COM_SECRETARY_ORDERING_SAVED');
		}

		$this->setMessage($msg);
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&amp;view=items&amp;extension=status', false));
	}

	public function deleteFiles()
	{

		\Joomla\CMS\Session\Session::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$files = $this->app->input->get('cid', array(), 'array');
		$user  = \Secretary\Joomla::getUser();

		if ($user->authorise('core.delete', 'com_secretary'))
		{
			$x = 0;
			
            foreach ($files as $file)
			{
				$target = \Secretary\Helpers\Uploads::safeUploadPath($file);
				
                if ($target !== false && is_file($target))
				{
					unlink($target);
					$x++;
                }
			}
			$this->setMessage(\Joomla\CMS\Language\Text::plural('COM_SECRETARY_N_ITEMS_DELETED', $x));
		}

		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&amp;view=items&amp;extension=uploads', false));
	}

}
