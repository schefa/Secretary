<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
// no direct access
defined('_JEXEC') or die;

class JFormFieldCategoriesMultiselect extends JFormField
{

	var $type = 'categoriesmultiselect';

	function getInput( $extension = false , $name = false, $selected = false )
	{
		$folders = array();
		if(empty($extension))
			$extension = (string) $this->element['extension'];
	 	$business	= \Secretary\Application::company();
	 	$user		= \Secretary\Joomla::getUser();
	
		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)->select("id AS value, title")
				->from($db->quoteName("#__secretary_folders"))
				->where($db->quoteName("business").' = '. intval($business['id']))
				->where($db->quoteName("level")." > 0");
		if(!empty($extension)) $query->where($db->quoteName('extension')."=". $db->quote( $extension ));	
		
		
		$db->setQuery($query);
		$folders = $db->loadObjectList();
		
		for ($i = 0; $i < count($folders); $i++) {
			if($user->authorise('core.show','com_secretary.folder.'.$folders[$i]->value) 
			|| $user->authorise('core.show.other','com_secretary.folder.'.$folders[$i]->value))
			{
				$folders[$i]->title = JText::_($folders[$i]->title);
			} else {
				unset($folders[$i]);	
			}
		}
		
		if(!empty($name)) $this->name = $name;
		if(!empty($selected)) $this->value = $selected;
		
		// Output
		return JHTML::_('select.genericlist', $folders, $this->name.'[]', 'class="inputbox" style="width:220px;" multiple="multiple" size="6"', 'value', 'title', $this->value);
	}

}

