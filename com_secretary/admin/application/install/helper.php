<?php

// No direct access 
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Cache\Cache;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Archive\Archive;

class SecretaryInstall
{

	public function getDbType()
	{
		return Factory::getDbo()->name == "postgresql" ? 'postgresql' : 'mysql';
	}

	public function updateDatabase($newVersion, $oldversion = null, $ignoreVersions = null)
	{
		$updates = array();

		// Get All update files for Database
		$files = Folder::files(COM_SECRETARY_INSTALLER_ADMINPATH . '/application/install/sql/updates/' . $this->getDbType() . '/', '\.sql$');

		foreach ($files as $file)
		{
			$updateVersion = str_replace('.sql', '', $file);

			// Update
			if (!empty($oldversion))
			{
				if (version_compare($updateVersion, $oldversion, '>'))
				{
					$updates[] = $this->updateSQL($file);
				}
			}
            else
			{
				// Ignore old versions after installation.sql file was updated
				if (!empty($ignoreVersions) && version_compare($updateVersion, $ignoreVersions, '<='))
				{
					continue;
                }

				// Installation
				if (version_compare($newVersion, $updateVersion, '>='))
				{
					$updates[] = $this->updateSQL($file);
                }
			}
		}

		if (!empty($updates))
		{
			$message = implode('<br>', $updates);
		}
        else
		{
			$message = '<p>Database is up to date!</p>';
		}

		return $message;
	}

