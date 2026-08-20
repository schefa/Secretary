<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;


class SecretaryModelLanguage extends \Joomla\CMS\MVC\Model\AdminModel
{
	protected $app;

	public function __construct($config = array())
	{
		// Only admin
		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
		{
			die;
		}

		$this->app = \Secretary\Joomla::getApplication();
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->app->getUserStateFromRequest('com_secretary.filter_search', 'filter_search', '', 'string');
		$this->setState('filter_search', $search);

		$filter_language = $this->app->getUserStateFromRequest('com_secretary.filter_language', 'filter_language', '', 'string');
		$this->setState('filter_language', $filter_language);

		$params = Secretary\Application::parameters();
		$this->setState('params', $params);
	}

	public function getTranslation($lang = 'en-GB', $item = 'com_secretary', $original = false)
	{
		$registry  = new Registry;
		$languages = array();

		$originalPath	= JPATH_ADMINISTRATOR . '/language/' . $lang . '/' . $lang . '.' . $item . '.ini';
		$overrideFile	= JPATH_ADMINISTRATOR . '/language/overrides/' . $lang . '.override.ini';

		if (File::exists($originalPath))
		{
			$registry->loadFile($originalPath, 'INI');
			$languages['original'] = $registry->toArray();
		}
        else
		{
			$registry->loadFile(JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.' . $item . '.ini', 'INI');
			$languages['original'] = $registry->toArray();
		}

		if (File::exists($overrideFile))
		{
			$registry->loadFile($overrideFile, 'INI');
			$languages[$lang] = $registry->toArray();
		}
        elseif (File::exists($originalPath))
		{
			$registry->loadFile($originalPath, 'INI');
			$languages[$lang] = $registry->toArray();
		}
        else
		{
			$languages[$lang] = array();
		}

		return $languages;
	}

	public function getSiteLanguages()
	{

		$languagefolders = Folder::folders(JPATH_ADMINISTRATOR . '/language');
		$return    = array();

		foreach ($languagefolders as $folder)
		{
			if (!in_array($folder, array('pdf_fonts', 'overrides')))
			{
				$return[] = $folder;
			}
		}

		return $return;
	}

	public function save($data)
	{
		$lang = \Joomla\CMS\Factory::getLanguage();

		$filterLanguage = $this->app->input->getVar('filter_language');
		$overrideFilePath = JPATH_ADMINISTRATOR . '/language/overrides/' . $filterLanguage . '.override.ini';

		$content = $this->makeFile($data);
		File::write($overrideFilePath, $content);
	}

	public function makeFile($data, $downable = false)
	{

		$values  = $data['values'];
		$content = "";

		foreach ($values as $key => $value)
		{
			if ($downable && (strpos($key, 'COM_SECRETARY') === false))
			{
				continue;
			}
			$content .= "$key=\"$value\"\n";
		}

		return $content;
	}

	public function getForm($data = array(), $loadData = true)
	{
		return false;
	}
}
