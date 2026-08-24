<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldTemplates extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'templates';
	protected static $_items = array();

	public function getOptions($only = array())
	{

		$html = array();

		if (empty(self::$_items))
		{
			$extension = $this->element ? (string) $this->element['extension'] : NULL;
			$business = Secretary\Application::company();

			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true)
				->select("id,title")
				->from($db->quoteName('#__secretary_templates'))
				->where($db->qn('business') . ' = ' . intval($business['id']));

			if (!empty($only))
			{
				$query->where('extension LIKE (' . $db->quote(implode('","', $only)) . ')');
			}

			$query->order('id ASC');

			if (!empty($extension))
			{
				$query->where($db->quoteName('extension') . '=' . $db->quote($extension));
			}
			
            try
			{
				$db->setQuery($query);
				self::$_items = $db->loadObjectList();
			}
            catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		$items = self::$_items;

		$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 0, \Joomla\CMS\Language\Text::_("COM_SECRETARY_NONE"));
		
        foreach ($items as $message)
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, \Joomla\CMS\Language\Text::_($message->title));
		}

		return $html;
	}

	public function getList($default, $name = 'jform[fields][template]', $class = "", $only = array())
	{
		$html = $this->getOptions($only);
		$result = '<select name="' . $name . '" class="form-control inputbox ' . $class . '">' . \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text', $default) . '</select>';
		
        return $result;
	}
}