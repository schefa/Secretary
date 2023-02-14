<?php

/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class SecretaryControllerBusiness extends JControllerForm
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
		$user = \Secretary\Joomla::getUser();
		$business = \Secretary\Application::company();

		if (!(\Secretary\Helpers\Access::checkAdmin()) || isset($business)) {
			throw new Exception(JText::_('COM_SECRETARY_ERROR_ACCESS'));
			return false;
		}

		// Update
		$db = \Secretary\Database::getDBO();
		$dbName = $db->name == "postgresql" ? 'postgre' : 'mysql';
		$file = SECRETARY_ADMIN_PATH . '/application/install/samples/sample_business.' . $dbName . '.sql';
		$buffer = file_get_contents($file);

		// Graceful exit and rollback if read not successful
		if ($buffer === false) {
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
			return false;
		}

		// Create an array of queries from the sql file
		//$queries = JDatabaseDriver::splitSql($buffer); // Joomla 3.x+
		$queries = JDatabase::splitSql($buffer);

		$update_count = 0;
		if (count($queries ?? []) != 0) {
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query[0] != '#') {
					$db->setQuery($query);
					if (!$db->execute()) {
						JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');
						return false;
					}

					$update_count++;
				}
			}
		}

		\Secretary\Joomla::getApplication()->enqueueMessage(JText::_('COM_SECRETARY_INSTALL_SAMPLE_DATA_INSTALLED'), 'notice');
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=' . $this->view_list, false));
	}


	public function save($key = NULL, $urlVar = NULL)
	{

		parent::save();

		$task = $this->getTask();
		if ($task == 'save') {

			$html = array();
			$html[] = '<h3>' . JText::_("COM_SECRETARY_TUTORIAL_FIRST_STEPS") . '</h3><ol>';
			$link1 = '<a href="index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings">' . JText::_('COM_SECRETARY_SETTINGS') . '</a>';
			$html[] = ' <li>' . JText::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_1", $link1) . '</li>';
			$link2 = '<a href="index.php?option=com_secretary&view=folders&extension=documents">' . JText::_('COM_SECRETARY_CATEGORIES') . '</a>';
			$html[] = '<li>' . JText::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_2", $link2) . '</li>';
			$link3 = '<a href="index.php?option=com_secretary&view=documents&catid=0">' . JText::_('COM_SECRETARY_DOCUMENTS') . '</a>';
			$html[] = '<li>' . JText::sprintf('COM_SECRETARY_TUTORIAL_FIRST_STEPS_3', $link3) . '</li></ol>';
			$html[] = '<p>' . JText::_("COM_SECRETARY_TUTORIAL_FAQ_LOOK_INSIDE") . '</p>';

			$message = implode("", $html);
			\Secretary\Joomla::getApplication()->enqueueMessage($message, 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_secretary&view=' . $this->view_list, false));
		}
	}
}