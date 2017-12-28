<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
	
class SecretaryControllerMessage extends JControllerForm
{
    
    protected $app;
    protected $view_list = 'messages';
    protected $catid;
    protected $contact;
    protected $layout;
    protected $refer_to;
    
    public function __construct() {
        $this->app      = JFactory::getApplication();
        $this->catid	= $this->app->input->getInt('catid');
        $this->contact	= $this->app->input->getInt('contact');
        $this->layout	= $this->app->input->getCmd('layout');
        $this->refer_to	= $this->app->input->getInt('rid');
        parent::__construct();
    }
	
	public function getModel($name = 'Message', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
	    return Secretary\Model::create($name,$prefix,$config);
	}
	
	protected function allowAdd($data = array())
	{
	    $user = JFactory::getUser();
	    return $user->authorise('core.create','com_secretary.message') || count($user->getAuthorisedCategories($this->option, 'core.create'));
	}
	
    protected function allowEdit($data = array(), $key = 'id')
    {
        $return = \Secretary\Helpers\Access::allowEdit('message',$data, $key);
        return $return;
    }
    
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $append = parent::getRedirectToItemAppend($recordId);
        if(!empty($this->catid)) $append .= '&catid=' . $this->catid;
        if(!empty($this->layout)) $append .= '&layout=' . $this->layout;
        if(!empty($this->contact)) $append .= '&contact=' . $this->contact;
        if(!empty($this->refer_to)) $append .= '&rid=' . $this->refer_to;
        return $append;
    }
    
    protected function getRedirectToListAppend()
    {
        $append = parent::getRedirectToListAppend();
        if(!empty($this->catid)) $append .= '&catid=' . $this->catid;
        if(!empty($this->refer_to)) $append .= '&rid=' . $this->refer_to;
        if(!empty($this->layout) && $this->layout != 'edit') $append .= '&layout=' . $this->layout;
        return $append;
    }
    
    public function email()
    { 
        $id		= $this->app->input->getInt('id','');
        if($id > 0) {
            	
            $data	= Secretary\Database::getQuery('messages',$id, 'id', '*', 'loadAssoc');
            if(!empty($this->refer_to)) {
                $recipient	= Secretary\Database::getQuery('messages', (int) $this->refer_to, 'id', '*', 'loadAssoc');
                $data['contact_to'] = $recipient['created_by'];
                $data['contact_to_alias'] = $recipient['created_by_alias'];
            }
            	
            $sent	= Secretary\Email::emailMessage( $data );
            	
            if(!empty($this->refer_to)) $sent['link'] .= '&rid=' . $this->refer_to;
            	
            $this->setMessage($sent['msg'], $sent['msgClass']);
        } else {
        	$config = JFactory::getConfig();
        	$subject		= $this->app->input->getString('subject');
        	$emailText		= $this->app->input->getString('emailText');
            Secretary\Email::sendEmail( false,$config->get( 'fromname' ),$config->get( 'mailfrom' ), $subject, $emailText);
            $sent['link'] = 'index.php?option=com_secretary';
        }
        $this->setRedirect( $sent['link'] );
        return;
    }
    
    
    public function reply()
    {
        if ($replyId = $this->app->input->getInt('reply_id'))
        {
            $this->setRedirect('index.php?option=com_secretary&view=message&layout=edit&reply_id=' . $replyId);
        }
        else
        {
            $this->setMessage(JText::_('COM_MESSAGES_INVALID_REPLY_ID'));
            $this->setRedirect('index.php?option=com_secretary&view=messages');
        }
    }
    
    public function add()
    {
        if ($contactId = $this->app->input->getInt('contact'))
        {
            $this->setRedirect('index.php?option=com_secretary&view=message&layout=edit&contact=' . $contactID);
        }
        else
        {
            parent::add();
        }
    }
    
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Message');
        $this->setRedirect(JRoute::_('index.php?option=com_secretary&view='. $this->view_list . $this->getRedirectToListAppend(), false));
        return parent::batch($model);
    }
    
    public function changeStatus()
    { 
        $itemID = $this->app->input->getInt('id');
        $value	= $this->app->input->getInt('value');
    
        $contact_to = (int) Secretary\Database::getQuery('messages', (int) $itemID,'id','contact_to','loadResult');
        $userId		= (int) Secretary\Database::getQuery('subjects', (int) JFactory::getUser()->id,'created_by','id','loadResult');
    
        if(($contact_to === $userId) && ($itemID > 0) && ($value > 0)) {
            // Update Message Status
            $db		= \Secretary\Database::getDBO();
            $query	= $db->getQuery(true);
            $query->update($db->quoteName('#__secretary_messages'))
            ->set($db->qn('state').'='.intval($value))
            ->where($db->qn('id').'='.intval($itemID));
            $db->setQuery($query);
            $result = $db->execute();
    
            if($result) {
                echo JText::_('COM_SECRETARY_SAVE_SUCCESS');
            } else {
                echo JText::_('COM_SECRETARY_SAVE_FAILED');
            }
        }
        $this->app->close();
    }
    
    public function unsubscribe()
    {
        $me = $this->app->input->getString('me');
        $me = Secretary\Utilities::cleaner($me);
        $email = Secretary\Security::encryptor('open', $me);
    
        $subjectID = Secretary\Database::getQuery('subjects',$email,'email','id','loadResult');
        if($subjectID > 0)
            $msg = \Secretary\Helpers\Newsletter::removeNewsletterFromContact($subjectID);
            else
                $msg = JText::_('COM_SECRETARY_ILLEGAL_LINK');
    
                $this->setRedirect(JRoute::_('index.php?option=com_secretary'), $msg);
    }
    
    public function submit()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
     
        $model  = $this->getModel('message');
        $id   	= $this->app->input->getInt('id');
        $tid   	= $this->app->input->getInt('tid');
        $cid   	= $this->app->input->getInt('cid',0);
    
        // Get the data from POST
        $data		= $this->app->input->post->get('jform', array(), 'array');
        $contact_to	= $model->getItem($id);
    
        // Check for a valid session cookie
        if (JFactory::getSession()->getState() != 'active')
        {
            JError::raiseWarning(403, JText::_('COM_SECRETARY_SESSION_INVALID'));
            $this->app->setUserState('com_secretary.message.data', $data);
            $this->setRedirect(JRoute::_('index.php?option=com_secretary&view=message&id='.$id.'&tid='.$tid.'&cid='.$cid, false));
            return false;
        }
    
        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }
    
        $validate = $model->validate($form, $data);
        if ($validate === false || (is_numeric($data['contact_name'])))
        {
            // Get the validation messages.
            $errors = $model->getErrors();
    
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception) {
                    $this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $this->app->enqueueMessage($errors[$i], 'warning');
                }
            }
    
            // Save the data in the session.
            $this->app->setUserState('com_secretary.message.data', $data);
            $this->setRedirect(JRoute::_('index.php?option=com_secretary&view=message&id='.$id.'&tid='.$tid.'&cid='.$cid, false));
            return false;
        }
    
        // Send the email
        $sent = false;
        $sendType = $model->getParam()->get('sendType');
        if($sendType == 0) {
            $sent = Secretary\Email::sendEmail($data,$data['contact_name'],$data['contact_email'],$data['subject'],$data['message']);
            $sent = $model->storeMessage($tid, $cid, $contact_to, $data);
        } elseif($sendType == 1) {
            $sent = Secretary\Email::sendEmail($data,$data['contact_name'],$data['contact_email'],$data['subject'],$data['message']);
        } elseif($sendType == 2) {
            $sent = $model->storeMessage($tid, $cid, $contact_to, $data);
        }
    
        // Set the success message if it was a success
        if (!($sent instanceof Exception))
        {
            $msg = JText::_('COM_SECRETARY_EMAIL_THANKS');
        } else {
            $msg = '';
        }
    
        // Flush the data from the session
        $this->app->setUserState('com_secretary.message.data', null);
    
        // Redirect if it is set in the parameters, otherwise redirect back to where we came from
        if ($model->getParam()->get('redirect')) {
            $this->setRedirect($model->getParam()->get('redirect'), $msg);
        } else {
            $this->setRedirect(JRoute::_('index.php?option=com_secretary&view=message&id='.$id.'&tid='.$tid.'&cid='.$cid, false), $msg);
        }
    
        return true;
    }
    
}
