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
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**************		Access       ************/
$app	= JFactory::getApplication();
$view	= $app->input->getVar('view');
$format	= $app->input->getVar('format');
$layout	= $app->input->getVar('layout');
$task	= $app->input->getVar('task');

if ($app->isClient('administrator') && !JFactory::getUser()->authorise('core.manage', 'com_secretary'))  {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
	return false;
}

/********* Dependancies ************/

jimport('joomla.application.component.controller');

// Framework
require_once  JPATH_ADMINISTRATOR .'/components/com_secretary/application/Secretary.php';

// Helpers 
Secretary\Application::loadFunctionsFromFolder(JPATH_COMPONENT_ADMINISTRATOR .'/helpers/');
JTable::addIncludePath(JPATH_SITE .'/administrator/components/com_secretary/models/tables');
JForm::addFormPath(JPATH_SITE .'/administrator/components/com_secretary/models/forms');

$pdf = Secretary\PDF::getInstance(); 

define('SECRETARY_VERSION', Secretary\Application::getVersion());
define('COM_SECRETARY_PDF', (null !== $pdf->getStrategy()));

/********** Head ********************/

$document = JFactory::getDocument();
if($format != 'raw') { 
	$mediaURL = JURI::root() .'media/secretary';
	JHtml::_('jquery.framework');
	
	$timestamp = '?'.strtotime(date('Y-m-d'));
	$document->addScript($mediaURL.'/assets/jquery/jquery-ui.min.js');
	$document->addScript($mediaURL.'/js/secretary.js?v='.SECRETARY_VERSION);
	if( $view == 'document' && $layout == 'edit' ) {
	    $document->addScript($mediaURL.'/js/secretary.document.js?v='.SECRETARY_VERSION);
		$document->addScript($mediaURL.'/assets/jquery/jquery.nestable.js');
	}

	if($layout == 'edit') {
		JHtml::_('behavior.formvalidation');
		JHtml::_('behavior.keepalive');
	}
	
	JHTML::_('behavior.modal');
	
	$document->addStyleSheet($mediaURL.'/assets/jquery/jquery-ui.css');
	$document->addStyleSheet($mediaURL.'/css/secretary.css?v='.SECRETARY_VERSION);
	$document->addStyleSheet($mediaURL.'/css/custom.css?v='.SECRETARY_VERSION);
	$document->addStyleSheet($mediaURL.'/fontawesome/css/font-awesome.min.css');
	\Secretary\Html::_('layout.templateCssStyle'); 
}

$title = 'Secretary';
if(!empty($view)) $title .= ' - '. JText::_('COM_SECRETARY_'.$view);
$document->setTitle($title);
			
/************		Display       ************/

//$legacy = new JControllerLegacy(array('base_path'=>JPATH_COMPONENT_ADMINISTRATOR,'table_path'=> JPATH_COMPONENT_ADMINISTRATOR));
$controller	= JControllerLegacy::getInstance('Secretary',array('base_path'=>JPATH_COMPONENT_ADMINISTRATOR));
$controller->execute($app->input->get('task'));
$controller->redirect();
