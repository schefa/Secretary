<?php

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
			throw new Exception(500, \Joomla\CMS\Language\Text('JERROR_ALERTNOAUTHOR'));
			
            return false;
		}

		$model = $this->getModel('Database');
		$this->assetsErrors = $model->assetsErrorMissingParent();

		parent::display($tpl);
	}
}
