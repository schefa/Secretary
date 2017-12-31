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

class JFormFieldDatafields extends JFormFieldList
{
	protected $type = 'datafields';

	public function getFieldsArray()
	{
		$result = array( 
			'text' => JText::_('COM_SECRETARY_FIELD_TEXT'),
			'textarea' => JText::_('COM_SECRETARY_FIELD_TEXTAREA'),
			'number' => JText::_('COM_SECRETARY_FIELD_NUMBER'),
			'html' => 'HTML',
			'color' => JText::_('COM_SECRETARY_FIELD_COLOR'),
			'date' => JText::_('COM_SECRETARY_FIELD_DATE'),
			'list' => JText::_('COM_SECRETARY_FIELD_LIST'),
			'url' => JText::_('COM_SECRETARY_FIELD_URL'),
		);
		if(\Secretary\Helpers\Access::checkAdmin()) {
			$result['sql'] = JText::_('COM_SECRETARY_FIELD_SQL');
			$result['search'] = 'search';
			$result['accounts_tax'] = 'accounts_tax';
		}
		return $result; 
	}
	 
	public function getResult( $key )
	{
		$result = $this->getFieldsArray();
		return $result[$key];
	}
	
	public function getOptions()
	{
		$result = $this->getFieldsArray();
		foreach($result as $key=>$value) {
			$html[] = JHtml::_('select.option', $key, $value );
		}
		return $result;
	}
}