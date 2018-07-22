<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary\Helpers;

use JText;
use stdClass;

// No direct access
defined('_JEXEC') or die; 

class Messages
{
    
    /**
     * Creates a new message item out of a document
     * 
     * @param array $data
     * @return string[] response
     */
	public static function createFromDocument( &$data )
	{
		$result = array( 'msg' => JText::_('COM_SECRETARY_MESSAGE_SAVED_FAILED'), 'msgClass' => 'warning' );
			
		$business = \Secretary\Application::company();
		$db = \Secretary\Database::getDBO();
		
		if(empty($data['fields']['message']['subject']) || empty($data['fields']['message']['text'])) {
			$result['msg'] = JText::_('COM_SECRETARY_MESSAGE_SAVED_FAILED');
			return $result;
		}
		
		$message = new stdClass();
		$message->business			= (int) $business['id'];
		$message->created_by_alias	= \Secretary\Utilities::cleaner($data['subject'][1]) .' '. \Secretary\Utilities::cleaner($data['subject'][2]);
		$message->created_by		= (int) \Secretary\Joomla::getUser()->id;
		$message->created 			= date('Y-m-d h:i:s');
		$message->subject			= \Secretary\Utilities::cleaner($data['fields']['message']['subject']);
		$message->message			= \Secretary\Utilities::cleaner($data['fields']['message']['text']);
		$message->template			= isset( $data['fields']['message']['template']) ? ((int) $data['fields']['message']['template']) : 0;
		
		$message_id = (isset($data['fields']['message']['id'])) ? $data['fields']['message']['id'] : FALSE;
		if(isset($message_id))
		    $check = \Secretary\Database::getQuery('messages', intval($message_id));
	    if($message_id < 1 || empty($check)) {
			$val		= $db->insertObject('#__secretary_messages', $message);
			$data['fields']['message']['id'] = $db->insertid();
		} else {
			$message->id = intval($data['fields']['message']['id']);
			$val = $db->updateObject('#__secretary_messages', $message, 'id');
		}
		
		if($data['fields']['message']['id'] > 0) {
			$fields = json_encode($data['fields'], true);
			
			$uQuery = $db->getQuery(true)
					->update($db->quoteName("#__secretary_documents"))
					->set($db->quoteName("fields")."=". $db->quote($fields))
					->where($db->quoteName("id")."=". $db->escape($data['id']));
			$db->setQuery($uQuery);
			$val = $db->query();
		}
		
		if($val) {
			$result['msg'] = JText::sprintf('COM_SECRETARY_MESSAGE_SAVED_ID', $data['fields']['message']['id']);
			$result['msgClass'] = 'message';
		}
				
		return $result;
	}
	
	public static function getChatOnlineUsers( $ref = FALSE )
	{ 
	    $db   = \Secretary\Database::getDBO();
	    $user  = \Secretary\Joomla::getUser(); 
	    $cids  = array($user->id);
	    
	    if(!empty($ref)) {
	    	
	        $contactToStr = (\Secretary\Database::getDbType() == 'postgresql') ? 'CAST (m.contact_to AS INTEGER)': 'm.contact_to';
    	    $db->setQuery('SELECT s.created_by FROM #__secretary_subjects AS s WHERE s.id IN (
    	         SELECT '.$contactToStr.' FROM #__secretary_messages AS m WHERE m.refer_to = '.$db->quote($ref).'
    	        ) OR s.id IN (
    	         SELECT '.$contactToStr.' FROM #__secretary_messages AS m WHERE m.refer_to = '.$db->quote($ref).'
    	        ) OR s.id IN (
    	         SELECT m.created_by FROM #__secretary_messages AS m WHERE m.refer_to = '.$db->quote($ref).'
    	        )');
    	    $cids = array_merge($cids, $db->loadColumn());
	    }
	    $cids = array_unique ($cids);
	    
	    $query = $db->getQuery(true)
	           ->select('u.name')->from($db->qn('#__session','s'))
	           ->join('LEFT', $db->qn('#__users','u').' ON s.userid = u.id');

	    if(!empty($ref) && !empty($cids)) {
	       $query->where('s.userid IN ('.implode(",",$cids).')');
	    }
	    
	    $query->where('s.guest = 0');
	    $db->setQuery($query);
	     
	    try
	    {
	        $results = $db->loadObjectList();
	    }
	    catch (\RuntimeException $e)
	    {
	        throw $e;
	    }
	     
	    return $results;
	}
	
	public static function getChatMessages($ref) {

	    $business   = \Secretary\Application::company();
	    $messages_unread = \Secretary\Application::parameters()->get('messages_unread', 9);
	    
	    $db   = \Secretary\Database::getDBO();
	    $query = $db->getQuery(true);
	    $query->select("m.id,m.subject,m.message,m.created,m.created_by_alias,m.created_by,m.state,status.title AS statustitle")
	    ->from($db->qn('#__secretary_messages','m'))
	    ->leftJoin($db->qn('#__secretary_status','status').' ON status.id = m.state')
	    ->where($db->qn('m.business').'='.intval($business['id']))
	    ->where($db->qn('m.refer_to').'='.intval($ref))
	    ->order('m.created ASC');
	     
	    $db->setQuery($query);
	    
	    $messages = $db->loadObjectList();
	    foreach($messages as $message) { 
	        $query = $db->getQuery(true);
	        $query->select("CONCAT(firstname,' ',lastname)")
	               ->from($db->qn('#__secretary_subjects'))
	               ->where($db->qn('id').'='.(int) $message->created_by);
	        $db->setQuery($query);
	    	$message->created_by_user = $db->loadResult();
	        $message->statustitle = (empty($message->statustitle)) ? JText::_('COM_SECRETARY_MESSAGES_OPTION_UNREAD') : JText::_($message->statustitle);
            $message->state = ($message->state < 1) ? $messages_unread : $message->state;
            $message->message = \Secretary\Utilities::cleaner($message->message,true);
	    }
	    
	    return $messages;
	}
	
