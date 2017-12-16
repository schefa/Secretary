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
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldBusinesses extends JFormFieldList
{
	
	protected $type = 'businesses';
	
	public function getOptions( )
	{
		$html = array();
		
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select("id,title");
        $query->from($db->qn('#__secretary_businesses'));
        $query->order('id ASC');
        
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, $message->title );
		}
		
		return $html;
	}
	
	public function getList( $default, $name = 'jform[fields][template]' )
	{
		$html = $this->getOptions();
		$result =	'<select name="'.$name.'" class="form-control inputbox">'. JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
}