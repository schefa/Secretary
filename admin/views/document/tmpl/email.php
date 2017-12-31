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

JHTML::_('behavior.formvalidator');
$app	= JFactory::getApplication();
$adminmail	= $app->getCfg('mailfrom');

$user = JFactory::getUser();
 

if(!empty($this->item->message['text'])) {
	$messageText = $this->item->message['text'] ;
} elseif(empty($this->item->message['text']) && !empty($this->emailTemplate->text)) {
	$messageText = \Secretary\Helpers\Templates::transformText( $this->emailTemplate->text, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); 
} else {
	$messageText = JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE');
}

?>

<div class="secretary-modal-top">
    <button class="modal-close" data-dismiss="modal" type="button">x</button>
    <h3><?php echo JText::_('COM_SECRETARY_EMAIL'); ?></h3>
</div>
        
<?php
if(!empty($this->item->subject[6])) {
	
	$emailed = (isset($this->item->message['emailed']) && $this->item->message['emailed'] > 0) ? $this->item->message['emailed'] : '';
?>
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=document&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="document-form" class="form-validate">

<div class="secretary-modal-content"> 
	
    <div class="row">	
        
        <div class="col-md-9">
        <div class="control-group">	
            <div class="control-label"><?php echo JText::_('COM_SECRETARY_EMAIL_SUBJECT');?></div>
            <input id="jform_message_subject" class="fullwidth form-control" type="text" name="jform[fields][message][subject]" value="<?php if(isset($this->item->message['subject'])) echo $this->item->message['subject']; ?>">
        </div>
        </div>
        
        <div class="col-md-3">
        <div class="control-group">
            <div class="control-label"><?php echo JText::_('COM_SECRETARY_EMAIL') .' '.JText::_('COM_SECRETARY_TEMPLATE');?>&nbsp;<a href="index.php?option=com_secretary&view=templates&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></div>
            <div class="select-template select-arrow select-arrow-white select-large" data-document="<?php echo $this->item->id; ?>"><?php echo $this->emailtemplates;?></div>
        </div>
        </div>
        
    </div>
    
    <div class="control-group">	
        <div class="control-label"><?php echo JText::_('COM_SECRETARY_MESSAGE');?></div>
        <textarea id="jform_message_text" class="fullwidth form-control" rows="10" name="jform[fields][message][text]"><?php echo $messageText; ?></textarea>
    </div>
    
    <input type="hidden" name="jform[fields][message][id]" value="<?php if(isset($this->item->message['id'])) echo $this->item->message['id']; ?>" />
    
    <div class="control-group">	
        <div class="control-label"><?php echo JText::_('COM_SECRETARY_ATTACHMENT');?></div>
        <?php 
		$path = JPATH_SITE.'/administrator/components/com_secretary/uploads/'.$this->business['id'].'/emails/';
		$filename = strtolower($this->item->document_title) .'-'.$this->item->createdEntry.'.pdf';
		jimport('joomla.filesystem.file');
		if (JFile::exists($path . $filename)){
			
			echo '<div class="btn-group">';
			echo '<a class="btn btn-default modal" rel="{size: {x: 900, y: 500}, handler:\'iframe\'}" href="'. JRoute::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&document='.$this->item->createdEntry.'&catid='.$this->item->catid.'&tmpl=component').'">'.$filename.'</a>';
			echo '<a class="btn btn-default " target="_parent" href="'. JRoute::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&download=1&document='.  $this->item->createdEntry.'&catid='.$this->item->catid.'&tmpl=component').'"><i class="fa fa-download"></i></a>';
			echo '</div>';
			    
		}
		?>
    </div>
    
    <div class="alert alert-danger" style="display:none"><?php echo JText::_('COM_SECRETARY_EMAIL_SAVE_CHANGES_FIRST');?></div>
    
    <div class="btn-submit">
    	<div data-table="documents" data-id="<?php echo $this->item->id; ?>" class="btn btn-default"><?php echo JText::sprintf('COM_SECRETARY_SAVE_THIS', JText::_('COM_SECRETARY_CHANGES'));?></div>
    </div>
      
</div>

<div class="secretary-modal-bottom">

    <div class="btn-group">
        
        <a class="btn btn-success btn-email" onclick="Secretary.Ajax.call(this,'document.email',<?php echo $this->item->id; ?>);"><span class="fa fa-paper-plane"></span><?php echo JText::sprintf('COM_SECRETARY_EMAIL_SEND_TO', $this->item->subject[1] .' ('.$this->item->subject[6].')'); ?></a>
        <?php if(isset($this->item->message['emailed']) && $this->item->message['emailed'] > 0)  { ?>
            <div class="btn">
                <?php
                $timeAgo = Secretary\Utilities\Time::elapse($this->item->message['emailed']);
                echo JText::sprintf('COM_SECRETARY_EMAIL_SENT_ON',$timeAgo);
                ?>
            </div>
        <?php } ?>	
    </div>
    <input id="emailed" type="hidden" name="jform[fields][message][emailed]" value="<?php echo $emailed; ?>" />
    
    <div class="btn-group">
        <a class="btn btn-default btn-email" onclick="Secretary.Ajax.call(this,'document.testemail',<?php echo $this->item->id; ?>);"><?php echo JText::sprintf('COM_SECRETARY_EMAIL_SEND_TO', $user->email); ?></a>
        
    </div>
    
    <div class="btn-group">
        <a class="btn btn-default btn-email" onclick="Secretary.Ajax.call(this,'document.message',<?php echo $this->item->id; ?>);$(this).addClass('btn-email-disable');"><?php echo JText::sprintf('COM_SECRETARY_SAVE_THIS', JText::_('COM_SECRETARY_MESSAGE')); ?></a>
    </div>
    
    <div class="btn modal-close pull-right" ><?php echo JText::_('COM_SECRETARY_TOOLBAR_CLOSE'); ?></div>
    
</div>

	<input id="form-task" type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script>
jQuery(document).ready(function($){
	
    $("form#document-form").on("keyup keydown keypress change blur", function() {
        if ($(this).val() != $.data(this, "lastvalue")) {
			$('.secretary-container .alert').show();
			$('.btn-email').hide();
        }
        $.data(this, "lastvalue", $(this).val());
    });
	
	$('.btn-submit .btn').bind('click', function (event) {
		event.preventDefault();
		$(this).addClass('ui-autocomplete-loading');
		var form = $(this).parents('form:first');
		var formTask = form.children('#form-task');
		formTask.val('ajax.update');
		var container = $(this);
		var table	= $(this).data('table');
		var id		= $(this).data('id');
		$.ajax({
			type: 'POST',
			url:  "index.php?option=com_secretary&task=ajax.update&table=" + table + "&id=" + id,
			data: form.serialize(),
			success: function (response) {
				container.removeClass('ui-autocomplete-loading');
				container.append('<div class="input-range-save-message">'+ response + '</div>');
				formTask.val('');
            }
		});
		showEmailButtons();
	});
	
	showEmailButtons = function() {
		$('.secretary-container .alert').hide();
		$('.btn-email').show();
	}

	// Loads the template
	$('.select-template select').change(function(){
		var id = $(this).val();
		var docid = $(this).parents('.select-template').data('document');
		$.getJSON(
			"index.php?option=com_secretary&task=ajax.load&table=templates&id="+ id +"&document_id="+ docid ,
			function(data){
				if( data != null ) {
					$('#jform_message_subject').val(data.title);
					$('#jform_message_text').val(data.text);
				} else {
					$('#jform_message_subject').val('');
					$('#jform_message_text').val('');
				}
			}
		);
	});
	
});
</script>


<?php } else { echo '<div class="secretary-modal-content">'. JText::_('COM_SECRETARY_NONE') . ' ' . JText::_('COM_SECRETARY_EMAIL') .'</div>'; } ?>

    