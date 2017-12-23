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

use JFactory;

// No direct access
defined('_JEXEC') or die;

class Database
{
    private static $query_result = array();
    private static $objectList = array();
    private static $object = array();
    private static $jdataresult = array();
    
    private static $joomla_tables = array(
        'assets',
        'users'
    );
    
    public static $secretary_tables = array(
        'accounting',
        'accounts',
        'accounts_system',
        'activities',
        'businesses',
        'currencies',
        'documents',
        'entities',
        'fields',
        'folders',
        'locations',
        'markets',
        'messages',
        'newsletter',
        'products',
        'repetition',
        'settings',
        'status',
        'subjects',
        'tasks',
        'templates',
        'times',
        'uploads'
    );
    
    /**
     * Database Interface
     */
    public static function getDBO() {
        return JFactory::getDbo();
    }
    
    /**
     * Method to get the Database 
     */
    public static function getDbType()
    {
        return self::getDBO()->name == "postgresql" ? 'postgresql' : 'mysql';
    }
    
    /**
     * Method to get the tables of Secretary
     */
    public static function getTables($fulltabletitle = false)
    {
        if($fulltabletitle) {
            $names = array();
            $prefix = self::getDbo()->getPrefix();
            foreach( self::$secretary_tables as $table) {
                $names[] = $prefix .'secretary_'. $table;
            }
            return $names;
        }
        return self::$secretary_tables;
    }
    
    /**
     * SELECT objectlist for secretary tables
     */
    public static function getObjectList($table, $select = array('*'), $where = array(), $order = null)
    {
        // Allow only secretary tables
        if(!in_array($table,self::$secretary_tables)) {
            throw new \Exception ('Table not allowed: '. $table);
            return false;
        }
        
        $db     = self::getDBO();
        $query  = $db->getQuery(true);
        
        $key  = strtolower($table .'_') . Utilities\Text::alphanumeric($select) . implode('_',$where) . $order; 
        $query->select($select);
        $query->from($db->qn('#__secretary_'.$table));
        
        if(!empty($where) && is_array($where)) {
            $query->where($where);
        }
        
        if(!empty($order)) {
            $query->order($order);
        }
         
        try {
            $db->setQuery($query);
            self::$objectList[$key] = $db->loadObjectList(); 
        
        } catch(\Exception $ex) {
            throw new \Exception($ex->getMessage());
            return false;
        }
        
        return self::$objectList[$key];
    }
    
    /**
     * SELECT query for secretary tables
     */
    public static function getQuery($table, $pk, $where_clause='id', $select = '*', $output = 'loadObject')
    {
        // Allow only secretary tables
        if(!in_array($table,self::$secretary_tables)) {
            throw new \Exception ('Table not allowed: '. $table);
            return false;
        }
        
        // Allow only valid query types
        if(!in_array($output,array('loadObject','loadResult','loadObjectList','loadAssoc','loadColumn'))) {
            throw new \Exception ('Not allowed: '. $output);
            return false;
        }
        
        $db = self::getDBO();
            
        $key = strtolower($table .'_'.$output.'_'. $where_clause .'_'. $pk);
        
        if(is_array($select)) {
            for ($i = 0;$i < count($select); $i++) $select[$i] = Utilities\Text::onlyLetters($select[$i],'*');
        } else {
            $select = $db->escape($select);
            $key .= Utilities\Text::alphanumeric($select);
        }
        
        if(empty(self::$query_result[$key])) {
            
            $where	= (is_numeric($pk)) ? intval($pk) : $db->quote( $pk ); 
            $select = (is_array($select)) ? $db->qn($select) : $db->escape($select);
            $where_clause = Utilities\Text::onlyLetters($where_clause,'_');
            
            $query	= $db->getQuery(true);
            $query->select($select);
            $query->from($db->qn('#__secretary_'.$table));
            $query->where($db->qn($where_clause).'='.$where);
             
            try {
                $db->setQuery($query);
                self::$query_result[$key] = $db->$output();
            } catch(\Exception $ex) {
                throw new \Exception($ex->getMessage()); 
                return false;
            }
            
        }
         
        return self::$query_result[$key];
    } 
    
    /**
     * SELECT query for verified tables
     */
    public static function getJDataResult($table,$pk ,$getField,$output = 'loadResult')
    {
        // Allow only secretary tables
        if(!in_array($table,self::$joomla_tables)) {
            throw new \Exception ('Table not allowed: '. $table);
            return false;
        }
        
        // Allow only valid query types
        if(!in_array($output,array('loadObject','loadResult'))) {
            throw new \Exception ('Not allowed: '. $output);
            return false;
        }
        
        $key = $table .'_'. $pk .'_'. Utilities\Text::alphanumeric($getField);
        if($pk > 0 && empty(self::$jdataresult[$key])) {
            
            $result = '';
        
            $db		= self::getDBO();
            $query	= $db->getQuery(true);
            
            $query->select($db->qn($getField));
            $query->from($db->qn('#__'.$table));
            $query->where($db->qn('id')."=". intval($pk) );
            
            try {
                $db->setQuery($query);
                self::$jdataresult[$key] = $db->$output();
            } catch(\Exception $exc) {
                throw new \Exception($exc->getMessage());
                return $result;
            }
        }
        return self::$jdataresult[$key];
    }
    
    /**
     * INSERT query
     *  
     * @return int|bool inserted id
     */
    public static function insert($table , $columns = array(), $values = array())
    {
        // Allow only secretary tables
        if(!in_array($table,self::$secretary_tables)) {
            throw new \Exception ('Table not allowed: '. $table);
            return false;
        }
        
        $db		= self::getDBO();
        $query	= $db->getQuery(true);
    
        $query->insert($db->qn('#__secretary_'.$table));
        $query->columns($db->qn($columns));
        $query->values(implode(',', $values));
            
        try {
            $db->setQuery($query);
            $db->execute();
            return $db->insertid();
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage(),500);
            return false; 
        }
    }
}

