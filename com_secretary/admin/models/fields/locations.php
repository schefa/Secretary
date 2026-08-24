<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldLocations extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'locations';

	public function getOptions()
	{
		$user = \Secretary\Joomla::getUser();
		$business = \Secretary\Application::company();
		$extension = (string) $this->element['extension'];
		$html = array();

		$db = \Secretary\Database::getDBO();

		$where = array('business = ' . intval($business['id']));
		
        if (!empty($extension))
		{
			array_push($where, 'extension = ' . $db->quote($extension));
		}

		$items = \Secretary\Database::getObjectList('locations', ['id', 'title'], $where, 'title ASC');

		// Make list
		$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 0, \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION'));
		
        foreach ($items as $message)
		{
			if (
				$user->authorise('core.show', 'com_secretary.location.' . $message->id)
				|| $user->authorise('core.show.other', 'com_secretary.location.' . $message->id)
			)
			{
				$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, $message->title);
            }
		}

		return $html;
	}

	public function getLocations($view, $not = NULL)
	{
		$user = \Secretary\Joomla::getUser();
		$locations = array();
		$business = \Secretary\Application::company();

		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)
			->select("id,title")
			->from($db->quoteName("#__secretary_locations"))
			->where($db->qn('business') . ' = ' . intval($business['id']))
			->where($db->quoteName('extension') . '=' . $db->quote($view))
			->order('title ASC');

		if (!empty($not))
		{
			$query->where($db->quoteName('id') . "!=" . intval($not));
		}
		$db->setQuery($query);
		$locations = $db->loadObjectList();

		for ($i = 0, $n = count($locations ?? []); $i < $n; $i++)
		{
            if (
				$user->authorise('core.show', 'com_secretary.location.' . $locations[$i]->id)
				|| $user->authorise('core.show.other', 'com_secretary.location.' . $locations[$i]->id)
			)
			{
				$locations[$i]->title = '- ' . $locations[$i]->title;
			}
            else
			{
				unset($locations[$i]);
			}
		}

		$title = \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL', \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS_DOCUMENTS'));
		array_unshift($locations, $title);

		return $locations;
	}

	public function getList($default = 0, $name = 'jform[items][##counter##][location]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" id="jform_items_location" class="form-control location-select">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text') . '</select>';
		
        return $result;
	}
}