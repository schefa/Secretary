<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary;

require_once SECRETARY_ADMIN_PATH .'/application/ControllerAdmin.php';

// No direct access
defined('_JEXEC') or die;

class Controller
{
    public static function checkin($table, $ids = array()) {
        // only secretary table
        if(!in_array($table, Database::$secretary_tables)) {
            throw new Exception ('Query failure: '. $table);
            return false;
        }
        
        $return = false;
        if(!empty($ids)) {
            $db = \Secretary\Database::getDBO();
            $query = $db->getQuery(true);
            $query->update($db->qn('#__secretary_'.$table))
            ->set('checked_out=0')
            ->set('checked_out_time='.$db->quote('0000-00-00 00:00:00'))
            ->where('id in ('.implode(',',$ids).')');
            $db->setQuery($query);
            $return = $db->execute();
        }
        return $return;
    }
    
}

