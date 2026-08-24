<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the required admin language files
$lang = \Joomla\CMS\Factory::getLanguage();
$lang->load('joomla', JPATH_ADMINISTRATOR);
$lang->load('com_secretary', JPATH_ADMINISTRATOR);

// Framework
require_once  JPATH_ADMINISTRATOR . '/components/com_secretary/application/Secretary.php';

$user    = \Secretary\Joomla::getUser();
$app    = \Secretary\Joomla::getApplication();
$view    = $app->input->getCmd('view', 'dashboard');
$task    = $app->input->getCmd('task');
$layout    = $app->input->getCmd('layout');
$parts  = explode(".", $task);

$single = Secretary\Application::getSingularSection($view);
$canSee    = $user->authorise('core.show', 'com_secretary.' . $single);

if (in_array($view, array('dashboard')) || in_array($parts[0], array('ajax')))
{
    $canSee = $user->authorise('core.show', 'com_secretary.business');
}

if ($view === 'dashboard')
{
    $canSee = true;
}

/********************************************
 ************		Display       ************
 *********************************************/

if (true === boolval($canSee))
{
    include_once JPATH_ADMINISTRATOR . '/components/com_secretary/secretary.php';
}
else
{
    echo '<div class="alert alert-danger">' . \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR') . '</div>';
    
    return false;
}
