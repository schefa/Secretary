<?php
/** @version     3.0.0
 *
 * @copyright    Copyright (C) 2026 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

// no direct access
defined('_JEXEC') or die ;

$user = \Joomla\CMS\Factory::getUser();

if (!$user->authorise('core.manage', 'com_secretary'))
{
	return;
}

// Attributes
if (!defined('SECRETARY_ADMIN_PATH'))
{
	define('SECRETARY_ADMIN_PATH', JPATH_ADMINISTRATOR .'/components/com_secretary');
}
$mod_name = "mod_secretary_dashboard";
$document = \Joomla\CMS\Factory::getDocument();

// Version
$update = false;
$xmlPath = SECRETARY_ADMIN_PATH.'/secretary.xml';

if (file_exists($xmlPath))
{
	$xml = simplexml_load_file($xmlPath);
	
	if (version_compare($xml->version,'3.0.0') < 0)
	{
		echo 'Please update to Secretary 3.0.0';
		
		return false;
	}
}
else
{
	echo 'Please install the latest version of Secretary';
	
	return false;
}


// Language files
$language = \Joomla\CMS\Factory::getLanguage();
$language->load('com_secretary', JPATH_ADMINISTRATOR);

// Get Business Data
$business	= array();

if (file_exists(SECRETARY_ADMIN_PATH.'/application/Secretary.php'))
{
    require_once SECRETARY_ADMIN_PATH .'/application/Secretary.php';
	$business	= \Secretary\Application::company();
}

$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'media/secretary/assets/fontawesome/css/all.min.css');
$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'media/secretary/assets/fontawesome/css/v4-shims.min.css');
$document->addStyleSheet(\Joomla\CMS\Uri\Uri::base(true).'/modules/'.$mod_name.'/tmpl/css/style.css?v=3.2');
$document->addScript(\Joomla\CMS\Uri\Uri::base(true).'/modules/'.$mod_name.'/tmpl/js/masonry.js');

require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath($mod_name, 'default');
