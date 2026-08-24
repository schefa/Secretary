<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */


defined('_JEXEC') or die('Restricted access');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
define('COM_SECRETARY_INSTALLER_PATH', __DIR__);
// __DIR__ is the extracted package root (with admin/, site/ siblings) only
// while installing from a zip. Once installed, script.php runs from its
// final flat location (administrator/components/com_secretary), which has
// no admin/ subfolder - fall back to that layout for uninstall/update.
define('COM_SECRETARY_INSTALLER_ADMINPATH', is_dir(__DIR__ . "/admin") ? __DIR__ . "/admin" : __DIR__);
define('COM_SECRETARY_INSTALLER_SITEPATH', is_dir(__DIR__ . "/site") ? __DIR__ . "/site" : JPATH_SITE . "/components/com_secretary");

class com_secretaryInstallerScript
{
	protected $component_name;
	protected $extension_name;

	protected $messageInstall = "";
	protected $message = "";
	// The 4.x line targets the Joomla 4+ platform (see version="4.0" in
	// secretary.xml) and is verified on Joomla 6 / PHP 8.3.
	protected $minimumPHPVersion = '8.1.0';
	protected $minimumJoomlaVersion = '4.0.0';
	protected $installedVersion = '1.0.0';
	static protected $_helper = NULL;

	function __construct()
	{
		// Prepare installation
		$fileHelper = COM_SECRETARY_INSTALLER_ADMINPATH . "/application/install/helper.php";

		// get installed version
		$xmlPath = JPATH_ADMINISTRATOR . "/components/com_secretary/secretary.xml";
		
		if (file_exists($xmlPath))
		{
			$xml = simplexml_load_file($xmlPath);
			$this->installedVersion = $xml->version;
		}

		if (file_exists($fileHelper))
		{
			require_once $fileHelper;

			if (is_null(self::$_helper) && class_exists('SecretaryInstall'))
			{
				self::$_helper = new SecretaryInstall;
			}
		}
	}

	function install($parent)
	{
		$this->messageInstall .= '<div class="nextsteps">';
		$this->messageInstall .= '<h3>Next Steps</h3><ol>';
		$this->messageInstall .= '<li>Start your first business OR install sample data <a href="index.php?option=com_secretary" target="_blank">here</a></li>';
		$this->messageInstall .= '<li>Customize and save the System Configuration <a href="index.php?option=com_secretary&view=item&id=1&layout=edit&extension=settings" target="_blank">here</a></li>';
		$this->messageInstall .= '<li>Create your folders (e.g. invoices, quotes) for the area documents <a href="index.php?option=com_secretary&view=folders&extension=documents" target="_blank">here</a></li>';
		$this->messageInstall .= '<li>Create your first documents for the folder, that means: write your invoices, quotes etc. <a href="index.php?option=com_secretary&view=folders&extension=documents" target="_blank">here</a></li>';
		$this->messageInstall .= '</ol></div>';
	}

	function uninstall($parent)
	{
	}

	function preflight($type, $parent)
	{
		$version = new \Joomla\CMS\Version;
		
		if (version_compare($version->getShortVersion(), $this->minimumJoomlaVersion, '<'))
		{
			\Joomla\CMS\Log\Log::add("<h2>Secretary requires Joomla " . $this->minimumJoomlaVersion . " or higher. Your version is too old.</h2>", \Joomla\CMS\Log\Log::WARNING, 'jerror');
			
			return false;
		}

		if (version_compare(phpversion(), $this->minimumPHPVersion, '<'))
		{
			\Joomla\CMS\Log\Log::add("<h2>Secretary requires PHP " . $this->minimumPHPVersion . " or higher. Your PHP is too old.</h2>", \Joomla\CMS\Log\Log::WARNING, 'jerror');
			
			return false;
		}

		if (!is_null(self::$_helper) && $type == 'update')
		{
			self::$_helper->deleteFolder(JPATH_SITE . '/media/secretary/assets');
			self::$_helper->deleteFolder(JPATH_SITE . '/media/secretary/css', array('custom.css'));
			self::$_helper->deleteFolder(JPATH_SITE . '/media/secretary/fontawesome');
			self::$_helper->deleteFolder(JPATH_SITE . '/media/secretary/images');
			self::$_helper->deleteFolder(JPATH_SITE . '/media/secretary/js');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/application');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/assets');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/controllers');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/helpers');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/models');
			self::$_helper->deleteFolder(JPATH_SITE . '/administrator/components/com_secretary/views');
			self::$_helper->deleteFolder(JPATH_SITE . '/components/com_secretary');
		}
	}

	function postflight($type, $parent)
	{
		// On uninstall, the media/uploads folders this checks for have already
		// been removed by the uninstall process, so there's nothing to verify.
		if ($type == 'uninstall')
		{
			return;
		}

		$version = $parent->getManifest()->version;

		$this->message .= self::$_helper->checkFolder();

		$this->message .= "<h4>Database</h4>";
		// Update Database
		switch ($type)
		{
			case 'update':

				// Update Database if installed version is lower 
				if ((version_compare($this->installedVersion, $version, '<')))
				{
					$this->message .= self::$_helper->updateDatabase($version, $this->installedVersion);
				}
                else
				{
					$this->message .= '<p>' . \Joomla\CMS\Language\Text::_('Database is up to date!') . '</p>';
				}

				// Major Changes
				if (version_compare($this->installedVersion, '2.0.4') <= 0)
				{
					self::$_helper->_update_2_0_5();
				}
				
                if (version_compare($this->installedVersion, '3.1.9') <= 0)
				{
					self::$_helper->_update_3_2_0();
				}

				break;

			case 'install':
				$this->message .= \Joomla\CMS\Language\Text::_('install.' . self::$_helper->getDbType() . '.sql executed<br>');
				// install.*.sql is kept fully current with schema, so no updates/*.sql
				// files - which only exist to migrate pre-existing installations - need
				// replaying on a fresh install.
				break;

			default:
				break;
		}

		self::$_helper->message($version, $this->message, $this->messageInstall);
	}
}