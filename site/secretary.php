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
 * 
 */

defined('_JEXEC') or die;

// Load the required admin language files
$lang = JFactory::getLanguage();
$lang->load('joomla', JPATH_ADMINISTRATOR);
$lang->load('com_secretary', JPATH_ADMINISTRATOR);

// Framework
require_once  JPATH_ADMINISTRATOR .'/components/com_secretary/application/Secretary.php';

$user	= \Secretary\Joomla::getUser();
$app	= \Secretary\Joomla::getApplication();
$view	= $app->input->getCmd('view','dashboard');
$task	= $app->input->getCmd('task');
$layout	= $app->input->getCmd('layout');
$parts  = explode(".",$task);

$single = Secretary\Application::getSingularSection($view);
$canSee	= $user->authorise('core.show','com_secretary.'.$single);
if(in_array($view,array('dashboard')) || in_array($parts[0],array('ajax'))) 
    $canSee = $user->authorise('core.show','com_secretary.business');
if(($view === 'message' && $layout === 'form') OR $view === 'dashboard' OR $view === 'messages')
    $canSee = true;
    
/********************************************
 ************		Display       ************
 *********************************************/
    
if(true === boolval($canSee)) {
    include_once ( JPATH_ADMINISTRATOR .'/components/com_secretary/secretary.php');
} else {
    echo '<div class="alert alert-danger">'.JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
}
