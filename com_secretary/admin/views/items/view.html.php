<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');

class SecretaryViewItems extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $title;
	protected $extension;
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;

	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$app 				= Secretary\Joomla::getApplication();
		$user				= Secretary\Joomla::getUser();
		$this->extension	= $app->input->getCmd('extension', 'status');
		$this->canDo		= \Secretary\Helpers\Access::getActions();

		if (!$user->authorise('core.admin', 'com_secretary'))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return;
		}

		$this->state		= $this->get('State');

		if ($this->extension == 'plugins')
		{
			$this->items		= $this->get('Plugins');
		}
        else
		{
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');

			$this->section	= $this->state->get('filter.section');
			$this->sections	= $this->getModules();
		}

		if ($this->extension == 'status')
		{
			$this->module = $this->state->get('filter.category_id', 'system');
		}

		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
		}

		if ($this->extension == 'uploads')
		{
			$this->extraItems = $this->get('EmailFiles');
			$this->extraTotal = $this->get('EmailFilesTotal');
			$this->extraLimit = max(1, (int) $app->input->getInt('filelimit', 25));
			$this->extraStart = max(0, (int) $app->input->getInt('filestart', 0));
		}

		if ($this->extension == 'settings')
		{
			$app->redirect(Secretary\Route::create('dashboard'));
		}

		$this->title =  \Joomla\CMS\Language\Text::_('COM_SECRETARY_' . strtoupper($this->extension));
		$this->getJS();

		parent::display($tpl);
	}


	private function getModules()
	{
		$modules = \Joomla\CMS\Form\FormHelper::loadFieldType('SecretarySections', false)->getOptions();
		
        return $modules;
	}

	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{

		$this->document->setTitle('Secretary - ' . $this->title);

		//Check if the form exists before showing the add/edit buttons
		$formPath = SECRETARY_ADMIN_PATH . '/views/item';
		
        if (file_exists($formPath) && ($this->extension != 'plugins'))
		{
			$module =  (!empty($this->module)) ? '&module=' . $this->module : '';
			
            if ($this->canDo->get('core.create'))
			{
				echo '<a href="index.php?option=com_secretary&task=item.add&extension=' . $this->extension . $module . '" class="btn btn-newentry">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_NEW') . '</a>';
            }

            if ($this->canDo->get('core.edit') && isset($this->items[0]) && $this->canDo->get('core.delete'))
			{
                echo Secretary\Navigation::ToolbarItem('items.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
            }
		}

		if ($this->extension == 'plugins')
		{
			echo '<a class="btn " href="' . \Joomla\CMS\Router\Route::_("index.php?option=com_plugins&filter_folder=secretary") . '">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_PLUGINS') . '</a>';
		}
	}

	protected function getSortFieldsEntities()
	{
		return array(
			'a.id' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'),
			'a.title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTITY_TITEL'),
			'a.description' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_ENTITY_LANG'),
		);
	}

	protected function getSortFieldsUploads()
	{
		return array(
			'a.id' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'),
			'a.title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'),
		);
	}

	protected function getSortFieldsStatus()
	{
		return array(
			'a.id' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'),
			'a.title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'),
			'a.ordering' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_ORDERING'),
			'a.closeTask' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLOSETASK'),
		);
	}

	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();

		$document->addScriptDeclaration('
			Joomla.orderTable = function() {
				table = document.getElementById("sortTable");
				direction = document.getElementById("directionTable");
				order = table.options[table.selectedIndex].value;
				if (order != "' . $this->state->get('list.ordering') . '") {
					dirn = "asc";
				} else {
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, "");
			}
		');
	}

	protected function checkPlugin($name)
	{
		$db		= \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);

		$query->select('a.extension_id,a.enabled')
			->from($db->quoteName('#__extensions', 'a'))
			->where($db->quoteName('folder') . '=' . $db->quote("secretary"))
			->where($db->quoteName('name') . '=' . $db->quote($name));

		$db->setQuery($query);
		$result = $db->loadObject();
		
        return $result;
	}
}
