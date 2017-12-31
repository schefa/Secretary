<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldLocations extends JFormFieldList
{
	
	protected $type = 'locations';
	
	public function getOptions( )
	{
		
        $user		= JFactory::getUser();
	 	$business	= Secretary\Application::company();
		$extension = (string) $this->element['extension'];
		$html = array();
		
		$db = \Secretary\Database::getDBO(); 
 
		$where = array('business = '. intval($business['id'])); 
		if(!empty($extension)) {
		    array_push($where,'extension = '.$db->quote($extension));
		}
		
		$items = \Secretary\Database::getObjectList('locations',['id','title'],$where,'title ASC'); 
		
		// Make list
		$html[] = JHtml::_('select.option', 0, JText::_('COM_SECRETARY_SELECT_OPTION') );
		foreach($items as $message) {
			if($user->authorise('core.show','com_secretary.location.'.$message->id) 
			|| $user->authorise('core.show.other','com_secretary.location.'.$message->id))
			{
				$html[] = JHtml::_('select.option', $message->id, $message->title );
			}
		}
		
		return $html;
		
	}
	
	public function getLocations( $view, $not = NULL )
	{
        $user		= JFactory::getUser();
		$locations	= array();
	 	$business	= Secretary\Application::company();
		
		$db		= \Secretary\Database::getDBO();
		$query = $db->getQuery(true)
				->select("id,title")
				->from($db->quoteName("#__secretary_locations"))
				->where($db->qn('business').' = '. intval($business['id']))
				->where($db->quoteName('extension').'='. $db->quote( $view ))
				->order('title ASC');
		
		if(!empty($not)) $query->where($db->quoteName('id')."!=". intval( $not ));
		$db->setQuery($query);
		$locations = $db->loadObjectList();
		 
		for ($i = 0, $n = count($locations); $i < $n; $i++) {
			if($user->authorise('core.show','com_secretary.location.'.$locations[$i]->id) 
			|| $user->authorise('core.show.other','com_secretary.location.'.$locations[$i]->id))
			{
				$locations[$i]->title = '- ' . $locations[$i]->title;
			} else {
				unset($locations[$i]);	
			}
		}

		$title = JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL',JText::_('COM_SECRETARY_LOCATIONS_DOCUMENTS'));
		array_unshift($locations,$title);
		
    	return $locations;
	}
	
	public function getList( $default = 0, $name = 'jform[items][##counter##][location]' )
	{
		$html = $this->getOptions();
		$result = '<select name="'.$name.'" id="jform_items_location" class="form-control location-select">'. JHtml::_('select.options', $html, 'value', 'text') . '</select>';
		return $result;
	}
}