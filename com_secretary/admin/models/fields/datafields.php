<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldDatafields extends \Joomla\CMS\Form\Field\ListField
{
	protected $type = 'datafields';

	public function getFieldsArray()
	{
		$result = array(
			'text' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_TEXT'),
			'textarea' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_TEXTAREA'),
			'number' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_NUMBER'),
			'html' => 'HTML',
			'color' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_COLOR'),
			'date' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_DATE'),
			'list' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_LIST'),
			'url' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_URL'),
		);
		
        if (\Secretary\Helpers\Access::checkAdmin())
		{
			$result['sql'] = \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_SQL');
			$result['search'] = 'search';
		}
		
        return $result;
	}

	public function getResult($key)
	{
		$result = $this->getFieldsArray();
		
        return $result[$key];
	}

	public function getOptions()
	{
		$result = $this->getFieldsArray();
		
        foreach ($result as $key => $value)
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $key, $value);
		}
		
        return $result;
	}
}