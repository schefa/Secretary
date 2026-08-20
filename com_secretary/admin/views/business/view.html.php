<?php

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewBusiness extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$jinput			= \Secretary\Joomla::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$this->layout	= $jinput->getCmd('layout');

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		// Data fields for the fields tab (see tmpl/edit.php); without this
		// printFields() is handed an empty list and saved fields never show.
		$this->datafields = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);

		// Permission
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id);
		
        if ($this->layout == 'edit' && !$check)
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}
        elseif ($this->layout != 'edit' && false === \Secretary\Helpers\Access::show($section, $this->item->id))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
			
            return false;
		}

		if (isset($this->item->checked_out))
		{
			$this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == \Secretary\Joomla::getUser()->get('id'));
		}
        else
		{
			$this->checkedOut = false;
		}

		$this->getJS();
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tab');
		parent::display($tpl);
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html	= array();

		if (!$this->checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create'))))
		{
			$html[] = Secretary\Navigation::ToolbarItem('business.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			$html[] = Secretary\Navigation::ToolbarItem('business.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}

		if (empty($this->item->id))
		{
			$html[] = Secretary\Navigation::ToolbarItem('business.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		}
        else
		{
			$html[] = Secretary\Navigation::ToolbarItem('business.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

		echo implode("\n", $html);
	}

	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton', 'business'));
	}
}
