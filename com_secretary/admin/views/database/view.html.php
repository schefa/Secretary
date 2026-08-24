<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewDatabase extends \Joomla\CMS\MVC\View\HtmlView
{
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$canSee	= Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary');
		
        if (!$canSee || count($this->get('Errors') ?? []))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}

		$model = $this->getModel('Database');
		$this->assetsErrors = $model->assetsErrorMissingParent();

		parent::display($tpl);
	}
}
