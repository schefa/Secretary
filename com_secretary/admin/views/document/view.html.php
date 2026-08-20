<?php

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewDocument extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $business;
	protected $state;
	protected $item;
	protected $form;
	protected $params;
	protected $productUsage;
	protected $genderoptions;
	protected $entityoptions;
	protected $productUsageOption;
	protected $itemtemplates;
	protected $emailTemplate;
	protected $checkedOut;
	protected $info;
	protected $fields;

	protected $multiple_subjects = false;
	protected $jsonSubjects = null;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$app			= \Secretary\Joomla::getApplication();
		$layout			= $app->input->getCmd('layout');
		$this->view		= $app->input->getCmd('view');
		$this->extension = 'documents';

		// Multiple contacts
		$this->subjects	= $app->input->getVar('subject');
		
        if (!empty($this->subjects) && $this->jsonSubjects = json_decode($this->subjects))
		{
			$this->multiple_subjects = true;
		}

		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		$this->business	= \Secretary\Application::company();

		// Permission
		$this->user     = \Secretary\Joomla::getUser();
		$show = false;
		
        if ($layout == "edit" && true === \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by))
		{
			$show = true;
		}
        elseif ($layout != "edit")
		{
			$subjectUserId = Secretary\Database::getQuery('subjects', $this->item->subjectid, 'id', 'created_by', 'loadResult');
			
            if (false !== \Secretary\Helpers\Access::show($this->view, $this->item->id, $this->item->created_by))
			{
				$show = true;
			}
			
            if (false !== \Secretary\Helpers\Access::show($this->view, $this->item->id, $subjectUserId))
			{
				$show = true;
			}
		}

		if (!$show)
		{
			echo '<div class="alert alert-danger">' . \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR') . '</div>';
			
            return false;
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
			$app->enqueueMessage(implode("\n", $errors), 'error');
			
            return false;
		}

		//Get Field options 
		$this->genderoptions		= \Joomla\CMS\Form\FormHelper::loadFieldType('gender', false)->getList((int) (!empty($this->item->subject) ? $this->item->subject[0] : 0), 'jform[subject][0]');
		$this->entityoptions		= \Joomla\CMS\Form\FormHelper::loadFieldType('entities', false)->getList();
		$this->productUsageOption	= \Joomla\CMS\Form\FormHelper::loadFieldType('productUsage', false)->getList($this->item->productUsage);
		$this->itemtemplates		= \Joomla\CMS\Form\FormHelper::loadFieldType('templates', false)->getList($this->item->template, 'jform[template]', '', array("documents"));

		$this->relatedContacts      = $this->getConnections();

		if (isset($this->item->message['template']))
		{
			$this->emailtemplates	=	\Joomla\CMS\Form\FormHelper::loadFieldType('templates', false)->getList($this->item->message['template'], 'jform[fields][message][template]', '', array("documents"));
			
            if ($this->item->message['template'] > 0)
			{
				$this->emailTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
			}
		}

		$this->defaultTemplate = \Secretary\Helpers\Templates::getTemplate($this->item->template);
		$this->item->datafields	= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);

		$this->getJS();

		if ($this->_layout == 'edit')
		{
			$this->getEditviewFiles();
		}

		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tab');
		parent::display($tpl);
	}

	/**
	 * Method to get related contacts of the default contact
	 * Usage for members of institutions
	 * 
	 * @return string
	 */
	protected function getConnections()
	{
		$html = array();
		
        if ($this->item->subjectid > 0 && isset($this->item->subject[7]))
		{
			$subjectConnect = \Secretary\Helpers\Connections::getConnectionsSubjectData($this->item->subjectid);
			
            foreach ($subjectConnect as $key => $value)
			{
				$fullname = (!empty($value->note)) ? $value->fullname . " (" . $value->note . ")" : $value->fullname;
				$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $value->id, $fullname);
            }
		}

		$display = (!empty($html)) ? "block" : "none";
		$standard = isset($this->item->subject[7]) ? $this->item->subject[7] : 0;
		$result = '<div class="secretary-control-group-name connection-dropdown ui-widget" style="display:' . $display . ';">';
		$result .= '<select id="jform_subject_connection" name="jform[subject][7]" class="form-control fullwidth">';
		$result .= \Joomla\CMS\HTML\HTMLHelper::_('select.options', $html, 'value', 'text', $standard);
		$result .= '</select></div>';
		
        return $result;
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{

		$this->document->setTitle('Secretary - ' . $this->item->document_title);

		$isNew		= ($this->item->id == 0);
		
        if (isset($this->item->checked_out))
		{
			$this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));
		}
        else
		{
			$this->checkedOut = false;
		}

		// If not checked out, can save the document.
		if (!$this->checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('document.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('document.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}

		if (!$this->checkedOut && ($this->canDo->get('core.create')))
		{
			echo Secretary\Navigation::ToolbarItem('document.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false, '');
		}

		if (!$isNew && $this->canDo->get('core.create'))
		{
			echo Secretary\Navigation::ToolbarItem('document.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false, '');
		}

		echo Secretary\Navigation::ToolbarItem('document.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false, '');
	}

	/**
	 * Edit view needs additional files
	 */
	protected function getEditviewFiles()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(SECRETARY_MEDIA_PATH . '/js/secretary.taxcalc.js?v=' . SECRETARY_VERSION);
		$document->addScript(SECRETARY_MEDIA_PATH . '/js/secretary.document.js?v=' . SECRETARY_VERSION);
		$document->addScript(SECRETARY_MEDIA_PATH . '/assets/jquery/jquery.nestable.js');
	}

	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();

		if (!empty($this->item->items))
        {
			$javaScript	= " var e_items = " . $this->item->items . "; ";
			$items	= json_decode($this->item->items, true);
			$this->countParameters	= count($items ?? []);
		}
        else
        {
			$javaScript	= " var e_items = [] ; ";
			$this->countParameters	= 0;
		}

		$javaScript .= ' var taxRatePerc = "' . $this->item->tax . '";';
		$javaScript .= ' var currency = "' . $this->item->currencySymbol . '";';

		$fields	= (isset($this->item->datafields['fields'])) ? $this->item->datafields['fields'] : '';
		$javaScript = 'Secretary.printFields( [' . $fields . '] );' . $javaScript;

		$document->addScriptDeclaration($javaScript);
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton', 'document'));
	}
}
