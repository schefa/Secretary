<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldProductUsage extends \Joomla\CMS\Form\Field\ListField
{
	protected $type = 'productUsage';

	public function getOptions($default = false)
	{
		$html = array();

		$result = array(
			0 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_NONE'),
			1 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_USAGE_VERBRAUCH'),
			2 => \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_USAGE_EINKAUF'),
		);

		foreach ($result as $key => $value)
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $key, $value);
		}

		if ($default == false)
		{
			return $html;
		}
        else
		{
            return $html[$default];
		}
	}

	public function getList($default, $name = 'jform[fields][pUsage]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" class="form-control inputbox pUsage">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text', $default) . '</select>';
		
        return $result;
	}
}