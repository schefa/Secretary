<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\Helpers;

use JFactory;
use stdClass;

// No direct access
defined('_JEXEC') or die;

class Connections
{
    private $one;
    private $two;
    private $extension = 'system';
    private $note;
    
    public function __construct($extension, $myId) {
        $this->extension = $extension;
        $this->one = $myId;
    }

    /**
     * Method to get a list of projects and tasks where the contact is involved
     * 
     * @param int $contact_id
     * @return StdClass
     */
    public static function getContactProjects($contact_id)
    {
        $db   = \Secretary\Database::getDBO();
        $search = '%"id":"'.(int) $contact_id .'","note%';
    
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','title','startDate','endDate')))
                ->from($db->qn('#__secretary_times'))
                ->where('contacts LIKE '.$db->quote($search) );
        
        $db->setQuery($query);
        $items = $db->loadObjectList();

        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','title','startDate','endDate')))
        ->from($db->qn('#__secretary_tasks'))
        ->where('contacts LIKE '.$db->quote($search) );
        
        $db->setQuery($query);
        $tasks = $db->loadObjectList(); 
        
        return  (object) array_merge((array) $items, (array) $tasks); 
    }

    /**
     * Method to get a connection between a Category and a Template 
     * e.g. Document has a Category and Template and vice versa
     * Document can get Category fields through Template
     * 
     * @param int $templateID
     * @return array list
     */
    public static function getFolderToTemplate($templateID) {
        $db   = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
    
        $query->select('one,two,note')
        		->from($db->qn('#__secretary_connections'))
        		->where('extension = '.$db->quote("folders").' AND ( two = '.(int) $templateID .')' );
        
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
    
    /**
     * Method to get connections from one startpoint
     */
    public static function getConnections($extension, $one, $both = true)
    {
        $db   = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
    
        $query->select('one,two,note')
        		->from($db->qn('#__secretary_connections'));
        if(true === $both) {
            $query->where('extension = '.$db->quote($extension).' AND ( one = '.(int) $one.' OR two = '.(int) $one .')' );
        } else {
            $query->where('extension = '.$db->quote($extension).' AND ( one = '.(int) $one.')' );
        }
    
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
    
    /**
     * Method to get the contacts that are associated with the contact
     */
    public static function getConnectionsSubjectData($contact_id)
    {
        $db   = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
    
        $query->select('one,two,note')
                ->from($db->qn('#__secretary_connections'))
                ->where('extension = '.$db->quote("subjects").' AND one = '.(int) $contact_id );
        
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        if(empty($items))
            return array();
        
        $query = $db->getQuery(true);
        $result = array();
        foreach($items as $item) {
        	
        	$query	= $db->getQuery(true);
        	$query->select("id,CONCAT(firstname,' ',lastname) as fullname")
        	->from($db->qn('#__secretary_subjects'))
        	->where("id =".$db->escape($item->two));
        	
        	$db->setQuery($query);
            $subject = $db->loadObject();
            if(isset($subject->fullname)) {
                $subject->note = $item->note;
                $subject->fullname = \Secretary\Utilities::cleaner($subject->fullname,true);
                $result[] = $subject;
            }
        }
        return $result;
    }

    /**
     * Deletes specific contacts for this (subject) instance
     * 
     * @param array $contacts
     * @return boolean
     */
    public function deleteConnectionsSubjects($contacts) {
        if(empty($this->extension) or empty($this->one) or empty($contacts))
            return false;
        
        $ids = array();
        foreach($contacts as $contact) {
            $ids[] = $contact->two;
        }
        $ids = implode(",", $ids);
        
        $db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
        $query->delete('#__secretary_connections');
        $query->where('extension = '.$db->quote($this->extension).' AND (( one = '.(int) $this->one.' AND two in ('. $db->escape($ids) .')) OR ( one in ('.$db->escape($ids).') AND two = '. $this->one .'))' );
        
        $db->setQuery($query);
        $db->execute();
    }
    
    /**
     * Deletes connections to this instance
     * 
     * @param boolean $bothWays search for this instance also as the second connection to another item 
     * @return boolean
     */
    public function deleteConnections($bothWays = true) {
    
        if(empty($this->extension) or empty($this->one))
            return false;
            
        $db   = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
        $query->delete('#__secretary_connections');
        
        if(true === $bothWays) {
            $query->where('extension = '.$db->quote($this->extension).' AND ( one = '.(int) $this->one.' OR two = '.(int) $this->one .')' );
        } else {
            $query->where('extension = '.$db->quote($this->extension).' AND ( one = '.(int) $this->one.')' );
        }
        
        $db->setQuery($query);
        $db->execute();
    }
    
    /**
     * Method to connect a second item to this instance
     * An item can be a subject or anything else 
     * This method is used to logically couple
     * 
     * @param unknown $two
     * @param unknown $note
     * @param boolean $both
     * @return boolean
     */
    public function addConnection($two,$note,$both = false) {

        if(empty($this->extension) or empty($this->one))
            return false;
            
        $db   = \Secretary\Database::getDBO();
        $query  = $db->getQuery(true);
        
        $query->select('one')
        	->from($db->quoteName('#__secretary_connections'))
        	->where("one = ".(int) $this->one." AND two = ".(int) $two." AND extension = ".$db->quote($this->extension));
        
        $db->setQuery($query);
        $exists = $db->loadResult();
        
        if(empty($exists)) {
        	 
        	$columns = array('extension', 'one', 'two', 'note');
        	$values = array( $db->quote($this->extension), (int) $this->one, (int) $two, $db->quote($note));
        	
        	$query->insert($db->quoteName('#__secretary_connections'))
        	       ->columns($db->quoteName($columns))
        	       ->values(implode(',', $values));
        	 
        	$db->setQuery($query);
        	$db->execute();
        	
        } else {
        	$query	= $db->getQuery(true);
        	$query->update($db->quoteName('#__secretary_connections'))
                    ->set("note = ".$db->quote($note))
                    ->where('extension = '.$db->quote($this->extension))
                    ->where('one = '.(int) $this->one)
                    ->where('two = '.(int) $two);
            $db->setQuery($query);
            $db->execute();
        }
        
        if($both) {
        	
        	$query  = $db->getQuery(true);
        	$query->select('one')
        			->from($db->quoteName('#__secretary_connections'))
        			->where("one = ".(int) $two)
        			->where("two = ".(int) $this->one)
                    ->where("extension = ".$db->quote($this->extension));
    	 
        	$db->setQuery($query);
            $exists = $db->loadResult();
            
            if(empty($exists)) { 
                $in             = new stdClass();
                $in->extension  = $this->extension ;
                $in->one        = (int) $two;
                $in->two        =(int) $this->one;
                $in->note       ="";
                $result = $db->insertObject('#__secretary_connections', $in);
            }
        }
    
    }
    
}
?>