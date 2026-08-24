<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerBusiness extends \Joomla\CMS\MVC\Controller\FormController
{

	public function __construct()
	{
		$this->view_list = 'businesses';
		parent::__construct();
	}

	public function getModel($name = 'Business', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$return = \Secretary\Helpers\Access::allowEdit('business', $data, $key);
		
        return $return;
	}

	public function csample()
	{
		\Joomla\CMS\Session\Session::checkToken('get') or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$user = \Secretary\Joomla::getUser();
		$business = \Secretary\Application::company();

		if (!(\Secretary\Helpers\Access::checkAdmin()) || isset($business))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('COM_SECRETARY_ERROR_ACCESS'));
			
            return false;
		}

		// Update
		$db = \Secretary\Database::getDBO();
		$dbName = $db->name == "postgresql" ? 'postgre' : 'mysql';
		$file = SECRETARY_ADMIN_PATH . '/application/install/samples/sample_business.' . $dbName . '.sql';
		$buffer = file_get_contents($file);

		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), 'warning');
			
            return false;
		}

		// Create an array of queries from the sql file
		$queries = \Joomla\Database\DatabaseDriver::splitSql($buffer);

		$update_count = 0;
		
        if (count($queries ?? []) != 0)
		{
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);
				
                if ($query != '' && $query[0] != '#')
				{
					$db->setQuery($query);
					
                    if (!$db->execute())
					{
						\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), \Joomla\CMS\Log\Log::WARNING, 'jerror');
						
                        return false;
                    }

					$update_count++;
				}
            }
		}

		\Secretary\Joomla::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_INSTALL_SAMPLE_DATA_INSTALLED'), 'notice');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list, false));
	}


	public function save($key = NULL, $urlVar = NULL)
	{

		parent::save();

		$task = $this->getTask();
		
        if ($task == 'save')
		{
			$html = array();
			$html[] = '<h3>' . \Joomla\CMS\Language\Text::_("COM_SECRETARY_TUTORIAL_FIRST_STEPS") . '</h3><ol>';
			$link1 = '<a href="index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_SETTINGS') . '</a>';
			$html[] = ' <li>' . \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_1", $link1) . '</li>';
			$link2 = '<a href="index.php?option=com_secretary&view=folders&extension=documents">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES') . '</a>';
			$html[] = '<li>' . \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_2", $link2) . '</li>';
			$link3 = '<a href="index.php?option=com_secretary&view=documents&catid=0">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS') . '</a>';
			$html[] = '<li>' . \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_TUTORIAL_FIRST_STEPS_3', $link3) . '</li></ol>';
			$html[] = '<p>' . \Joomla\CMS\Language\Text::_("COM_SECRETARY_TUTORIAL_FAQ_LOOK_INSIDE") . '</p>';

			$message = implode("", $html);
			\Secretary\Joomla::getApplication()->enqueueMessage($message, 'notice');
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list, false));
		}
	}
}