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

class JFormFieldProductUsage extends JFormFieldList
{
	protected $type = 'productUsage';
	
	public function getOptions( $default = false )
	{
		$html = array();
		
		$result = array( 
			0 => JText::_('COM_SECRETARY_NONE'),
			1 => JText::_('COM_SECRETARY_PRODUCT_USAGE_VERBRAUCH'),
			2 => JText::_('COM_SECRETARY_PRODUCT_USAGE_EINKAUF'),
		);

		foreach($result as $key=>$value) {
			$html[] = JHtml::_('select.option', $key, $value );
		}
		
		if($default == false) {
			return $html;
		} else {
			return $html[$default];
		}
	}
	
	public function getList( $default, $name = 'jform[fields][pUsage]' )
	{
		$html = $this->getOptions();
		$result =	'<select name="'.$name.'" class="form-control inputbox pUsage">'. JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
	
}