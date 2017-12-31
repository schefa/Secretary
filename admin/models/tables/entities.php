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

class SecretaryTableEntities extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db) {
        parent::__construct('#__secretary_entities', 'id', $db);
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
			\Secretary\Helpers\Activity::set('entities', 'deleted', 0, $pk);
        }
        return $result;
    }
}
