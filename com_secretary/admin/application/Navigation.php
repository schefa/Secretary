<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

defined('_JEXEC') or die;

require_once SECRETARY_ADMIN_PATH . '/application/NavigationModel.php';

class Navigation
{

	/**
	 * Creates and renders the Sidebar
	 * 
	 * @return string sidebar
	 */
	public static function getSidebar()
	{

		$app = Joomla::getApplication();
		// Default to '' rather than null: these end up as strpos() needles and
		// in string concatenation further down, and none of them is set on e.g.
		// the component's dashboard URL.
		$view = $app->input->getCmd('view', '');
		$extension = $app->input->getCmd('extension', '');
		$layout = $app->input->getCmd('layout', '');
		$catid = $app->input->getInt('catid', 0);
		$sidebarToggle = (int) $app->getUserState('filter.toggleSidebar');
		$html = array();
		$isEditForm = false;

		switch ($view)
		{
			case 'item':
			case 'items':
				$view = $extension;
				break;
		}

		if ($layout == 'edit')
		{
			$isEditForm = true;
			$app->input->set('hidemainmenu', true);
		}

		$sidebarClass = ($sidebarToggle == 0) ? '' : ' hidden-sidebar';

		$html[] = '<div class="secretary-sidebar-container' . $sidebarClass . '">';
		$html[] = '<div class="secretary-topbar-brand"><a class="secretary-topbar-brand-title" href="https://github.com/schefa/Secretary" target="_blank">Secretary</a></div>';
		$html[] = self::createSubmenu($view, $isEditForm, $catid) . '</div>';

		return implode($html);
	}

	/**
	 * Create a toolbar Button (save, edit, delete, etc.)
	 * 
	 * @param string $taskName name of the task to execute
	 * @param string $taskTitle Title of the task to display
	 * @param bool $check execute only when items in the list are selected
	 * @param string $cssClass additional CSS class
	 * @param string $icon Font-Awesome Icon
	 * 
	 * @return string Button
	 */
	public static function ToolbarItem($taskName, $taskTitle, $check = false, $cssClass = "default", $icon = NULL)
	{
		if ($check == true)
		{
			$onclick = "if (document.adminForm.boxchecked.value==0){";
			$onclick .= "alert('" . \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAVBAR_ERROR_NO_SELECTION') . "');";
			$onclick .= "} else { Joomla.submitbutton('" . $taskName . "'); }";
		}
        else
		{
			$onclick = "Joomla.submitbutton('" . $taskName . "')";
		}

		$result = array();
		$result[] = '<button type="button" class="btn btn-' . $cssClass . '" onclick="' . $onclick . '">';
		
        if (!empty($icon))
		{
			$result[] = '<span class="fa ' . $icon . '"></span>';
		}
		$result[] = \Joomla\CMS\Language\Text::_($taskTitle);
		$result[] = '</button>';

