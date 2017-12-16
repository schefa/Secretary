<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

class SecretaryTableAccounting extends JTable
{

    /**
     * Class constructor
     * 
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_accounting', 'id', $db);
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
			// Save activity
			\Secretary\Helpers\Activity::set('accounting', 'deleted', 0, $pk );
        }
        return $result;
    }
}
