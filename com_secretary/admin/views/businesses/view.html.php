<?php

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewBusinesses extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $canDo;
	protected $items;
	protected $pagination;
	protected $state;
	protected $states;
	protected $view;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$app		        = \Secretary\Joomla::getApplication();
		$this->view         = $app->input->getCmd('view');

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->states		= \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions('root');

		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		
        if (!$this->canDo->get('core.show'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html = array();

		if ($this->canDo->get('core.create'))
		{
			$addEventText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS');
			$html[] = Secretary\Navigation::ToolbarItem('business.add', \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $addEventText), false, 'newentry');
		}

		if (!empty($this->items[0]) && \Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
		{
			$html[] = Secretary\Navigation::ToolbarItem('businesses.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash');
			$html[] = Secretary\Navigation::ToolbarItem('businesses.setDefault', 'COM_SECRETARY_TOOLBAR_SET_HOME', true, 'default', 'fa-star');
		}

		echo implode("\n", $html);
	}
}
