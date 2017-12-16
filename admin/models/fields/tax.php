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
JFormHelper::loadFieldClass('list');

class JFormFieldTax extends JFormField
{
	protected $type = 'tax';

	protected function getOptions()
	{
		$options = array( 1 => 'COM_SECRETARY_INKLUSIV', 2 => 'COM_SECRETARY_EXKLUSIV');
		$html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value);
		return $html;
	}
	
}