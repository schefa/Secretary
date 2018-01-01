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
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once SECRETARY_ADMIN_PATH.'/application/Secretary.php';

class JFormFieldCategories extends JFormFieldList
{
	protected $type = 'categories';

	public function getOptions()
	{
	    
	    $user	    = \Secretary\Joomla::getUser();
	    $app        = \Secretary\Joomla::getApplication();
	 	$business	= \Secretary\Application::company();
		$categories = array();
		
		$db	      = \Secretary\Database::getDBO();
		$query    = $db->getQuery(true);
		
		$query->select("id,title,parent_id,level,state");
		$query->select("id AS value,title AS text");
		$query->from($db->quoteName("#__secretary_folders"));
		$query->where('business = '. intval($business['id']));
		
		// Filter by the type
		if ($extension = $this->element['extension']) {
			$additional = "";
			if($extension == 'templates') { $additional = ",". $db->quote("newsletters"); }
			$query->where('('.$db->quoteName("extension").' IN ( ' . $db->quote($extension) . $additional .'))');
		}
		
		$query->where($db->quoteName("level")." > 0");
		
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		$categories = \Secretary\Helpers\Folders::reorderFolderItems( $categories );
		
		if(!empty($extension)) { $view = $extension; } else { $view = $app->input->get('view');}
		
		for ($i = 0, $n = count($categories); $i < $n; $i++) {
			if($user->authorise('core.show','com_secretary.folder.'.$categories[$i]->id) 
			|| $user->authorise('core.show.other','com_secretary.folder.'.$categories[$i]->id))
			{
				$categories[$i]->text = str_repeat('- ', $categories[$i]->level) . JText::_($categories[$i]->text);
			} else {
				unset($categories[$i]);	
			}
		}
		
		switch($view) {
			case 'documents' : $defaultText = JText::_('COM_SECRETARY_FILTER_DOCUMENTS'); break;
			case 'subjects' : $defaultText = JText::_('COM_SECRETARY_GROUPS_ALL'); break;
			case 'messages' : $defaultText = JText::_('COM_SECRETARY_MESSAGES_ALL'); break;
			case 'markets' : $defaultText = JText::_('COM_SECRETARY_WATCHLISTS_ALL'); break;
			case 'templates' : $defaultText = JText::_('COM_SECRETARY_CATEGORIES_ALL'); break;
			default : $defaultText = JText::_('COM_SECRETARY_CATEGORIES_NOPARENT'); break;
		}
		
		array_unshift(	$categories , $defaultText );
    	return $categories;
		
	}
	
	public function getCategories( $view, $not = NULL )
	{
        $user	    = \Secretary\Joomla::getUser();
	 	$business	= \Secretary\Application::company();
		$categories = array();
		
		$db		= \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		
		$query->select("id as value,title as text,id,title,level,parent_id,state");
		$query->from($db->quoteName("#__secretary_folders"));
		$query->where($db->quoteName("business").' = '. intval($business['id']));
		$query->where($db->quoteName('extension')."=". $db->quote( $view ));
		$query->where($db->quoteName("level")." > 0");
		
		if(!empty($not)) $query->where($db->quoteName('id')."!=". intval( $not ));
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		$categories = \Secretary\Helpers\Folders::reorderFolderItems( $categories );
		
		for ($i = 0, $n = count($categories); $i < $n; $i++) {
			if($user->authorise('core.show','com_secretary.folder.'.$categories[$i]->id) 
			|| $user->authorise('core.show.other','com_secretary.folder.'.$categories[$i]->id))
			{
				$categories[$i]->title = str_repeat('- ', $categories[$i]->level) . JText::_($categories[$i]->title);
			} else {
				unset($categories[$i]);	
			}
		}
		
		switch($view) {
			case 'documents' : $defaultText = JText::_('COM_SECRETARY_FILTER_DOCUMENTS'); break;
			case 'messages' : $defaultText = JText::_('COM_SECRETARY_MESSAGES_ALL'); break;
			case 'subjects' : $defaultText = JText::_('COM_SECRETARY_GROUPS_ALL'); break;
			case 'markets' : $defaultText = JText::_('COM_SECRETARY_WATCHLISTS_ALL'); break;
			case 'templates' : $defaultText = JText::_('COM_SECRETARY_CATEGORIES_ALL'); break;
			case 'times' : $defaultText = JText::_('COM_SECRETARY_CATEGORIES_ALL'); break;
			default : $defaultText = JText::_('COM_SECRETARY_CATEGORIES_NOPARENT'); break;
		}
		
		array_unshift($categories, $defaultText);
    	return $categories;
	}
	
    public function getNewsletter( $selected = false )
	{
		$db	    = \Secretary\Database::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select("id,title");
		$query->from($db->qn('#__secretary_folders'));
		$query->where($db->qn('extension').' = '.$db->quote("newsletters"));
				
		if(!empty($selected))
			$query->where('id='.$db->escape($selected) );
			
		$db->setQuery($query);
		return $db->loadObjectList();
			
	}
}