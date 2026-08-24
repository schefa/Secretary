<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

 
defined('_JEXEC') or die;


class SecretaryModelDatabase extends \Joomla\CMS\MVC\Model\ItemModel
{
	/**
	 * Joomla 6 declares ItemModelInterface::getItem() abstract. This model only
	 * exposes import/export maintenance tasks, so there is no item to return.
	 *
	 * @param   mixed  $pk  Ignored.
	 * @return  null
	 */
	public function getItem($pk = null)
	{
		return null;
	}

	protected $app;

	/**
	 * Class constructor 
	 * 
	 * @param array $config
	 * @throws Exception 
	 */
	public function __construct()
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		$this->app = \Secretary\Joomla::getApplication();
		parent::__construct();
	}

	/**
	 * Removes all entries for Secretary in assets
	 * @return mixed
	 */
	public function clearAssetsTable()
	{
		$db		= \Secretary\Database::getDBO();
		$query  = $db->getQuery(true);
		$query->delete($db->qn('#__assets'));
		$query->where($db->qn('name') . ' LIKE ' . $db->quote('com_secretary.%'));
		$db->setQuery($query);
		$return = $db->execute();
		
        return $return;
	}

	/**
	 * Method to set missing parent id
	 *
	 * @param int $id
	 * @return boolean|string
	 */
	public function assetsFix($id)
	{

		$db		= \Secretary\Database::getDBO();
		$item	= \Secretary\Database::getJDataResult('assets', $id, '*', 'loadObject');

		$parts	= explode('.', $item->name);
		
        if (count($parts ?? []) == 1)
		{
			$parentID = 1; // root
		}
        else
		{
			if ($parts[1] == 'task')
			{
				$searchParent = 'com_secretary.time';
            }
            else
			{
				array_pop($parts);
				$searchParent = implode('.', $parts);
            }

			// get parent
			$db->setQuery('SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__assets') . ' WHERE ' . $db->qn('name') . ' = ' . $db->quote($searchParent));
			$parentID	= $db->loadResult();
		}

		if (isset($parentID))
		{
			// update
			$update = $db->getQuery(true);
			$update->update('#__assets');
			$update->set('parent_id = ' . (int) $parentID);
			$update->where('id = ' . (int) $id);
			
            try
			{
				$db->setQuery($update);
				$db->execute();
			}
            catch (Exception $exception)
			{
                return $exception->getMessage();
			}
			
            return true;
		}
        else
		{
			// No Parent Found
			return \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_ASSETS_FIX_NO_PARENT_FOUND_FOR', $id);
		}
	}

	public function assetsErrorMissingParent()
	{
		$db = \Secretary\Database::getDBO();

		// No Parent
		$sql = $db->getQuery(true);
		$sql->select("id,name");
		$sql->from('#__assets');
		$sql->where('parent_id = ' . $db->quote(0) . ' AND id != ' . $db->quote(1));
		$db->setQuery($sql);
		$noParents	= $db->loadObjectList();

		// No Parent
		$areas = array('com_secretary.business', 'com_secretary.folder', 'com_secretary.document', 'com_secretary.item', 'com_secretary.location', 'com_secretary.market', 'com_secretary.product', 'com_secretary.reports', 'com_secretary.subject', 'com_secretary.time', 'com_secretary.template');

		$sql = " SELECT id,name FROM #__assets WHERE ( name in ('" . implode("','", $areas) . "') ) AND parent_id != (SELECT id FROM #__assets WHERE name = " . $db->quote('com_secretary') . ')';
		$db->setQuery($sql);
		$wrongParents	= $db->loadObjectList();

		$status = count($noParents ?? []) + count($wrongParents ?? []);
		
        return  array('status' => $status, 'no_parent' => $noParents + $wrongParents);
	}

	public function export($format = false)
	{

		\Joomla\CMS\Session\Session::checkToken() or jexit('Invalid Token');

		$app		= \Secretary\Joomla::getApplication();
		$user		= \Secretary\Joomla::getUser();
		$data			= $app->input->post->get('jform', array(), 'array');
		$exportTables	= $data['exportTables'];

		// Check permission
		$authorised = $user->authorise('core.admin', 'com_secretary');
		
        if ($authorised !== true)
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if (!empty($exportTables) && isset($format))
		{
			$prefix = \Secretary\Database::getDBO()->getPrefix();
			$tables = array();

			foreach ($exportTables as $table)
			{
				$tables[] = $prefix . 'secretary_' . $table;
			}

			$filename = date('Y-m-d') . '_' . date('H-i-s') . '__SECRETARY';
			
            switch ($format)
			{
				default:
					return false;
				case 'sql':
					return $this->exportSQL($tables, $filename);
				case 'csv':
					return $this->exportCsv($tables, $filename);
				case 'xml':
					return $this->exportXML($tables, $filename);
				case 'json':
					return $this->exportJSON($tables, $filename);
				case 'excel':
					return $this->exportExcel($tables, $filename);
			}

			return true;
		}
        else
		{
			$this->setError(\Joomla\CMS\Language\Text::_('COM_SECRETARY_DATABASE_NO_TABLES'));
		}

		return false;
	}

	/**
	 * Export tables to JSON file
	 * 
	 * @param array $tables
	 * @param string $filename
	 */
	private function exportJSON($tables, $filename)
	{
		header('Content-type: text/json');
		header('Content-Disposition: attachment; filename="' . $filename . '.json"');

		$db = \Secretary\Database::getDBO();
		$fp = fopen('php://output', 'w');
		$response = array();

		foreach ($tables as $table)
		{
			$sql = $db->getQuery(true)->select("*")->from($db->qn($table));
			$db->setQuery($sql);
			$items	= $db->loadObjectList();
			$response[$table] = $items;
		}

		fwrite($fp, json_encode($response));
		fclose($fp);
		jexit();
	}

	/**
	 * Export tables to CSV file
	 *
	 * @param array $tables
	 * @param string $filename
	 */
	private function exportCsv($tables, $filename)
	{
		// ToDo : multiple Sheets

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=" . $filename . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$db = \Secretary\Database::getDBO();
		$csv = fopen('php://output', 'w');

		foreach ($tables as $table)
		{
			$cols	= array_keys($db->getTableColumns($table));
			$list = $db->getQuery(true)->select("*")->from($db->qn($table));

			$db->setQuery($list);
			$items	= $db->loadObjectList();

			fputcsv($csv, array($table));
			fputcsv($csv, $cols);
			
            foreach ($items as $line)
			{
				fputcsv($csv, (array) $line);
			}
		}

		fclose($csv);

		jexit();
	}

	/**
	 * Export tables to XML file
	 *
	 * @param array $tables
	 * @param string $filename
	 */
	private function exportXML($tables, $filename)
	{
		// ToDo : multiple Sheets

		$title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS');
		$time = date("D, d M Y H:i:s e");

		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '.xml"');
		@date_default_timezone_set("GMT");

		$db	 = \Secretary\Database::getDBO();
		$xml = new XMLWriter;

		$xml->openURI('php://output');
		$xml->startDocument('1.0');
		$xml->setIndent(4);

		foreach ($tables as $table)
		{
			$xml->startElement($table);

			$query	= $db->getQuery(true);
			$query->select("*")->from($db->qn($table));
			$db->setQuery($query);
			$items = $db->loadObjectList();

			//----------------------------------------------------
			$xml->writeElement('title', $table);
			$xml->writeElement('link', \Joomla\CMS\Uri\Uri::current());
			$xml->writeElement('created', $time);
			$xml->writeElement('user', \Secretary\Joomla::getUser()->username);

			foreach ($items as $item)
			{
				$tmp = explode("_", $table);
				$lastWord = array_pop($tmp);

				$xml->startElement($lastWord);
				
                foreach ($item as $key => $value)
				{
					$xml->writeElement($key, $value);
				}
				$xml->endElement();
			}

			$xml->endElement();
		}

		$xml->endDocument();

		$xml->flush();
		jexit();
	}

	/**
	 * Export tables to EXCEL file
	 *
	 * @param array $tables
	 * @param string $filename
	 */
	private function exportExcel($tables, $filename)
	{
		// ToDo : multiple Sheets

		// filename for download
		$filename = $filename . ".xls";

		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$db = \Secretary\Database::getDBO();

		foreach ($tables as $table)
		{
			$query	= $db->getQuery(true);
			$query->select("*")->from($db->qn($table));
			$db->setQuery($query);
			$items = $db->loadObjectList();

			echo "\r\n\t" . $table . "\t\r\n";

			$flag = false;
			
            foreach ($items as $row)
			{
				$row = (array) $row;
				
                if (!$flag)
				{
					echo implode("\t", array_keys($row)) . "\r\n";
					$flag = true;
                }
				echo implode("\t", array_values($row)) . "\r\n";
			}
		}

		jexit();
	}

	/**
	 * Export tables to SQL file
	 * Credit : https://github.com/tazotodua/useful-php-scripts
	 * 
	 * @param array $tables
	 * @param string $filename
	 */
	private function exportSQL($tables, $backup_name)
	{

		// Datenbank Name
		$config			= \Joomla\CMS\Factory::getConfig();
		$databaseName	= $config->get('db');

		$db = \Secretary\Database::getDBO();
		$sql = $db->getQuery(true);

		try
		{
			foreach ($tables as $table)
			{
				// Alle Einträge in der Tabelle
				$db->setQuery('SELECT * FROM ' . $db->qn($table));
				$rows	= $db->loadObjectList();

				if (empty($rows))
				{
					continue;
				}

				// Spalten und Zeilen
				$fieldsColumnsArray = \Joomla\Utilities\ArrayHelper::fromObject($rows[0]);
				$cols_num	= count($fieldsColumnsArray ?? []);
				$rows_num	= count($rows ?? []);

				// throw new Exception( $cols_num .'-'. $rows_num , 404); return false;

				// Kopfbereich der Tabelle
				$db->setQuery('SHOW CREATE TABLE ' . $table);
				$TableMLine	= $db->loadRow();

				// Ausgabe beginnend mit der Tabelleninfo
				$content = (!isset($content) ?  '' : $content) . "\n\n" . $TableMLine[1] . ";\n\n";

				foreach ($rows as $rowNr => $row)
				{
					$row = \Joomla\Utilities\ArrayHelper::fromObject($row);
					$row = array_values($row);

					//when started (and every after 100 command cycle):
					if ($rowNr % 100 == 0 || $rowNr == 0)
					{
						$content .= "\nINSERT INTO " . $db->qn($table) . " VALUES";
					}
					$content .= "\n(";
					
                    for ($j = 0; $j < $cols_num; $j++)
					{
						// Zahlen (auch Float Werte?)
						if (is_numeric($row[$j]))
						{
							$row[$j] = $db->escape($row[$j]);
                        }
                        else
						{
							$row[$j] = "'" . str_replace("'", "\'", $row[$j]) . "'";
                        }
						$row[$j] = str_replace("\n", "\\n", $row[$j]);
						
                        if (isset($row[$j]))
						{
                            $content .= $row[$j];
                        }
                        else
						{
                                    $content .= "''";
                        }
						
                        if ($j < ($cols_num - 1))
						{
                                    $content .= ',';
                        }
					}
					$content .= ")";
					//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
					if ((($rowNr + 1) % 100 == 0 && $rowNr != 0) || $rowNr + 1 == $rows_num)
					{
						$content .= ";";
					}
                    else
					{
						$content .= ",";
					}
				}

				$content .= "\n\n\n";
			}
		}
        catch (Exception $ex)
		{
			throw new Exception($ex->getMessage());
		}

		if (empty($content))
		{
			return false;
		}

		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . $backup_name . ".sql\"");
		echo $content;
		exit;
	}

	public function import()
	{

		\Joomla\CMS\Session\Session::checkToken() or jexit('Invalid Token');

		$app		= \Secretary\Joomla::getApplication();

		$data			= $app->input->post->get('jform', array(), 'array');
		$multiple_files	= $data['import'];
		$single_file	= $app->input->files->get('jform');
		$single_file_name = $single_file['import']['name'];

		// Datei ausgewählt
		if (empty($single_file_name) && empty($multiple_files))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('COM_SECRETARY_NO_FILE_SELECTED'), 403);
		}

		// Adminrechte prüfen
		if (!(\Secretary\Helpers\Access::checkAdmin()))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if (!empty($single_file_name))
		{
			return $this->_importUpload($single_file);
		}
		elseif (!empty($multiple_files))
		{
			foreach ($multiple_files as $fileName)
			{
				// Dateiendung
				if (!\Secretary\Helpers\Uploads::checkExtension($fileName, 'sql'))
				{
					throw new \RuntimeException(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DOCUMENT_INVALID_EXTENSION', 'sql'), 403);
				}

				$filePath	= SECRETARY_ADMIN_PATH . '/application/install/samples/' . $fileName;

				if (!$this->updateSQL($filePath))
				{
					$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Not imported: %s', $fileName), 'warning');
					
                    return false;
				}

				$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DATABASE_EXECUTED_AND_UPDATED', $fileName), 'message');
			}
		}

		$this->cleanCache();
		
        return true;
	}

	private function _importUpload($single_file)
	{
		$app		= \Secretary\Joomla::getApplication();
		$fileName	= $single_file['import']['name'];
		$fileSize	= $single_file['import']['size'];

		// Upload size
		$allowedSize = \Secretary\Utilities\Number::getBytes(ini_get('upload_max_filesize'));
		
        if ($fileSize > $allowedSize)
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DOCUMENT_INVALID_SIZE', \Secretary\Utilities\Number::human_filesize($allowedSize)), 403);
		}

		// Dateiendung
		if (!\Secretary\Helpers\Uploads::checkExtension($fileName, 'sql'))
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DOCUMENT_INVALID_EXTENSION', 'sql'), 403);
		}

		// Allow an exception to be thrown.
		try
		{
			$fileTempPath	= $single_file['import']['tmp_name'];
			$return 		= $this->updateSQL($fileTempPath);
			// Store the data.
			if ($return === false)
			{
				return false;
			}

			$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_DATABASE_EXECUTED_AND_UPDATED', $fileName), 'message');
		}
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			
            return false;
		}
	}

	private function updateSQL($file)
	{
		$db = \Secretary\Database::getDBO();
		$buffer = file_get_contents($file);

		// Graceful exit and rollback if read not successful
		if ($buffer === false)
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER') . 12 . $file, 403);
		}

		// Create an array of queries from the sql file
		//$queries = JDatabaseDriver::splitSql($buffer); // Joomla 3.x+
		$queries = \Joomla\Database\DatabaseDriver::splitSql($buffer);
		$tables	= Secretary\Database::getTables(true);

		$update_count = 0;
		
        if (count($queries ?? []) != 0)
		{
			// Process each query in the $queries array (split out of sql file).
			foreach ($queries as $query)
			{
				$query = trim($query);

				// Make sure only secretary tables are affected
				/*$ok = false;
				foreach($tables as $table) {
					if(strpos($query,$table) === true) $ok = true;	
				}*/

				if ($query != '' && $query[0] != '#')
				{
					try
					{
						$db->setQuery($query);
						$db->execute();
					}
                    catch (Exception $ex)
					{
						\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $ex->getMessage()), \Joomla\CMS\Log\Log::WARNING, 'jerror');
						
                        return 0;
					}

					$update_count++;
				}
            }
		}

		return $update_count;
	}
}
