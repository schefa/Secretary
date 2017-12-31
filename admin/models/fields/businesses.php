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
		$items = \Secretary\Database::getObjectList('businesses',['id','title']); 
		foreach($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, $message->title );
		}
		
		return $html;
	}
	
	public function getList( $default, $name = 'jform[fields][template]' )
	{
		$html = $this->getOptions();
		$result = '<select name="'.$name.'" class="form-control inputbox">'.JHtml::_('select.options', $html,'value','text',$default).'</select>';
		return $result;
	}
}