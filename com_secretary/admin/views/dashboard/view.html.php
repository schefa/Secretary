<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewDashboard extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $pagination;
	protected $state;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{

		$this->pagination	= $this->get('Pagination');
		$this->activities	= $this->get('Items');
		$this->state		= $this->get('State');
		$this->canDo		= \Secretary\Helpers\Access::getActions();

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
