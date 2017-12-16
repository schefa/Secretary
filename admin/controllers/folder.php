<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class SecretaryControllerFolder extends JControllerForm
{
	
	protected $extension;
	
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (empty($this->extension))
		{
			$this->extension = JFactory::getApplication()->input->getCmd('extension', 'documents');
		}
	}

	public function getModel($name = 'Folder', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
	    return Secretary\Model::create($name,$prefix,$config);
	}
	
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $return = \Secretary\Helpers\Access::allowEdit('folder',$data, $key);
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
	    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	    $model = $this->getModel('Folder');
	    $this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
	    return parent::batch($model);
	}
}
