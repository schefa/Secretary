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
use JStringPunycode;
use JText;
use JUri;
use JUser;

// No direct access
defined('_JEXEC') or die;

class Email 
{
	
	public static function emailMessage( $data )
	{
	    
		$user = JFactory::getUser(); 
		$business	= Application::company();
		
		$result = array('link' => 'index.php?option=com_secretary&view=messages&layout=talk&id='.$data['id'].'&catid='.$data['catid'], 
						'msg' => JText::_('COM_SECRETARY_EMAIL_SENT_FAILED'), 'msgClass' => 'warning',
						'result' => false); 
		
		if(!(\Secretary\Helpers\Access::checkAdmin())) return $result;
		
		if(empty($data['subject'])) {
			$result['msg'] = JText::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_SUBJECT');
			return $result;
		}
		
		// $attachment = SECRETARY_ADMIN_PATH.'/uploads/'.$business['id'].'/emails/document-'.$data['createdEntry'].'.pdf';
		$attachment = "";
		$name		= $data['contact_to'];
		$email		= $data['contact_to_alias'];
		
		if(empty($name) || empty($email)) {
			$result['msg'] .= ': '. JText::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_RECIPIENT');
			return $result;
		}
		
		if(!empty($data['id']))
		{
			$sent = self::email($name, $email, $data['subject'], $data['message'], $attachment);
				
			if($sent == 1)
			{
				$db = JFactory::getDbo();
				 
				$data['fields']['emailed'] = intval( time() );
				$fields = json_encode($data['fields'], true);
				
				$query = $db->getQuery(true);
				$query->update($db->quoteName("#__secretary_messages"));
				$query->set($db->quoteName("fields")."=". $db->quote($fields));
				$query->where($db->quoteName("id")."=". $db->escape($data['id']));
				$db->setQuery($query);
				$db->query();
				
				$result['msg'] = JText::sprintf('COM_SECRETARY_EMAIL_SENT_TO', $email);
				$result['msgClass'] = 'success';
				$result['result'] = true;
			}
		}
		
		return $result;
	}
	
	/**
	 * Send a document via email
	 * 
	 * @param array $data
	 * @return string JSON response
	 */
	public static function emailDocument( $data )
	{
		$user       = JFactory::getUser(); 
		$business	= \Secretary\Application::company();
		
		$result = array('link' => 'index.php?option=com_secretary&task=document.edit&id='.$data['id'].'&catid='.$data['catid'], 
						'msg' => JText::_('COM_SECRETARY_EMAIL_SENT_FAILED'), 'msgClass' => 'warning',
						'result' => false); 
		
		// No permission
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			$result['msg'] = JText::_('COM_SECRETARY_PERMISSION_FAILED');
			return $result;
		}
		
		// No contact
		if(empty($data['fields']['message']['subject'])) {
			$result['msg'] = JText::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_SUBJECT');
			return $result;
		}
		
		$category_title = self::getCategoryTitle($data['catid']);
		$attachment = SECRETARY_ADMIN_PATH.'/uploads/'.$business['id'].'/emails/'.$category_title.'-'.$data['createdEntry'].'.pdf';
		
		$name		= $data['subject'][1];
		$email		= $data['subject'][6];
		
		if(empty($name) || empty($email)) {
			$result['msg'] .= ': '. JText::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_RECIPIENT');
			return $result;
		}
		
		if(!empty($data['id']))
		{
			$sent = self::email($name, $email, $data['fields']['message']['subject'], $data['fields']['message']['text'], $attachment);
				
			if(is_bool($sent))
			{
				$db = JFactory::getDbo();
				 
				$data['fields']['message']['emailed'] = intval( time() );
				$fields = json_encode($data['fields'], true);
				
				$query = $db->getQuery(true);
				$query->update($db->quoteName("#__secretary_documents"));
				$query->set($db->quoteName("fields")."=". $db->quote($fields));
				$query->where($db->quoteName("id")."=". $db->escape($data['id']));
				$db->setQuery($query);
				$db->execute();
				
				if($sent) {
				    $result['msg'] = JText::sprintf('COM_SECRETARY_EMAIL_SENT_TO', $email);
				} else {
				    $result['msg'] = JText::_('COM_SECRETARY_EMAIL_SENT_FAILED');
				}
				$result['msgClass'] = 'success';
				$result['result'] = $sent;
			} else {
			    $result['msg'] = JText::_('COM_SECRETARY_EMAIL_SENT_FAILED') .': '.$sent->get('message');
			    $result['msgClass'] = 'error';
			    $result['result'] = false;
			}
		}
		
