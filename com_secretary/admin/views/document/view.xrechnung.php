<?php

defined('_JEXEC') or die;

class SecretaryViewDocument extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $item;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = 'xrechnung')
	{
		$this->item	= $this->get('Item');

		// Permission
		if (!\Secretary\Helpers\Access::documentExportAllowed($this->item, $this->_layout))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors), 404);
			
            return false;
		}

		if (!\Secretary\Helpers\ERechnung::isAvailable($this->item))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('COM_SECRETARY_ERECHNUNG_UNAVAILABLE'), 404);
			
            return false;
		}

		$xml = \Secretary\Helpers\ERechnung::generate($this->item);

		$filename = 'XRechnung_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $this->item->nr) . '.xml';

		$app = \Secretary\Joomla::getApplication();
		$app->setHeader('Content-Type', 'application/xml; charset=utf-8', true);
		$app->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"', true);
		$app->sendHeaders();

		echo $xml;

		$app->close();
	}
}
