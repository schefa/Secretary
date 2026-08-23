<?php

defined('_JEXEC') or die;


class SecretaryViewDocument extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $document_title;
	protected $productUsage;
	protected $genderoptions;
	protected $entityoptions;
	protected $productUsageOption;
	protected $itemtemplates;
	protected $emailTemplate;
	protected $checkedOut;
	protected $info;
	protected $fields;

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
		$layout			= $jinput->getCmd('layout');

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);

		// Permission
		if (!\Secretary\Helpers\Access::documentExportAllowed($this->item, $layout))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors), 404);
		}

		// Get Business Data
		$this->business	= Secretary\Application::company();

		//Get Field options
		\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
		$this->genderoptions		=	\Joomla\CMS\Form\FormHelper::loadFieldType('gender', false)->getList($this->item->subject[0], 'jform[subject][0]');
		$this->entityoptions		=	\Joomla\CMS\Form\FormHelper::loadFieldType('entities', false)->getList();
		$this->productUsageOption	=	\Joomla\CMS\Form\FormHelper::loadFieldType('productUsage', false)->getList($this->item->productUsage);
		$this->itemtemplates		=	\Joomla\CMS\Form\FormHelper::loadFieldType('templates', false)->getList($this->item->template, 'jform[template]');

		$this->emailtemplates		=	\Joomla\CMS\Form\FormHelper::loadFieldType('templates', false)->getList($this->item->message['template'], 'jform[fields][message][template]');
		
        if ($this->item->message['template'] != 0)
		{
			$this->emailTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
		}

		if (!empty($this->item->template))
		{
			$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);
		}

		parent::display($tpl);
	}
}
