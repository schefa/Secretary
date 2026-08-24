<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SecretaryTableUploads extends \Joomla\CMS\Table\Table
{

	/**
	 * Class constructor
	 *
	 * @param mixed $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__secretary_uploads', 'id', $db);
	}

	/**
	 * Delete and save activity
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
	public function delete($pk = null)
	{
		// Delete file
		$app = \Secretary\Joomla::getApplication();

		$upload = Secretary\Database::getQuery('uploads', $pk);

		$path = SECRETARY_ADMIN_PATH . '/uploads/' . $upload->business . '/' . $upload->folder . '/' . $upload->title;
		
        if (\Joomla\Filesystem\File::delete($path))
		{
			if ($upload->itemID > 0)
			{
				$this->_updateItemDocument((int) $upload->itemID, $upload->extension, (int) $pk);
            }
			$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_UPLOAD_DELETED', $upload->title), 'notice');
		}
        else
		{
			$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_UPLOAD_DELETED_NOT', $upload->title), 'error');
		}

		parent::delete($pk);
	}

	/**
	 * Update the section item
	 * 
	 * @param int $itemID
	 * @param string $extension
	 * @param int $uploadID
	 */
	private function _updateItemDocument($itemID, $extension, $uploadID)
	{
		$db			= \Secretary\Database::getDBO();
		$query		= $db->getQuery(true);
		$fields		= array($db->qn('upload') . " = ''");
		$conditions	= array($db->qn('id') . ' = ' . $db->escape($itemID), $db->qn('upload') . ' = ' . $db->escape($uploadID));
		$query->update($db->qn('#__secretary_' . $extension))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}
}
