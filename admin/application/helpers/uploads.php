<?php

/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary\Helpers;

use JFile;
use JTable;
use JText;

// No direct access
defined('_JEXEC') or die;

class Uploads
{
	protected static $_file = array();
	private static $_uploadedFile = array();

	public static function whatFileType($title)
	{
		switch ($title) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
				return 'image';
				break;
			case 'pdf':
				return 'pdf';
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * Method to display a file
	 * 
	 * @param object $item
	 * @param string $class
	 * @param string $size
	 * @param boolean $return
	 * @return boolean|string
	 */
	public static function getUploadFile($item, $class = NULL, $size = NULL, $return = FALSE)
	{
		if (empty($item))
			return false;

		$file = SECRETARY_ADMIN_PATH . '/uploads/' . $item->business . '/' . $item->folder . '/' . $item->title;
		$parts = explode('.', strtolower($item->title));
		$extension = self::whatFileType(end($parts));

		if (!file_exists($file)) {
			return false;
		}

		if ($extension == 'image') {
			// Assume image type is jpeg
			$mime_type = "image/jpeg";
			if (function_exists("mime_content_type")) {
				$mime_type = mime_content_type($file);
			}

			// Read image path, convert to base64 encoding
			$imageData = base64_encode(file_get_contents($file));
			$src = 'data: ' . $mime_type . ';base64,' . $imageData;

			$class = (!empty($class)) ? 'class="' . $class . '"' : '';
			$width = (!empty($size)) ? 'width="' . $size . '"' : '';
			$output = '<img ' . $width . ' ' . $class . ' src="' . $src . '">';

			if ($return) {
				return $output;
			} else {
				echo $output;
			}
		} else {

			if (empty($item->extension))
				return false;

			$class = (!empty($class)) ? $class : '';
			$words = explode(" ", $item->title);

			$output = array();
			$output[] = '<div class="btn-group ' . $class . '">';
			$output[] = '<span class="btn">' . implode(" ", array_splice($words, 0, 10)) . '</span>';

			if ($extension == 'pdf') {
				$output[] = '<a class="btn btn-upload modal" rel="{size: {x: 900, y: 500}, handler:\'iframe\'}" href="index.php?option=com_secretary&task=item.openFile&id=' . $item->id . '"><i class="fa fa-eye"></i></a>';
			}

			// ACL
			$user = \Secretary\Joomla::getUser();
			$section = \Secretary\Application::getSingularSection($item->extension);
			$canDownload = $user->authorise('core.show', 'com_secretary.' . $section . '.' . $item->itemID);
			if (!$canDownload) {
				$canDownload = $user->authorise('core.show', 'com_secretary.' . $section);
			}

			if ($canDownload) {
				$output[] = '<a class="btn btn-upload" href="index.php?option=com_secretary&task=item.openFileDownload&id=' . $item->id . '"><i class="fa fa-download"></i></a></div>';
			}

			echo implode('', $output);
		}
	}

	public static function upload($folder, $extension, $upload_title, $newID = FALSE, $files = array())
	{

		$input = \Secretary\Joomla::getApplication()->input;
		$user = \Secretary\Joomla::getUser();

		if (!$user->authorise('core.upload', 'com_secretary')) {
			throw new \Exception('JERROR_ALERTNOAUTHOR', 500);
			return false;
		}

		$checkBox = $input->get('deleteDocument');
		if (isset($checkBox)) {
			$uploadObj = JTable::getInstance("Uploads", "SecretaryTable");
			$uploadObj->delete($upload_title);
		} else {

			if (empty($files)) {
				$files = $input->files->get('jform');
			}

			$key = "" . $files['upload']['size'] . $files['upload']['name'] . "";
			if (!isset(self::$_uploadedFile[$key])) {
				self::$_uploadedFile[$key] = $files['upload'];
				self::$_uploadedFile[$key]['uploaded'] = false;
				if (is_uploaded_file(self::$_uploadedFile[$key]['tmp_name']))
					self::$_uploadedFile[$key]['is_uploaded_file'] = true;
				else
					self::$_uploadedFile[$key]['is_uploaded_file'] = false;
			}

			if (!empty(self::$_uploadedFile[$key]['name']) && true === self::$_uploadedFile[$key]['is_uploaded_file']) {
				if (!empty($upload_title)) {
					$uploadObj = JTable::getInstance("Uploads", "SecretaryTable");
					$uploadObj->delete($upload_title);
				}
				self::$_uploadedFile[$key]['uploaded'] = self::uploadDocument(self::$_uploadedFile[$key], $newID, $folder, $extension);
			}
		}
	}

	private static function uploadDocument($files, $itemID = FALSE, $folder = 'document', $extension = '')
	{
		// Beleg Upload
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$app = \Secretary\Joomla::getApplication();
		$business = \Secretary\Application::company();

		// Check Parameter
		$params = \Secretary\Application::parameters();
		$allowedExt = $params->get('documentExt');
		$allowedSize = $params->get('documentSize', 1000000);

		if (empty($files['name']) || !($files['is_uploaded_file'])) {
			return false;
		}

		if ($files['size'] > $allowedSize) {
			$app->enqueueMessage(JText::sprintf('COM_SECRETARY_DOCUMENT_INVALID_SIZE', \Secretary\Utilities\Number::human_filesize($allowedSize)), 'error');
			return false;
		}

		//check the file extension is ok
		if (!self::checkExtension($files['name'], $allowedExt)) {
			$app->enqueueMessage(JText::sprintf('COM_SECRETARY_DOCUMENT_INVALID_EXTENSION', $allowedExt), 'error');
			return false;
		}

		// Endung OK
		// $files['name'] = date('Y-m-d_H-i-s',$itemID).'_'. $files['name'];
		$fileNamePathName = (!empty($itemID)) ? ($itemID . '_' . $files['name']) : (date('Y-m-d') . '_' . $files['name']);
		$uploadPath = SECRETARY_ADMIN_PATH . '/uploads/' . $business['id'] . '/' . $folder . '/' . $fileNamePathName;

		// Existiert in der UploadRoutine
		if (isset($files['uploaded']) && strlen($files['uploaded']) > 0 && JFile::copy($files['uploaded'], $uploadPath)) {
			self::_updateTables($extension, $itemID, $folder, $fileNamePathName, $business['id']);
			return $uploadPath;
		} elseif (JFile::upload($files['tmp_name'], $uploadPath)) {
			$app->enqueueMessage(JText::sprintf('COM_SECRETARY_UPLOAD_SUCCESSFUL', $files['name']), 'notice');

			if (!empty($itemID)) {
				self::_updateTables($extension, $itemID, $folder, $fileNamePathName, $business['id']);
				return $uploadPath;
			}
		}
	}

	/**
	 * Updates tables after upload
	 * 
	 * @param string $section
	 * @param int $itemID
	 * @param string $folder
	 * @param string $fileNamePathName
	 * @param int $businessID
	 */
	private static function _updateTables($section, $itemID, $folder, $fileNamePathName, $businessID)
	{
		// Allow only secretary tables
		if (!in_array($section, \Secretary\Database::$secretary_tables)) {
			throw new \Exception('Table not allowed: ' . $section);
			return false;
		}

		// Update Upload Table
		$db = \Secretary\Database::getDBO();
		$col = array('itemID', 'title', 'extension', 'folder', 'business', 'created');
		$val = array(intval($itemID), $db->quote($fileNamePathName), $db->quote($section), $db->quote($folder), $businessID, $db->quote(date('Y-m-d H:i:s')));
		$uploadId = \Secretary\Database::insert('uploads', $col, $val);

		// Update Upload Id for Section
		$fields = array($db->qn('upload') . ' = ' . ((int) $uploadId));
		$conditions = array($db->qn('id') . ' = ' . ((int) $itemID));

		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_' . $db->escape($section)));
		$query->set($fields);
		$query->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}

	/**
	 * Method to check if file is allowed
	 * 
	 * @param string $fileName
	 * @param array|string $allowedExt
	 * @return boolean
	 */
	public static function checkExtension($fileName, $allowedExt)
	{

		$uploadedFileNameParts = explode('.', $fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);

		$validFileExts = explode(',', $allowedExt);
		$extOk = false;

		foreach ($validFileExts as $key => $value) {
			if ($value == $uploadedFileExtension) {
				$extOk = true;
			}
		}
		return $extOk;
	}

	/**
	 * Test if file is not connected to an item. If not, return that file id
	 * 
	 * @param int $fileID
	 * @return NULL|integer
	 */
	public static function checkEmptyFileId($fileID)
	{
		if (!is_int($fileID))
			return NULL;

		if (!empty($fileID) && empty(self::$_file[$fileID])) {
			// Check if ok
			$file = \Secretary\Database::getQuery('uploads', $fileID, 'id', 'id,itemID');
			if (isset($file->id) && ($file->itemID == 0)) {
				self::$_file[$fileID] = $file->id;
			}
		}
		return self::$_file[$fileID];
	}

	/**
	 * Method to connect a file id to an section item
	 * 
	 * @param int $itemId
	 * @param int $fileId
	 * @param string $section
	 */
	public static function connectFileWithSheet($itemId, $fileId, $section)
	{
		$db = \Secretary\Database::getDBO();

		// Update Uploads
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_uploads'))
			->set($db->quoteName('itemID') . ' = ' . ((int) $itemId))
			->set($db->quoteName('extension') . ' = ' . $db->quote($section))
			->where($db->quoteName('id') . ' = ' . ((int) $fileId));
		$db->setQuery($query);
		$db->execute();

		// Update Document
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_' . $db->escape($section)))
			->set($db->quoteName('upload') . '=' . ((int) $fileId))
			->where($db->quoteName('id') . '=' . ((int) $itemId));
		$db->setQuery($query);
		$db->execute();
	}
}