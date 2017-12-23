<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 * @author      Fjodor Schaefer - https://www.schefa.com
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
 
class SecretaryControllerLanguage extends JControllerForm
{
    
    protected $app;
    
	public function __construct($config = array())
	{
		if(!JFactory::getUser()->authorise('core.admin', 'com_secretary'))
		    die;
		
		$this->app = \Secretary\Joomla::getApplication();
		parent::__construct($config);
	}
	
	public function save($key = null, $urlVar = null)
	{   
	    $data				= $this->app->input->get('jform','','array');
	    $filter_language	= $this->app->input->getVar('filter_language');
		 
		$model = $this->getModel('language');
		$model->save($data);
		
		$this->setRedirect('index.php?option=com_secretary&view=language&filter_language=' . $filter_language, JText::_('COM_SECRETARY_TRANSLATIONS_SAVED'));
	}
	
	public function share( )
	{ 
		$lang 				= JFactory::getLanguage();
		
		$filter_language	= $this->app->getUserStateFromRequest('com_secretary.filter_language', 'filter_language', $lang->getName(), 'string');
		$data				= $this->app->input->get('jform','','array');
		
		$model = $this->getModel('language');
		$content = $model->makeFile($data,true);
		
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=".$filter_language.".com_secretary.ini");
		
		print $content;
		
		$this->app->close();
	}
	
	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_secretary&view=dashboard');
	}
}
