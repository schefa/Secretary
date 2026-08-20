<?php

defined('_JEXEC') or die;

class SecretaryTableRepetition extends \Joomla\CMS\Table\Table
{

    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_repetition', 'id', $db);
    }

    /**
     * Delete and save activity
     *
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::delete()
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        
        if ($result)
		{
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
        $row = Secretary\Database::getQuery('repetition', $time_id, 'time_id');
        
        if (isset($row->id))
		{
            $result = $this->delete($row->id);
        }
    }
}
