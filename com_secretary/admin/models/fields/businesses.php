<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

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
}