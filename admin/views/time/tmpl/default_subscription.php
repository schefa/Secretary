<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

$user		= JFactory::getUser();
$levels		= JAccess::getAuthorisedViewLevels($user->id);

if($this->userAttendee) {
	
} elseif(isset($this->item->maxContacts)) { 
	if($this->contactsCounts < $this->item->maxContacts) {
		if(in_array($this->item->access,$levels)) { ?>
        
        <div class="secretary-event-subscription-container">
        
        <h3><?php echo ucfirst( JText::_('COM_SECRETARY_TIME_JOIN_EVENT') );?></h3>
        <form action="<?php echo JRoute::_('index.php?option=com_secretary'); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="time-form">
        
        <?php $userContact = ($user->id > 0) ? Secretary\Database::getQuery('subjects',$user->id,'created_by') : (object) array('id'=>-1);
			if(isset($userContact) && $userContact->id > 0) {
			?>
            <input type="hidden" name="cid" value="<?php echo Secretary\Security::encryptor('close',$userContact->id); ?>" />
		<?php } else { ?>
			<input type="text" name="cname" placeholder="Name" />
			<input type="text" name="cemail" placeholder="Email" />
		<?php } ?>
                
			<button type="submit" class="btn btn-success"><?php echo JText::_('COM_SECRETARY_TIME_JOIN_EVENT');?></button>
            <input type="hidden" name="id" value="<?php echo Secretary\Security::encryptor('close',$this->item->id); ?>" />
            <input type="hidden" name="task" value="time.subscription" />
            <input type="hidden" name="extension" value="<?php echo $this->item->extension; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
        
	<?php } else {
			$reason = JText::_('COM_SECRETARY_TIME_JOIN_BUT_NO_PERMISSION');
			echo '<div class="alert alert-warning">'.JText::sprintf('COM_SECRETARY_TIME_JOIN_EVENT_NOT_POSSIBLE_BECAUSE', $reason).'</div>';		
		}
	} else {
		$reason = JText::_('COM_SECRETARY_TIME_ENOUGH_ATTENDEES');
		echo '<div class="alert alert-warning">'.JText::sprintf('COM_SECRETARY_TIME_JOIN_EVENT_NOT_POSSIBLE_BECAUSE', $reason).'</div>';	
	}
}
?>
<br>