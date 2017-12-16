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


JFormHelper::loadFieldClass('list');

class JFormFieldCurrency extends JFormFieldList
{
	
	protected $type = 'currency';
	
	public function getInput( )
	{
		$options = array();
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
		->select("currency, CONCAT(symbol, ' (' , title , ')') as value")
		->from($db->quoteName('#__secretary_currencies'))
		->order('title ASC');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach($items as $message) {
			$options[] = JHtml::_('select.option', $message->currency, $message->value );
		}
	
		$html = '<div class="select-arrow select-arrow-white"><select name="'.$this->name.'" id="'.$this->id.'" class="form-control currency-select">'. JHtml::_('select.options', $options, 'value', 'text', $this->value) . '</select></div>';
		
		return $html;
		
	}
	
}