<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldSecretarySections extends \Joomla\CMS\Form\Field\ListField
{

	protected $type = 'secretarysections';

	public function getIcons()
	{
		return $result = array(
			'system' => ('---'),
			'businesses' => '<i class="fa fa-home"></i>',
			'folders' => '<i class="fa fa-folders-o"></i>',
			'documents' => '<i class="fa fa-file-o"></i>',
			'subjects' => '<i class="fa fa-users"></i>',
			'products' => '<i class="fa fa-shopping-cart"></i>',
			'newsletters' => '<i class="fa fa-newspaper-o"></i>',
			'locations' => '<i class="fa fa-cube"></i>',
			'templates' => '<i class="fa fa-print"></i>',
			'times' => '<i class="fa fa-calendar"></i>',
		);
	}

	public function getModulesArray()
	{
		return $result = array(
			'system' => ('---'),
			'businesses' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESSES'),
			'folders' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_FOLDERS'),
			'documents' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS'),
			'subjects' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS'),
			'products' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCTS'),
			'newsletters' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_NEWSLETTER'),
			'locations' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATIONS'),
			'templates' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATES'),
			'times' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_MANAGEMENT'),
		);
	}

	public function getOptions()
	{
		$result = $this->getModulesArray();
		$unsets = isset($this->element['unset']) ? explode(',', $this->element['unset']) : [];

		foreach ($unsets as $unset)
		{
			$unset = (string) $unset;
			
            if (!empty($unset) && isset($result[$unset]))
			{
				unset($result[$unset]);
			}
		}

		return $result;
	}
}