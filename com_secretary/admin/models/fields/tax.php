<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldTax extends \Joomla\CMS\Form\FormField
{
	protected $type = 'tax';

	protected function getOptions()
	{
		$options = array(1 => 'COM_SECRETARY_INKLUSIV', 2 => 'COM_SECRETARY_EXKLUSIV');
		$html = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value);
		
        return $html;
	}
}