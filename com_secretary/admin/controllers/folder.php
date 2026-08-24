<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerFolder extends \Joomla\CMS\MVC\Controller\FormController
{

	protected $extension;

	public function __construct($config = array())
	{
		parent::__construct($config);

		if (empty($this->extension))
		{
			$this->extension = \Secretary\Joomla::getApplication()->input->getCmd('extension', 'documents');
		}
	}

	public function getModel($name = 'Folder', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$return = \Secretary\Helpers\Access::allowEdit('folder', $data, $key);
		
        return $return;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;
		
        return $append;
	}

	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;
		
        return $append;
	}

	public function batch($model = null)
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$model = $this->getModel('Folder');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
		
        return parent::batch($model);
	}
}