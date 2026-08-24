<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;

defined('_JEXEC') or die;

class Email
{

	/**
	 * Send a document via email
	 * 
	 * @param array $data
	 * @return string JSON response
	 */
	public static function emailDocument($data)
	{
		$user = \Secretary\Joomla::getUser();
		$business = \Secretary\Application::company();

		$result = array(
			'link' => 'index.php?option=com_secretary&task=document.edit&id=' . $data['id'] . '&catid=' . $data['catid'],
			'msg' => Text::_('COM_SECRETARY_EMAIL_SENT_FAILED'),
			'msgClass' => 'warning',
			'result' => false
		);

		// No permission
		if (!(\Secretary\Helpers\Access::checkAdmin()))
		{
			$result['msg'] = Text::_('COM_SECRETARY_PERMISSION_FAILED');
			
            return $result;
		}

		// No contact
		if (empty($data['fields']['message']['subject']))
		{
			$result['msg'] = Text::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_SUBJECT');
			
            return $result;
		}

		$category_title = self::getCategoryTitle($data['catid']);
		$attachment = SECRETARY_ADMIN_PATH . '/uploads/' . $business['id'] . '/emails/' . $category_title . '-' . $data['createdEntry'] . '.pdf';

		$name = $data['subject'][1];
		$email = $data['subject'][6];

		if (empty($name) || empty($email))
		{
			$result['msg'] .= ': ' . Text::_('COM_SECRETARY_EMAIL_SENT_FAILED_NO_RECIPIENT');
			
            return $result;
		}

		if (!empty($data['id']))
		{
			$sent = self::email($name, $email, $data['fields']['message']['subject'], $data['fields']['message']['text'], $attachment);

			if (is_bool($sent))
			{
				$db = \Secretary\Database::getDBO();

				$data['fields']['message']['emailed'] = intval(time());
				$fields = json_encode($data['fields'], true);

				$query = $db->getQuery(true);
				$query->update($db->quoteName("#__secretary_documents"));
				$query->set($db->quoteName("fields") . "=" . $db->quote($fields));
				$query->where($db->quoteName("id") . "=" . $db->escape($data['id']));
				$db->setQuery($query);
				$db->execute();

				if ($sent)
				{
					$result['msg'] = Text::sprintf('COM_SECRETARY_EMAIL_SENT_TO', $email);
				}
                else
				{
					$result['msg'] = Text::_('COM_SECRETARY_EMAIL_SENT_FAILED');
				}
				$result['msgClass'] = 'success';
				$result['result'] = $sent;
			}
            else
			{
				$result['msg'] = Text::_('COM_SECRETARY_EMAIL_SENT_FAILED') . ': ' . $sent->get('message');
				$result['msgClass'] = 'error';
				$result['result'] = false;
			}
		}

		return $result;
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
	 * @return boolean
	 */
	public static function email($contact_to, $contact_to_email, $headline, $emailText, $attachment = '')
	{
		$app = \Secretary\Joomla::getApplication();
		$user = \Secretary\Joomla::getUser();

		if (!isset($contact_to) || !isset($contact_to_email) || !isset($headline) || !isset($emailText))
		{
			return false;
		}

		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$sitename = $app->getCfg('sitename');
		$body = "\r\n" . stripslashes($emailText);

		$mail = Factory::getMailer();
		$mail->isHTML(true);
		$mail->Encoding = 'base64';
		
        if (!empty($attachment))
		{
			$mail->addAttachment($attachment);
		}
		$mail->addRecipient($contact_to_email, $contact_to);
		$mail->setSender(array($mailfrom, $fromname));
		$mail->setSubject($headline);
		$mail->setBody($body);

		try
		{
			$sent = $mail->Send();
		}
        catch (\Exception $e)
		{
			throw new Exception($e->getMessage());
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
		$categoryP = \Secretary\Database::getQuery('folders', (int) $catid, 'id', 'alias');
		$alias = (!empty($categoryP->alias)) ? Text::_($categoryP->alias) : Text::_('COM_SECRETARY_DOCUMENT');
		
        return strtolower($alias);
	}
}