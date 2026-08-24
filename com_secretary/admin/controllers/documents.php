<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerDocuments extends Secretary\Controller\Admin
{
	protected $app;
	protected $catid;
	protected $view;
	protected $redirect_url;

	public function __construct()
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->catid = $this->app->input->getInt('catid');
		$this->view = $this->app->input->getCmd('view', 'documents');
		$this->redirect_url = 'index.php?option=com_secretary&amp;view=' . $this->view . '&amp;catid=' . $this->catid;
		parent::__construct();
	}

	public function getModel($name = 'Document', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
        return $model;
	}

	public function acquit()
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.edit', 'com_secretary.document'))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			
            return false;
		}

		$pk = $this->app->input->getInt('cid');
		$return = \Secretary\Helpers\Documents::acquit($pk);
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function updateProducts()
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.delete', 'com_secretary.product'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			
            return false;
		}

		$pks = $this->app->input->get('cid', array(), 'array');

		if (empty($pks))
		{
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_NO_ITEM_SELECTED'), 'error');
		}
        else
		{
			$return = \Secretary\Helpers\Products::updateProducts($pks);
			$msg = \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DOCUMENTS_PRODUCTS_UPDATE', implode(', ', $return));
			$this->setMessage($msg);
		}

		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function updateRepetitions()
	{
		$user = \Secretary\Joomla::getUser();
		
        if (!$user->authorise('core.create', 'com_secretary.document') || !$user->authorise('core.create', 'com_secretary.time'))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			
            return false;
		}

		$pks = $this->app->input->get('cid', array(), 'array');

		if (!empty($pks))
		{
			$msg = \Secretary\Helpers\Times::updateRepetitions("documents", $pks);
			$this->setMessage($msg);
		}
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->repetitionRedirectUrl(), false));
	}

	public function postDeleteUrl()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->redirect_url, false));
	}

	public function deleteRepetitions()
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.delete', 'com_secretary.time'))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 100);
			
            return false;
		}

		$pks = $this->app->input->get('cid', array(), 'array');

		if (!empty($pks))
		{
			$table = \Joomla\CMS\Table\Table::getInstance('Repetition', 'SecretaryTable');
			
            foreach ($pks as $pk)
			{
				$table->delete((int) $pk);
				$table->reset();
			}
		}

		$this->setRedirect(\Joomla\CMS\Router\Route::_($this->repetitionRedirectUrl(), false));
	}

	/**
	 * updateRepetitions()/deleteRepetitions() are only ever submitted from the
	 * repetition popup, which loads as a standalone tmpl=component <iframe>
	 * document (see secretary.php). Redirecting to $this->redirect_url (the
	 * full chrome admin list) would navigate that iframe away from the popup,
	 * so these two send the user back into the popup itself instead.
	 */
	protected function repetitionRedirectUrl()
	{
		return 'index.php?option=com_secretary&amp;view=documents&amp;layout=repetition&amp;tmpl=component&amp;catid=' . $this->catid;
	}
}