		return $result;
	}

	public static function sendEmail($data, $contact_to, $contact_to_email, $subject, $emailText, $attachment = '')
	{
		
		$app = JFactory::getApplication();

		if ($contact->email == '' && $contact->user_id != 0)
		{
			$contact_user   = JUser::getInstance($contact->user_id);
			$contact->email = $contact_user->get('email');
		}
		
		$mailfrom = $app->get('mailfrom');
		$fromname = $app->get('fromname');
		$sitename = $app->get('sitename');

		$name    = $contact_to;
		$email   = JStringPunycode::emailToPunycode($contact_to_email); 

		// Prepare email body
		$prefix = JText::sprintf('COM_SECRETARY_ENQUIRY_TEXT', JUri::base());
		$body   = $prefix . "\n" . $name . ' <' . $email . '>' . "\r\n\r\n" . stripslashes($emailText);

		$mail = JFactory::getMailer();
		$mail->addRecipient($email);
		$mail->addReplyTo($email, $name);
		$mail->setSender(array($mailfrom, $fromname));
		$mail->setSubject($sitename . ': ' . $subject);
		$mail->setBody($body);
		$sent = $mail->Send();

		// Check whether email copy function activated
		if ($copy_email_activated == true && !empty($data['contact_email_copy']))
		{
			$copysubject = JText::sprintf('COM_SECRETARY_COPYSUBJECT_OF', $subject);
			$copytext    = JText::sprintf('COM_SECRETARY_COPYTEXT_OF', $name, $sitename) . "\r\n\r\n" . $body;

			$mail = JFactory::getMailer();
			$mail->addRecipient($email);
			$mail->addReplyTo(array($email, $name));
			$mail->setSender(array($mailfrom, $fromname));
			$mail->setSubject($copysubject);
			$mail->setBody($copytext);
			$sent = $mail->Send();
		}

		return $sent;
	}

	/**
	 * Method to send email via Joomla standard
	 * 
	 * @param string $contact_to
	 * @param string $contact_to_email
	 * @param string $headline
	 * @param string $emailText
	 * @param string $attachment
	 * @throws Exception
	 * @return boolean|boolean|JException
	 */
	public static function email($contact_to, $contact_to_email, $headline, $emailText, $attachment = '')
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		
		if(!isset($contact_to) || !isset($contact_to_email) || !isset($headline) || !isset($emailText))
			return false;
		
		$mailfrom	= $app->getCfg('mailfrom');
		$fromname	= $app->getCfg('fromname');
		$sitename	= $app->getCfg('sitename');
		$body		= "\r\n".stripslashes($emailText);
		
		$mail = JFactory::getMailer();
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		if(!empty($attachment)) $mail->addAttachment($attachment);
		$mail->addRecipient($contact_to_email,$contact_to);
		$mail->setSender(array($mailfrom, $fromname));
		$mail->setSubject($headline);
		$mail->setBody($body);
		
		try {
		    $sent = $mail->Send();
		} catch(Exception $e) {
		    throw new Exception($e->getMessage());
		    return false;
		}
		
		return $sent;
		
	}

	/**
	 * Method to get the category title which is the prefix of the email document attachment
	 * 
	 * @param int $catid
	 * @return string title
	 */
	public static function getCategoryTitle($catid)
	{
	    $categoryP	= \Secretary\Database::getQuery('folders',(int) $catid,'id','alias');
	    $alias = (!empty($categoryP->alias)) ? JText::_($categoryP->alias) : JText::_('COM_SECRETARY_DOCUMENT');
	    return strtolower($alias);
	}
	
}
