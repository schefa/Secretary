<?php

// No direct access
defined('_JEXEC') or die;

class JFormFieldCategoriesMultiselect extends \Joomla\CMS\Form\FormField
{

	var $type = 'categoriesmultiselect';

	function getInput($extension = false, $name = false, $selected = false)
	{
		$folders = array();
		
        if (empty($extension))
		{
			$extension = (string) $this->element['extension'];
		}
		$business = \Secretary\Application::company();
		$user = \Secretary\Joomla::getUser();

		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)->select("id AS value, title")
			->from($db->quoteName("#__secretary_folders"))
			->where($db->quoteName("business") . ' = ' . intval($business['id']))
			->where($db->quoteName("level") . " > 0");
		
        if (!empty($extension))
		{
			$query->where($db->quoteName('extension') . "=" . $db->quote($extension));
		}


		$db->setQuery($query);
		$folders = $db->loadObjectList();

		for ($i = 0; $i < count($folders ?? []); $i++)
		{
            if (
				$user->authorise('core.show', 'com_secretary.folder.' . $folders[$i]->value)
				|| $user->authorise('core.show.other', 'com_secretary.folder.' . $folders[$i]->value)
			)
			{
				$folders[$i]->title = \Joomla\CMS\Language\Text::_($folders[$i]->title);
			}
            else
			{
				unset($folders[$i]);
			}
		}

		if (!empty($name))
		{
			$this->name = $name;
		}
		
        if (!empty($selected))
		{
			$this->value = $selected;
		}

		// Output
		return \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $folders, $this->name . '[]', 'class="inputbox" style="width:220px;" multiple="multiple" size="6"', 'value', 'title', $this->value);
	}
}