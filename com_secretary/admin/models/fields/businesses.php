<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldBusinesses extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'businesses';

	public function getOptions()
	{
		$html = array();
		$items = \Secretary\Database::getObjectList('businesses', ['id', 'title']);
		
        foreach ($items as $message)
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, $message->title);
		}

		return $html;
	}

	public function getList($default, $name = 'jform[fields][template]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" class="form-control inputbox">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text', $default) . '</select>';
		
        return $result;
	}
}