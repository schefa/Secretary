<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die; 

class Session
{
    
    /**
     * Customizable columns from listview (mainly for contacts, products)
     * 
     * @param string $fieldName
     * @param array $allColumns
     * @return array
     */
    public static function getColumns($fieldName,array $allColumns) {
        
        $app     = Joomla::getApplication();
        $params  = Application::parameters();
        $cols    = $params->get($fieldName);
        
        $selectedCols	= $app->getUserState('filter.'.$fieldName) ;
        $selectedCols   = (empty($selectedCols)) ? $cols : $selectedCols;
        $result         = array();
        
        if(!empty($selectedCols)) {
            foreach($allColumns as $name => $value) {
                $result[$name] = (in_array($name,$selectedCols)) ? true : false;
            }
        } else {
            $result = $allColumns;
        }
        
        return $result;
    }
} 