	public static function changeMessageState($data) {

	    $itemId    = $data->getInt('item');
	    $stateId   = $data->getInt('status');

	    $user		= \Secretary\Joomla::getUser();
	    $userId		= (int) \Secretary\Database::getQuery('subjects', (int) $user->id,'created_by','id','loadResult');
	    
	    $return    = false;

	    $canEditState	= \Secretary\Joomla::getUser()->authorise('core.edit.state', 'com_secretary.message');
	    if(true === $canEditState) {
	        
	        $db   = \Secretary\Database::getDBO();
	        
	        $query = $db->getQuery(true);
	        $query->update($db->qn('#__secretary_messages'));
	        $query->set($db->qn('state').'='.intval($stateId)); 
	        $query->where($db->qn('id').'='.intval($itemId));
	        $query->where($db->qn('created_by').'!='.intval($userId));
	        	
	        $db->setQuery($query);
	        $return = $db->execute();
	        
        } else {
            $return = JText::_('COM_SECRETARY_ERROR_ACCESS');
        }
          
        return $return;
	}
	
	public static function addChatMessage($data) {

	    $return     = false;
	    $business	= \Secretary\Application::company();
	    $message    = nl2br($data->getString('message'));
	    $subject    = $data->getString('subject');
	    $referto    = $data->getInt('refer_to');
	    
	    $refer = \Secretary\Database::getQuery('messages', intval($referto));
	    if(!$refer)
	    	return false;
	    
	    $catid            = $refer->catid;
	    $contactto        = $refer->contact_to;
	    $contact_to_alias = $refer->contact_to_alias;
	    $referto          = $refer->refer_to;
	    
	    $user      = \Secretary\Joomla::getUser();
	    $canCreate	= $user->authorise('core.create', 'com_secretary.message');
	    
	    if($user->id > 0) {
	       $userContact = \Secretary\Database::getQuery('subjects', intval($user->id),'created_by','id','loadResult');
	    } else {
	       $name    = $data->getVar('name');
	       $email   = $data->getVar('email');
	       $key     = $data->getVar('kat');

	       $userContact = \Secretary\Utilities::cleaner($name);
	       $createdByAlias = \Secretary\Utilities::cleaner($email);

	       if(\Secretary\Joomla::getApplication()->isSite() && (md5($refer->id.$refer->created) == $key))
	           $canCreate = true;
	    }
	    
	    if(empty($userContact) || empty($message)) {
	        return $return;
	    }
	    
	    $subject = \Secretary\Utilities::cleaner($subject);
	    $message = \Secretary\Utilities::cleaner($message);

	    if(true === $canCreate) {
	        
	        $db   = \Secretary\Database::getDBO();
    	     
    	    $query = $db->getQuery(true);
    	    $query->insert($db->qn('#__secretary_messages'));
    	    $query->set($db->qn('business').'='.$db->escape($business['id']));
    	    $query->set($db->qn('catid').'='.intval($catid));
    	    $query->set($db->qn('refer_to').'='.$db->escape(intval($referto)));
    	    $query->set($db->qn('created').'= NOW() ');
    	    $query->set($db->qn('created_by').'='.$db->quote($userContact));
    	    if(isset($createdByAlias)) $query->set($db->qn('created_by_alias').'='.$db->quote($createdByAlias));
    	    $query->set($db->qn('contact_to').'='.$db->quote($contactto));
    	    $query->set($db->qn('contact_to_alias').'='.$db->quote($contact_to_alias));
    	    $query->set($db->qn('subject').'='.$db->quote($subject));
    	    $query->set($db->qn('message').'='.$db->quote($message));
    	    
    	    $db->setQuery($query);
    	    $return = $db->execute();
    	    
	    } else {
	        $return = JText::_('COM_SECRETARY_ERROR_ACCESS'); 
	    }
	    
	    return $return;
	}
	
	public static function deleteChatMessage($id){
	    $return     = false;
	    
	    if(!isset($id)) return $return;
	    
	    $canDelete	= \Secretary\Joomla::getUser()->authorise('core.delete', 'com_secretary.message');
	    if(true === $canDelete) {
	        $db   = \Secretary\Database::getDBO();
	        $db->setQuery('DELETE FROM #__secretary_messages WHERE '.$db->qn('id').'='. intval($id));
	        $result = $db->execute(); 
	    } else {
	        $return = JText::_('COM_SECRETARY_ERROR_ACCESS'); 
	    }

	    return $result;
	} 
	  
}
