<?php

// No direct access
defined('JPATH_BASE') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldEntities extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'entities';

	public function getInput()
	{
		$params = Secretary\Application::parameters();
		
        if ($params->get('entitySelect') != 1)
		{
			$html = '<input id="' . $this->id . '_entity" type="text" class="fullwidth" name="' . $this->name . '" value="' . $this->value . '" />';
		}
        else
		{
			$options = array();
			$items = \Secretary\Database::getObjectList('entities', ['id', 'title'], [], 'title ASC');
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 0, \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION'));
			
            foreach ($items as $message)
			{
				$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, \Joomla\CMS\Language\Text::_($message->title));
            }

			$html = '<div class="select-arrow select-arrow-white">'
				. '<select name="' . $this->name . '" id="' . $this->id . '" class="form-control entity-select">'
				. \Joomla\CMS\HTML\HTMLHelper::_('select.options', $options, 'value', 'text', $this->value)
				. '</select></div>';
		}

		return $html;
	}

	public function getOptions()
	{

		$html = array();

		$params = Secretary\Application::parameters();
		
        if ($params->get('entitySelect') == 1)
		{
			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true)
				->select("id,title")
				->from($db->qn('#__secretary_entities'))
				->order('title ASC');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 0, \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTITY'));
			
            foreach ($items as $message)
			{
				$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, $message->title);
            }
		}

		return $html;
	}

	public function getList($default = 0, $name = 'jform[items][##counter##][entity]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" id="jform_items_entity" class="form-control entity-select">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text') . '</select>';
		
        return $result;
	}
}