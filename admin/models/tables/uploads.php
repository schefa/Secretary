<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

class SecretaryTableUploads extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
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
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$app = \Secretary\Joomla::getApplication();
		
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		$upload = Secretary\Database::getQuery('uploads', $pk );
		
		$path = SECRETARY_ADMIN_PATH .'/uploads/'. $upload->business . '/' . $upload->folder . '/' . $upload->title;
		if( JFile::delete($path) ) {
		    if($upload->itemID > 0) $this->_updateItemDocument((int) $upload->itemID, $upload->extension,(int) $pk);
		    $app->enqueueMessage(JText::sprintf('COM_SECRETARY_UPLOAD_DELETED',$upload->title), 'notice');
		} else {
		    $app->enqueueMessage(JText::sprintf('COM_SECRETARY_UPLOAD_DELETED_NOT',$upload->title), 'error');
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
	private function _updateItemDocument($itemID,$extension,$uploadID)
	{
		$db			= \Secretary\Database::getDBO();
		$query		= $db->getQuery(true);
		$fields		= array($db->qn('upload') . " = ''");
		$conditions	= array($db->qn('id') . ' = '. $db->escape($itemID), $db->qn('upload') . ' = '. $db->escape($uploadID));
		$query->update($db->qn('#__secretary_'. $extension))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}

}
