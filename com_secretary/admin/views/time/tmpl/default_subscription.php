<?php
 
defined('_JEXEC') or die;

$user		= \Secretary\Joomla::getUser();
$levels		= \Joomla\CMS\Access\Access::getAuthorisedViewLevels($user->id);

if ($this->userAttendee)
{
}
elseif (isset($this->item->maxContacts))
{
	if ($this->contactsCounts < $this->item->maxContacts)
	{
		if (in_array($this->item->access,$levels))
		{
            ?>
        
        <div class="secretary-event-subscription-container">
        
        <h3><?php echo ucfirst( \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_JOIN_EVENT') );?></h3>
        <form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary'); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="time-form">
        
            <?php $userContact = ($user->id > 0) ? Secretary\Database::getQuery('subjects',$user->id,'created_by') : (object) array('id'=>-1);
			
            if (isset($userContact) && $userContact->id > 0)
            {
                ?>
            <input type="hidden" name="cid" value="<?php echo Secretary\Security::encryptor('close',$userContact->id); ?>" />
                    <?php }
            else
            {
                ?>
			<input type="text" name="cname" placeholder="Name" />
			<input type="text" name="cemail" placeholder="Email" />
		<?php } ?>
                
			<button type="submit" class="btn btn-success"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_JOIN_EVENT');?></button>
            <input type="hidden" name="id" value="<?php echo Secretary\Security::encryptor('close',$this->item->id); ?>" />
            <input type="hidden" name="task" value="time.subscription" />
            <input type="hidden" name="extension" value="<?php echo $this->item->extension; ?>" />
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </form>
        </div>
        
	<?php }
        else
        {
			$reason = \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_JOIN_BUT_NO_PERMISSION');
			echo '<div class="alert alert-warning">'.\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_TIME_JOIN_EVENT_NOT_POSSIBLE_BECAUSE', $reason).'</div>';		
        }
	}
    else
    {
		$reason = \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_ENOUGH_ATTENDEES');
		echo '<div class="alert alert-warning">'.\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_TIME_JOIN_EVENT_NOT_POSSIBLE_BECAUSE', $reason).'</div>';	
    }
}
?>
<br>