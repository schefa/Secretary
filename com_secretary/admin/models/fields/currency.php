<?php

// No direct access
defined('JPATH_BASE') or die;


\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCurrency extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'currency';

	/**
	 * Method to return a list of all available currencies
	 * 
	 * {@inheritDoc}
	 * @see JFormFieldList::getInput()
	 */
	public function getInput()
	{
		$options = array();

		$items = \Secretary\Database::getObjectList('currencies', ['currency', "CONCAT(symbol,' (',title,')') as value"], [], 'title ASC');
		
        foreach ($items as $message)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->currency, $message->value);
		}

		$html = '<div class="select-arrow select-arrow-white">'
			. '<select name="' . $this->name . '" id="' . $this->id . '" class="form-control currency-select">'
			. \Joomla\CMS\HTML\HTMLHelper::_('select.options', $options, 'value', 'text', $this->value)
			. '</select></div>';

		return $html;
	}
}