	/**
	 * Install database updates
	 * 
	 * @param string $file
	 * @return boolean|string
	 */
	private function updateSQL($file)
	{
		$db = Factory::getDbo();
		$buffer = file_get_contents(COM_SECRETARY_INSTALLER_ADMINPATH . '/application/install/sql/updates/' . $this->getDbType() . '/' . $file);

		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), 'warning');
			
            return false;
		}

		// Create an array of queries from the sql file
		//$queries = JDatabaseDriver::splitSql($buffer); // Joomla 3.x+
		$queries = \Joomla\Database\DatabaseDriver::splitSql($buffer);

		$update_count = 0;
		
        if (count($queries ?? []) != 0)
		{
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);
				
                if ($query != '' && $query[0] != '#')
				{
					$db->setQuery($query);
					
                    if (!$db->execute())
					{
						\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), \Joomla\CMS\Log\Log::WARNING, 'jerror');
						
                        return false;
                    }
					$update_count++;
				}
            }
		}

		return $file . ' executed';
	}

	/**
	 * Method to check if some folders exists
	 * 
	 * @return string Message
	 */
	public function checkFolder()
	{
		$uploadfolder = JPATH_ADMINISTRATOR . "/components/com_secretary/uploads";
		$message = '<h4>' . \Joomla\CMS\Language\Text::_('Files & Folders') . '</h4>';

		// Check default upload directory
		if (!Folder::exists($uploadfolder))
		{
			if (Folder::create($uploadfolder, 0755))
			{
				// copy the index.html to the new folder
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				File::write($uploadfolder . DS . "index.html", $data);
				$message .= '<p>' . \Joomla\CMS\Language\Text::sprintf("Folder <strong><em>%s</em></strong> created!", "<em>../com_secretary/<strong>uploads</strong></em>") . '</p>';
			}
		}
        else
		{
			$message .= "-";
		}

		$message .= $this->secureFolder();
		$message .= $this->customFiles();

		return $message;
	}

	/**
	 * Create or keep custom user files (like custom.css)
	 * 
	 * @return string Message
	 */
	private function customFiles()
	{
		$dest = JPATH_SITE . '/media/secretary/css/custom.css';
		
        if (is_file($dest))
		{
			return "<p>custom.css exists</p>";
		}

		if ($fh = fopen($dest, 'w'))
		{
			$stringData = "/* @package com_secretary */";
			fwrite($fh, $stringData, 1024);
			fclose($fh);
			
            return "<p>custom.css created</p>";
		}
	}

	/**
	 * Secures upload folder with .htaccess from direct access
	 * 
	 * @return string Message
	 */
	private function secureFolder()
	{
		// check folder protection situation
		$source = COM_SECRETARY_INSTALLER_ADMINPATH . '/htaccess.txt';
		$dest = JPATH_ADMINISTRATOR . '/components/com_secretary/uploads/.htaccess';

		if (!is_file($dest))
		{
			// copy and rename the htaccess
			if (File::exists($source))
			{
				File::copy($source, $dest);
				$msg = ('.htaccess created for ../uploads');
			}
            else
			{
				$msg = ('Error ! .htaccess not created for ../uploads');
			}
			
            return '<p>' . $msg . '</p>';
		}
		
        return "";
	}

	/**
	 * Message after successful installation
	 * 
	 * @param number $version
	 * @param string $message
	 * @param string $messageInstall
	 */
	public function message($version, $message, $messageInstall = "")
	{

		$this->cleanCache();

		$html[] = '<style>.readonly { padding: 10px 0 30px; font-family: Arial; font-size:13px !important; font-weight: normal !important; text-align: justify; color: #4d4d4d; line-height: 24px; } 
			.readonly h1 { clear:both; font-family:Verdana,Geneva,sans-serif; font-size:38px;letter-spacing:3px;margin:30px 6px 16px;padding:0;color:#333;text-shadow:0 1px 1px #ddd;font-weight:600; } 
			.readonly h1 .green { color:#690; }
			.readonly p { margin: 0 6px 10px } 
			.readonly p.license {font-size: 11px; margin: 15px 6px 30px; padding: 6px 0; }
			.install-notes{background-color:#eee;padding:6px 9px;border:1px solid #ddd;max-width:620px;}
			.install-notes h3{border-bottom:1px solid #ddd;padding:0 0 10px;}
			.install-notes h4{}
			.tooltip-content { display: none; }
			.tip-text span.readonly { display: none; }
			.tip-text span.tooltip-content { display: block; }.btn-group{margin-bottom:20px;}
			.principle{max-width:620px;font-size:17px;}
			.secretary-principle-img{float:right;max-width:120px;margin-left:20px;margin-bottom:13px;}
			.nextsteps{padding:10px 10px 30px;}
			</style>';

		$html[] = '<div class="readonly">';
		// echo '<img class="secretary_image pull-right" src="'.JURI::root().'media/secretary/images/secretary_medium_logo.png" />';
		$html[] = "<h1><span class='green'>SECRETARY</span> <small>" . $version . "</small></h1>";
		$html[] = "<p>Copyright © Fjodor Schäfer | <a href='https://github.com/schefa/Secretary' target='_blank'>Homepage</a></p>";


		if (!empty($messageInstall))
		{
			$html[] = $messageInstall;
		}

		$html[] = '<div class="install-notes">';
		$html[] = '<h3>Installation Notes</h3>';
		$html[] = $message;
		$html[] = '</div><p class="license">Secretary is released under the <a target="_blank" href="http://www.gnu.org/licenses/gpl-2.0.html">GNU/GPL v2 license.</a></p>';
		$html[] = "</div>";

		echo implode("", $html);
	}

	public function deleteFiles($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (Folder::exists($path))
		{
			foreach (Folder::files($path, '.', false, true, $ignore) as $file)
			{
				$sFile = explode("/", $file);
				
                if (File::exists($file) && !in_array($sFile[count($sFile) - 1], $ignore))
				{
					File::delete($file);
				}

				//JFactory::getApplication()->enqueueMessage(($sFile[count($sFile) - 1]));
			}
		}
	}

	/**
	 * Delete folder and subfolders
	 * 
	 * @param string $path
	 * @param array $ignore
	 */
	public function deleteFolders($path, $ignore = array())
	{
		$ignore = array_merge($ignore, array('.git', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

		if (Folder::exists($path))
		{
			foreach (Folder::folders($path, '.', false, true, $ignore) as $folder)
			{
				if (Folder::exists($folder))
				{
					Folder::delete($folder);
				}
			}
		}
	}

	/**
	 * Delete directory and all files und subdirectories
	 * 
	 * @param string $path
	 * @param array $ignore
	 */
	public function deleteFolder($path, $ignore = array())
	{
		$this->deleteFiles($path, $ignore);
		$this->deleteFolders($path, $ignore);
	}

	/**
	 * Cleans cache
	 * 
	 * @param string $group
	 * @param number $client_id
	 */
	protected function cleanCache($group = 'com_secretary', $client_id = 0)
	{
		$conf = Factory::getConfig();
		$app = Factory::getApplication();

		$options = array(
			'defaultgroup' => ($group) ? $group : (isset($this->option) ? $this->option : Factory::getApplication()->input->get('option')),
			'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'),
			'result' => true,
		);

		$cache = Cache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$app->triggerEvent('onContentCleanCache', $options);
	}



	/** ******************************************/
	/** ************ Major Changes ***************/
	/** ******************************************/

	public function _update_3_2_0()
	{
		$db = Factory::getDbo();
		$location = 'https://raw.githubusercontent.com/schefa/updateservers/master/secretary/secretary.xml';

		$query = $db->getQuery(true);

		$query = $db->getQuery(true);
		$query->select('name')
			->from($db->qn('#__update_sites'))
			->where($db->qn('name') . '=' . $db->quote('com_secretary'));
		$db->setQuery($query);
		$hasValue = $db->loadResult();

		if (!empty($hasValue))
		{
			$query = $db->getQuery(true);
			$query->update('#__update_sites');
			$query->set('location = ' . $db->quote($location));
			$query->where('name =' . $db->quote('com_secretary'));
			$db->setQuery($query);
			$db->execute();
		}
        else
		{
			$object = new stdClass;
			$object->name = 'com_secretary';
			$object->type = 'extension';
			$object->enabled = 1;
			$object->extra_query = $extra_query;
			$object->location = $location;
			$result = $db->insertObject('#__update_sites', $object);
		}
	}

	public function _update_2_0_5()
	{

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$db->setQuery('SELECT id,connections FROM #__secretary_subjects WHERE connections != ""');
		$subjects = $db->loadObjectList();

		foreach ($subjects as $subject)
		{
			if ($connections = json_decode($subject->connections))
			{
				$db->setQuery('DELETE FROM `#__secretary_connections` WHERE extension = ' . $db->quote('subjects') . ' AND ( one = ' . (int) $subject->id . ' OR two = ' . (int) $subject->id . ')');
				$db->execute();

				foreach ($connections as $idx => $feature)
				{
					foreach ($feature as $key => $value)
					{
						if ($key == 'id')
						{
							$db->setQuery('INSERT INTO `#__secretary_connections` (`extension`,`one`,`two`,`note`)
                	                   VALUES (' . $db->quote('subjects') . ',' . (int) $subject->id . ',' . (int) $value . ',' . $db->quote($feature->note) . ')');
							$db->execute();
						}
					}
				}
			}
		}

		$db->setQuery('ALTER TABLE `#__secretary_subjects` DROP `connections`');
		$db->execute();
	}
}