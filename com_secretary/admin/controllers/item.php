<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryControllerItem extends \Joomla\CMS\MVC\Controller\FormController
{

	protected $app;
	protected $baseurl = null;

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->app 			= \Secretary\Joomla::getApplication();
		$this->extension	= $this->app->input->getCmd('extension', 'status');
		$this->module		= $this->app->input->getCmd('module', 'system');
		$this->baseurl		= 'index.php?option=com_secretary&view=items&extension=' . $this->extension;
		$this->view_list = 'items';
		parent::__construct($config);
	}

	public function getModel($name = 'Item', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
		return Secretary\Model::create($name, $prefix, $config);
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&extension=' . $this->extension;
		
        if ($this->extension == 'status')
		{
			$append .= '&module=' . $this->module;
		}
		
        return $append;
	}

	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;
		
        return $append;
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		parent::save();
		$task	= $this->getTask();
		
        if ($task == 'save')
		{
			if ($this->extension == 'settings')
			{
				$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=dashboard', false));
            }
            else
			{
				$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=' . $this->view_list . '&extension=' . $this->extension, false));
            }
		}
	}

	public function openFileDownload()
	{
		$user	= \Secretary\Joomla::getUser();
		$id		= $this->app->input->getInt('id');
		$canDownload = false;

		if ($id > 0)
		{
			$db = \Secretary\Database::getDBO();
			$item = Secretary\Database::getQuery('uploads', intval($id), 'id', $db->qn(array('extension', 'itemID', 'business', 'title', 'folder')));
			$section = Secretary\Application::getSingularSection($item->extension);

			// ACL
			$canDownload = $user->authorise('core.show', 'com_secretary.' . $section . '.' . $item->itemID);
			
            if (!$canDownload)
			{
				$canDownload = $user->authorise('core.show', 'com_secretary.' . $section);
			}

			$file = (!empty($item->title)) ? \Secretary\Helpers\Uploads::safeUploadPath($item->business . '/' . $item->folder . '/' . $item->title) : false;

			if ($file !== false && $canDownload)
			{
				$filename = $item->title;

				// Clean
				while (@ob_end_clean());
				@clearstatcache();

				// Header
				header("MIME-Version: 1.0");
				header("Content-Type: " . mime_content_type($file));
				header("Content-Transfer-Encoding: binary");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Accept-Ranges: bytes");

				// Cache abstellen
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Expires: 0");
				header('Pragma: no-cache');

				// Leert den Ausgabepuffer 
				flush();

				// Ausgabeversuch
				@readfile($file);

				// Ende
				exit(0);
			}
            else
			{
				$this->setMessage(\Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSION_FAILED'), 'error');
				$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=items&extension=uploads', false));
			}
		}
	}

	public function openFile()
	{
		$id		= $this->app->input->getInt('id');
		
        if ($id > 0)
		{
			$item = Secretary\Database::getQuery('uploads', intval($id), 'id', 'business,title,folder');
			$file = \Secretary\Helpers\Uploads::safeUploadPath($item->business . '/' . $item->folder . '/' . $item->title);
			$ext = explode('.', $item->title);
			$fileType = \Secretary\Helpers\Uploads::whatFileType(end($ext));

			if ($file === false)
			{
				return;
            }

			if ($fileType == 'pdf')
			{
				$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&id=' . $id, false));
            }
            elseif ($fileType == 'image')
			{
				// Read image path, convert to base64 encoding
				$imageData = base64_encode(file_get_contents($file));
				$src = 'data: ' . mime_content_type($file) . ';base64,' . $imageData;

				echo '<img src="' . $src . '">';

				$this->app->close();
            }
            else
			{
                header("Content-Type: " . end($ext));
                header("Content-Disposition: attachment; filename=\"$file\"");
                readfile($file);
            }
		}
	}
}
