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

class SecretaryTableMessage extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
	public function __construct(& $db)
	{
		parent::__construct('#__secretary_messages', 'id', $db);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::check()
	 */
	public function check()
	{
		if (empty($this->subject))
		{
		    $errTitle = JText::_('COM_SECRETARY_TITLE');
		    $this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
		    return false;
		}

		if (empty($this->message))
		{
		    $errTitle = JText::_('COM_SECRETARY_MESSAGE');
		    $this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
		    return false;
		}

		return true;
	}
	
	/**
	 * Delete and save activity
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
    public function delete($pk = null) {
        $this->load($pk);
        $result = parent::delete($pk);
        if ($result) {
			\Secretary\Helpers\Activity::set('messages', 'deleted', $this->catid, $pk);
        }
        return $result;
    }
}
