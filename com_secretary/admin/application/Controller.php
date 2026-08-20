<?php

namespace Secretary;

require_once SECRETARY_ADMIN_PATH . '/application/ControllerAdmin.php';

defined('_JEXEC') or die;

class Controller
{
    public static function checkin($table, $ids = array())
    {
        // only secretary table
        if (!in_array($table, Database::$secretary_tables))
		{
            throw new Exception('Query failure: ' . $table);
            
            return false;
        }

        $return = false;
        
        if (!empty($ids))
		{
            $db = \Secretary\Database::getDBO();
            $query = $db->getQuery(true);
            $query->update($db->qn('#__secretary_' . $table))
                ->set('checked_out=0')
                ->set('checked_out_time=' . $db->quote('0000-00-00 00:00:00'))
                ->where('id in (' . implode(',', $ids) . ')');
            $db->setQuery($query);
            $return = $db->execute();
        }
        
        return $return;
    }
}