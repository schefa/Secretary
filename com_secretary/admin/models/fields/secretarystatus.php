<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldSecretarystatus extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'secretarystatus';

	public function getOptions($extension = 'root')
	{
		$db = \Secretary\Database::getDBO();

		if (!empty($this->element['extension']))
		{
			$extension = (string) $this->element['extension'];
		}
        elseif ($mod = \Secretary\Joomla::getApplication()->input->getCmd('module'))
		{
			$extension = $mod;
		}
        else
		{
			$extension = $extension;
		}

		$html = array();

		$query = $db->getQuery(true)
			->select("id,title")
			->from($db->quoteName('#__secretary_status'))
			->where($db->quoteName('extension') . ' = ' . $db->quote($extension))
			->order('ordering ASC, id ASC');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $message)
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $message->id, \Joomla\CMS\Language\Text::_($message->title));
		}

		return $html;
	}
}