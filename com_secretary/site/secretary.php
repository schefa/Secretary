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
$layout    = $app->input->getCmd('layout');

// Read the task the way BaseController::getInstance() does (including the
// task[controller.task]= array form), so the gate checks the controller that
// actually runs.
$task    = $app->input->get('task', '');

if (is_array($task))
{
    $keys = array_keys($task);
    $task = array_pop($keys);
}
$task    = \Joomla\CMS\Filter\InputFilter::getInstance()->clean((string) $task, 'cmd');
$parts  = explode(".", $task);

if (count($parts) > 1)
{
    // A controller.task request runs that controller whatever view it names, so
    // the view - dashboard by default, which is open to guests - says nothing
    // about it. ajax and dashboard (deleting activities, checked again in the
    // model) work on the business; other controllers outside the ACL sections
    // are backend-only.
    $single = Secretary\Application::getSingularSection($parts[0]);
    $canSee = in_array($parts[0], array('ajax', 'dashboard'))
        ? $user->authorise('core.show', 'com_secretary.business')
        : (!empty($single) && $user->authorise('core.show', 'com_secretary.' . $single));
}
else
{
    $single = Secretary\Application::getSingularSection($view);
    $canSee    = $user->authorise('core.show', 'com_secretary.' . $single);

    if ($view === 'dashboard')
    {
        $canSee = true;
    }
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
