<?php
 
defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewFolders extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $canDo;
	protected $extension;
	protected $states;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app	= Secretary\Joomla::getApplication();
        $user	= Secretary\Joomla::getUser();
		
		if (empty($app->input->getCmd('extension')))
		{
		    $app->redirect(Secretary\Route::create('folders', array('extension'=>'documents')));
		}
		
		$this->view			= $app->input->getCmd('view');
		$this->extension	= $app->input->getCmd('extension');
		
		// Access
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		
        if (!$this->canDo->get('core.show') || !$user->authorise('core.show','com_secretary.'.substr($this->extension,0,-1)))
		{
		    echo '<div class="alert alert-danger">'. \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		}
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->states		= $this->getStates();

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new \RuntimeException(implode("\n", $errors), 404);
		}
		
		$this->items	= \Secretary\Helpers\Folders::reorderFolderItems( $this->items );
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
	    $user		= \Secretary\Joomla::getUser();
		$html		= array();
		 
		$title = (isset($this->extension)) ? \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_'.strtoupper($this->extension)) : \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES');
		$addEntryText = \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $title);
		
		
		// Prepare the toolbar
		if ($this->canDo->get('core.create') && $user->authorise('core.create','com_secretary.'.substr($this->extension,0,-1)))
		{
			$html[] = Secretary\Navigation::ToolbarItem('folder.add', $addEntryText, false, 'newentry', 'fa-plus');
		}
		
        // Stapel
		if (isset($this->items[0]) && $this->canDo->get('core.edit'))
		{
			$html[] = '<button data-joomla-dialog=\'{"popupType": "inline", "src": "#secretary-batch-dialog"}\' type="button" class="btn btn-small">
						<span class="fa fa-database" title=\"'.\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'\"></span>'.
			\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'</button>';
		}
		
		if ($this->canDo->get('core.delete'))
		{
			$html[] = Secretary\Navigation::ToolbarItem('folders.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
		
		if ($this->canDo->get('core.admin'))
		{
			$html[] = Secretary\Navigation::ToolbarItem('folders.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
		}
		
		echo implode("\n", $html);
	}
	
	private function getStates()
	{
		$states = \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		
        return $states;
	}
	
	protected function getSortFields()
	{
		return array(
			'a.state' => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.title' => \Joomla\CMS\Language\Text::_('JGLOBAL_TITLE')
		);
	}
}
