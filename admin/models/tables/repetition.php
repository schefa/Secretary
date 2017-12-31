<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

class SecretaryTableRepetition extends JTable
{
    
    /**
     * Class constructor
     *
     * @param mixed $db
     */
	public function __construct(&$db) {
        parent::__construct('#__secretary_repetition', 'id', $db);
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
			\Secretary\Helpers\Activity::set('repetition', 'deleted', $this->catid, $pk);
        }
        return $result;
    }
    
    /**
     * Delete repetition depending on time id
     * 
     * @param int $time_id
     */
    public function deleteRepetition($time_id)
	{
	    $row = Secretary\Database::getQuery('repetition',$time_id,'time_id');
		if(isset($row->id)) {
			$result = $this->delete($row->id);
		}
	}
	
}
