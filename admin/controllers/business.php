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

class SecretaryControllerBusiness extends JControllerForm
{

    public function __construct() {
        $this->view_list = 'businesses';
        parent::__construct();
    }
    
    public function getModel($name = 'Business', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
    {
        return Secretary\Model::create($name,$prefix,$config);
    }
    
	protected function allowEdit($data = array(), $key = 'id')
	{
	    $return = \Secretary\Helpers\Access::allowEdit('business',$data, $key);
		return $return;
	}
	
	public function csample()
	{ 
		$user	= JFactory::getUser();
		$business = Secretary\Application::company();
		
		if ( !(\Secretary\Helpers\Access::checkAdmin()) || isset($business) )
		{
			throw new Exception(JText::_('COM_SECRETARY_ERROR_ACCESS'));
			return false;
		}
		
		// Update
		$db = JFactory::getDBO();
        $dbName = $db->name == "postgresql" ? 'postgre' : 'mysql';
        $file = SECRETARY_ADMIN_PATH.'/application/install/samples/sample_business.' . $dbName . '.sql';
		$buffer = file_get_contents($file);
		
		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
			return false;
		}

		// Create an array of queries from the sql file
		//$queries = JDatabaseDriver::splitSql($buffer); // Joomla 3.x+
		$queries = JDatabase::splitSql($buffer);
	
		$update_count = 0;
		if (count($queries) != 0)
		{
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$db->setQuery($query);
					if (!$db->execute())
					{
						JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');
						return false;
					}
					
					$update_count++;
				}
			}
		}
		
		JFactory::getApplication()->enqueueMessage(JText::_('COM_SECRETARY_INSTALL_SAMPLE_DATA_INSTALLED'), 'notice');
		$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='.$this->view_list , false));
    }
	

	public function save($key = NULL, $urlVar = NULL)
	{
		 
		parent::save( );
		
		$task = $this->getTask();
		if($task == 'save') {
			
			$html = array();
			$html[] = '<h3>'. JText::_("COM_SECRETARY_TUTORIAL_FIRST_STEPS") .'</h3><ol>';
    		$link1 = '<a href="index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings">'.JText::_('COM_SECRETARY_SETTINGS').'</a>';
			$html[] = ' <li>'. JText::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_1", $link1) .'</li>';
			$link2 = '<a href="index.php?option=com_secretary&view=folders&extension=documents">'.JText::_('COM_SECRETARY_CATEGORIES').'</a>';
			$html[] = '<li>'. JText::sprintf("COM_SECRETARY_TUTORIAL_FIRST_STEPS_2", $link2) .'</li>';
    		$link3 = '<a href="index.php?option=com_secretary&view=documents&catid=0">'.JText::_('COM_SECRETARY_DOCUMENTS').'</a>'; 
			$html[] = '<li>'. JText::sprintf('COM_SECRETARY_TUTORIAL_FIRST_STEPS_3', $link3) .'</li></ol>';
			$html[] = '<p>'.JText::_("COM_SECRETARY_TUTORIAL_FAQ_LOOK_INSIDE").'</p>';
			
			$message = implode("",$html);
			JFactory::getApplication()->enqueueMessage($message, 'notice');
			$this->setRedirect(JRoute::_('index.php?option=com_secretary&view='.$this->view_list , false));
		}
	}
	
}