		return implode("\n", $result);
	}

	/**
	 * Displays an alphabetical toolbar to filter by letter
	 * 
	 *  @param string $view current view
	 */
	public static function displayAlphabeticalToolbar($view)
	{
		$result = array();
		$alphabet = range('a', 'z');
		$task = Joomla::getApplication()->input->get('letter');

		$result[] = '<div class="btn-group">';
		
        foreach ($alphabet as $letter)
		{
			$link = Route::create($view, array('letter' => $letter));
			$active = ($task == $letter) ? ' active' : '';
			$result[] = '<a class="btn btn-default letter ' . $active . '" href="' . $link . '">' . ucfirst($letter) . '</a>';
		}
		$result[] = "</div>";
		echo implode($result);
	}

	/**
	 * Prepares the menu items
	 * 
	 * @param array $categories
	 * @return array
	 */
	private static function createMenuItems(&$categories)
	{
		$user = Joomla::getUser();
		$links = array();
		$sublinks = array();

		// Counter
		$contactsCounter = NULL;

		// Menu START
		// Zentrale 
		$links[] = NavigationModel\Factory::create(new NavigationModel\Headline('COM_SECRETARY_CENTRAL'));
		$links[] = NavigationModel\Factory::create(new NavigationModel\Item(1, 'dashboard', 'fa-dashboard', 'COM_SECRETARY_DASHBOARD'));

		if ($user->authorise('core.show', 'com_secretary.business'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(2, 'businesses', 'fa-home', 'COM_SECRETARY_BUSINESS'));
		}
		
        if ($user->authorise('core.show', 'com_secretary.location'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(3, 'locations', 'fa-cube', 'COM_SECRETARY_LOCATIONS'));
		}
		
        if ($user->authorise('core.show', 'com_secretary.reports'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(4, 'reports', 'fa-bar-chart', 'COM_SECRETARY_REPORTS'));
		}
		// Katogerien	
		if ($user->authorise('core.show', 'com_secretary.folder'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(4, 'folders', 'fa-columns', 'COM_SECRETARY_CATEGORIES', true));
		}

		// Headline 
		$links[] = NavigationModel\Factory::create(new NavigationModel\Headline('COM_SECRETARY_SECTIONS'));

		// Dokumente
		if ($user->authorise('core.show', 'com_secretary.document'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\ItemAccordion(20, 'COM_SECRETARY_DOCUMENTS', '', 'fa-file', 3, 'documents', 'documents&catid=0'));
			
            if (!empty($categories['documents'][0]->id))
			{
				foreach ($categories['documents'] as $konto)
				{
					if (isset($konto->id))
					{
						$sublinks['documents'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(20, 'documents&catid=' . $konto->id, $konto->title, null, $konto->id));
					}
                }
			}
		}

		// Kontakte
		if ($user->authorise('core.show', 'com_secretary.subject'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\ItemAccordion(30, 'COM_SECRETARY_SUBJECTS', '', 'fa-users', 4, 'subjects', 'subjects&catid=0', $contactsCounter));
			
            if (!empty($categories['subjects'][0]->id))
			{
				foreach ($categories['subjects'] as $konto)
				{
					if (isset($konto->id))
					{
						$sublinks['subjects'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(30, 'subjects&catid=' . $konto->id, $konto->title, null, $konto->id));
					}
                }
			}
		}

		// Produkte
		if ($user->authorise('core.show', 'com_secretary.product'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\ItemAccordion(40, 'COM_SECRETARY_PRODUCTS', '', 'fa-shopping-cart', 5, 'products', 'products&catid=0'));
			
            if (!empty($categories['products'][0]->id))
			{
				foreach ($categories['products'] as $konto)
				{
					if (isset($konto->id))
					{
						$sublinks['products'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(40, 'products&catid=' . $konto->id, $konto->title, null, $konto->id));
					}
                }
			}
		}

		// Time Management
		if ($user->authorise('core.show', 'com_secretary.time'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\ItemAccordion(80, 'COM_SECRETARY_TIMES', '', 'fa-calendar', 8, 'times', 'times&section=list'));
			$sublinks['times'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(80, 'times&extension=events', 'COM_SECRETARY_EVENTS'));
			$sublinks['times'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(80, 'times&extension=projects', 'COM_SECRETARY_PROJECTS'));
			$sublinks['times'][] = NavigationModel\Factory::create(new NavigationModel\SubItem(80, 'times&extension=locations', 'COM_SECRETARY_LOCATIONS'));
		}


		// Headline
		$links[] = NavigationModel\Factory::create(new NavigationModel\Headline('COM_SECRETARY_STANDARDS'));

		if ($user->authorise('core.show', 'com_secretary.template'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(100, 'templates', 'fa-print', 'COM_SECRETARY_TEMPLATES'));
		}

		if ($user->authorise('core.admin', 'com_secretary'))
		{
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(101, 'items&extension=fields', 'fa-th-large', 'COM_SECRETARY_FIELDS'));
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(102, 'items&extension=status', 'fa-paperclip', 'JSTATUS'));
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(103, 'items&extension=entities', 'fa-text-height', 'COM_SECRETARY_ENTITIES'));
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(104, 'items&extension=currencies', 'fa-money', 'COM_SECRETARY_CURRENCIES'));
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(105, 'language', 'fa-globe', 'COM_SECRETARY_TRANSLATIONS'));

			$links[] = NavigationModel\Factory::create(new NavigationModel\Headline('System'));

			if ($user->authorise('core.admin', 'com_secretary'))
			{
				$links[] = NavigationModel\Factory::create(new NavigationModel\Item(109, 'items&extension=uploads', 'fa-upload', 'COM_SECRETARY_FILES'));
			}
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(111, 'database', 'fa-database', 'COM_SECRETARY_DATABASE'));
			$links[] = NavigationModel\Factory::create(new NavigationModel\Item(112, 'item&id=1&layout=edit&extension=settings', 'fa-cog', 'COM_SECRETARY_SETTINGS'));
		}

		// Menu END

		return array('links' => $links, 'sublinks' => $sublinks);
	}

	private static function createSubmenu($active, $disabled = false, $itemCatid = NULL)
	{
		$app = Joomla::getApplication();
		$sidebarToggle = (int) $app->getUserState('filter.toggleSidebar');

		// Cache Instanz laden.
		$user = Joomla::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key = 'navigation' . implode(',', $levels) . '.' . $active . '.' . $disabled . '.' . $itemCatid;
		$cache = Joomla::getCache();
		$enabled = Application::parameters()->get('cache');

		if (($enabled == 0) || !($html = $cache->get($key)))
		{
			$catid = $app->input->getInt('catid');
			$view = $app->input->getString('view');
			$extension = $app->input->getString('extension');
			//$this->canDo		= parent::getActions();
			$html = array();
			$konten = array();

			// Get Business Data
			$business = Application::company();

			if ($selectedFolders = json_decode($business['selectedFolders'], true))
			{
				foreach ($selectedFolders as $section => $categories)
				{
					if (is_string($section))
					{
						for ($i = 0; $i < count($categories ?? []); $i++)
						{
							$konten[$section][] = Database::getQuery('folders', (int) $categories[$i]);
						}
					}
				}
			}

			$html[] = '<div class="sidebar-nav">';
			$html[] = '	<ul class="secretary-nav">';

			// Printing Menu
			$menuItems = self::createMenuItems($konten);
			
            foreach ($menuItems['links'] as $link)
			{
				$class = '';
				
                if (isset($link->url))
				{
					if ((strpos($link->url, $active . '&catid=' . $itemCatid) !== false) && !empty($itemCatid) && empty($extension))
					{
						// Dokumente
						$class = 'active';
                    }
                    elseif (strpos($link->url, $active . '&extension=' . $extension) !== false && empty($itemCatid))
					{
						// Fields
						$class = 'active';
                    }
                    elseif ((strpos($link->url, $active) !== false) && strpos($link->url, 'items') === false && empty($itemCatid))
					{
						$class = 'active';
                    }
                }
                elseif (!empty($link->accordionHeadline))
				{
                    $class = 'accordion-headline';
                }

                if ($link instanceof NavigationModel\Headline || $link->horzline)
				{
                    $class .= ' nav-headline';
                }

				$html[] = '<li class="' . $class . '">';

				$separator = (isset($link->separator) && isset($link->section) && !empty($menuItems['sublinks'][$link->section])) ? true : false;
				
                if ($separator)
				{
                            $html[] = '<span>';
                            (($link->separator == 3) || ($active == $link->section)) ? ($angle = 'down') : ($angle = 'left');
                            $html[] = '	<span class="fa pull-right fa-angle-' . $angle . '" data-target="#accordion-' . $link->separator . '"></span>';
                }

				// Link <a> START
                if (!isset($link->headline))
				{
                    if (isset($link->url) && $disabled == false)
                    {
                        $url = ' href="index.php?option=com_secretary&view=' . $link->url . '"';
                    }
                    else
                    {
                        $url = ' class="nolink"';
                    }

                    if (isset($link->separator) && $disabled == false)
					{
                        $html[] = '<a' . $url . ' class="accordion">';
                    }
                    else
					{
                        $html[] = '<a' . $url . '>';
                    }
                }

                if (isset($link->icon))
				{
                    $html[] = '<span class="fa ' . $link->icon . '"></span>';
                }
				
                if (!isset($link->headline))
				{
                            $toggleSidebarTextClass = ($sidebarToggle == 0) ? '' : ' hide-sidebar-text';
                            $html[] = '<span class="nav-item-text' . $toggleSidebarTextClass . '">' . \Joomla\CMS\Language\Text::_($link->title) . '</span>';
                }
                else
				{
                            $html[] = '<span class="nav-headline-text">' . \Joomla\CMS\Language\Text::_($link->title) . '</span>';
                }

                if (!isset($link->headline))
				{
                    $html[] = '</a>';
                }

                if ($separator)
				{
                    $html[] = '</span>';
                }
				// Link <a> END

				$class = ''; // Reset

				// Submenu
                if (isset($link->separator))
				{
                    $accClass = isset($link->class) ? $link->class : '';
					
                    if (($link->separator == 3) || $active == $link->section)
                    {
						$accIn = 'block';
						$accClass .= ' out';
                    }
                    else
					{
                        $accIn = 'none';
                    }

					$html[] = '<ul class="accordion ' . $accClass . '" id="accordion-' . $link->separator . '" style="display:' . $accIn . ';">';

                    foreach ($menuItems['sublinks'] as $section => $sublinks)
					{
                        foreach ($sublinks as $sublink)
                        {
                            if ($sublink->pid == $link->id)
                            {
                                $class = '';
                                // Dokumente : Kategorien
                                if (isset($sublink->catid) && (($sublink->catid == $catid && empty($itemCatid)) || $sublink->catid == $itemCatid))
                                {
                                    $class = ' class="active"';
                                    // Kategorien 
                                }
                                elseif (isset($sublink->url) && ($sublink->url == $active . '&extension=' . $extension) !== false && empty($itemCatid))
                                {
                                    $class = ' class="active"';
                                }
                                elseif (isset($sublink->url) && $sublink->url == $view && empty($extension))
                                {
                                    $class = ' class="active"';
                                }

                                ($disabled == true) ? ($url = ' class="nolink"') : ($url = ' href="index.php?option=com_secretary&view=' . $sublink->url . '"');

                                $html[] = '<li' . $class . '><a' . $url . '>';

                                if (isset($sublink->icon))
                                {
                                    $html[] = '<span class="fa ' . $sublink->icon . '"></span>';
                                }

                                $html[] = '<span class="nav-item-text">' . \Joomla\CMS\Language\Text::_($sublink->title) . '</span></a>';
                            }
                        }
                    }

					$html[] = '</ul>';
                }

				$html[] = '</li>';
			}

			$html[] = '	</ul>';

			$html[] = '</div>';

			$html = implode("\n", $html);

			if ($enabled == 1)
			{
				$cache->store($html, $key);
			}
		}

		return $html;
	}

}