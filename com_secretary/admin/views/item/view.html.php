<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewItem extends \Joomla\CMS\MVC\View\HtmlView
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
		$app		     = Secretary\Joomla::getApplication();
		$this->extension = $app->input->getCmd('extension');
		$this->canDo	 = \Secretary\Helpers\Access::getActions('item');

		$user            = Secretary\Joomla::getUser();
		
        if (!$user->authorise('core.admin', 'com_secretary'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}

		if ($this->extension !== 'plugins')
		{
			$this->item		= $this->get('Item');
			$this->state	= $this->get('State');
			$this->form		= $this->get('Form');
		}

		switch ($this->extension)
		{
			case 'settings':
				$this->rulesList = array();
				$model = $this->getModel();
				$sections = Secretary\Application::$sections;
				unset($sections['system']);
				unset($sections['item']);

				// Section assets are otherwise only created when the settings form
				// is saved (see models/item.php). On a site that has never saved
				// settings, the #__assets rows are missing, so JAccess::checkGroup()
				// below gets an unresolvable asset key and PHP 8.1 throws a
				// deprecation warning for every action/group combination.
				\Secretary\Helpers\Access::restoreDefaultSectionAssets();
				$db = \Secretary\Database::getDBO();
				$db->setQuery('SELECT ' . $db->qn('rules') . ' FROM ' . $db->qn('#__secretary_settings') . ' WHERE ' . $db->qn('id') . ' = 1');
				$rules = json_decode($db->loadResult(), true);

				foreach ($sections as $singular => $plural)
				{
					$assetKey = (is_array($rules) && !empty($rules[$singular])) ? $rules[$singular] : 'com_secretary';
					$this->rulesList[$plural] = Secretary\HTML::_('configuration.getRulesField', $singular, $assetKey);
				}
				break;

			case 'uploads':
				$canUpload = $user->authorise('core.upload', 'com_secretary');
				
                if (!$canUpload)
				{
					throw new Exception(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'));
				}

				break;
		}

		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
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

		$user		= \Secretary\Joomla::getUser();
		
        if (isset($this->item->id))
		{
			$isNew		= ($this->item->id == 0);
		}
		
        if (isset($this->item->checked_out))
		{
			$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
        else
		{
			$checkedOut = false;
		}

		// If not checked out, can save the item.
		if (!$checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('item.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('item.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		
        if (!$checkedOut && $this->extension != 'settings' && ($this->canDo->get('core.create')))
		{
			echo Secretary\Navigation::ToolbarItem('item.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}

		if ($this->extension == 'settings')
		{
			echo '<a href="' . Secretary\Route::create('dashboard') . '" class="btn btn-default">' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_CLOSE') . '</a>';
		}
        else
		{
			echo Secretary\Navigation::ToolbarItem('item.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}
	}

	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();

		$this->title =  \Joomla\CMS\Language\Text::_('COM_SECRETARY_' . strtoupper($this->extension));
		$document->setTitle('Secretary - ' . $this->title);
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton', 'item'));
	}
}
