<?php
 
defined('_JEXEC') or die;

\Joomla\CMS\HTML\HTMLHelper::_('behavior.formvalidator');
$app	= \Secretary\Joomla::getApplication();
$adminmail	= $app->getCfg('mailfrom');

$user = \Secretary\Joomla::getUser();
 

if (!empty($this->item->message['text']))
{
	$messageText = $this->item->message['text'] ;
}
elseif (empty($this->item->message['text']) && !empty($this->emailTemplate->text))
{
	$messageText = \Secretary\Helpers\Templates::transformText( $this->emailTemplate->text, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); 
}
else
{
	$messageText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_NOTEMPLATE');
}

?>

<div class="secretary-modal-top">
    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL'); ?></h3>
</div>
        
<?php
if (!empty($this->item->subject[6]))
{
	$emailed = (isset($this->item->message['emailed']) && $this->item->message['emailed'] > 0) ? $this->item->message['emailed'] : '';
    ?>
<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=document&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="document-form" class="form-validate">

<div class="secretary-modal-content"> 
	
    <div class="row">	
        
        <div class="col-md-9">
        <div class="control-group">	
            <div class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_SUBJECT');?></div>
            <input id="jform_message_subject" class="fullwidth form-control" type="text" name="jform[fields][message][subject]" value="<?php 
            if (isset($this->item->message['subject']))
            {
                echo $this->item->message['subject'];
            } ?>">
        </div>
        </div>
        
        <div class="col-md-3">
        <div class="control-group">
            <div class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL') .' '.\Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE');?>&nbsp;<a href="index.php?option=com_secretary&view=templates&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></div>
            <div class="select-template select-arrow select-arrow-white select-large" data-document="<?php echo $this->item->id; ?>"><?php echo $this->emailtemplates;?></div>
        </div>
        </div>
        
    </div>
    
    <div class="control-group">	
        <div class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_MESSAGE');?></div>
        <textarea id="jform_message_text" class="fullwidth form-control" rows="10" name="jform[fields][message][text]"><?php echo $messageText; ?></textarea>
    </div>
    
    <input type="hidden" name="jform[fields][message][id]" value="<?php if (isset($this->item->message['id']))
    {
        echo $this->item->message['id'];
                                                                  } ?>" />
    
    <div class="control-group">	
        <div class="control-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ATTACHMENT');?></div>
        <?php 
        $path = SECRETARY_ADMIN_PATH.'/uploads/'.$this->business['id'].'/emails/';
		$filename = strtolower($this->item->document_title) .'-'.$this->item->createdEntry.'.pdf';
		
        if (\Joomla\Filesystem\File::exists($path . $filename))
        {
			echo '<div class="btn-group">';
			echo '<a class="btn btn-default" data-joomla-dialog=\'{"popupType": "iframe", "src": "'. \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&document='.$this->item->createdEntry.'&catid='.$this->item->catid.'&tmpl=component').'"}\'>'.$filename.'</a>';
			echo '<a class="btn btn-default " target="_parent" href="'. \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&task=openFile&format=pdf&download=1&document='.  $this->item->createdEntry.'&catid='.$this->item->catid.'&tmpl=component').'"><i class="fa fa-download"></i></a>';
			echo '</div>';
			    
        }
		?>
    </div>
    
    <div class="alert alert-danger" style="display:none"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_SAVE_CHANGES_FIRST');?></div>
    
    <div class="btn-submit">
    	<div data-table="documents" data-id="<?php echo $this->item->id; ?>" class="btn btn-default"><?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_SAVE_THIS', \Joomla\CMS\Language\Text::_('COM_SECRETARY_CHANGES'));?></div>
    </div>
      
</div>

<div class="secretary-modal-bottom">

    <div class="btn-group">
        
        <a class="btn btn-success btn-email" onclick="Secretary.Ajax.call(this,'document.email',<?php echo $this->item->id; ?>);"><span class="fa fa-paper-plane"></span><?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_EMAIL_SEND_TO', $this->item->subject[1] .' ('.$this->item->subject[6].')'); ?></a>
        <?php 
        if (isset($this->item->message['emailed']) && $this->item->message['emailed'] > 0)
        {
            ?>
            <div class="btn">
                <?php
                $timeAgo = Secretary\Utilities\Time::elapse($this->item->message['emailed']);
                echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_EMAIL_SENT_ON',$timeAgo);
                ?>
            </div>
                <?php } ?>	
    </div>
    <input id="emailed" type="hidden" name="jform[fields][message][emailed]" value="<?php echo $emailed; ?>" />
    
    <div class="btn-group">
        <a class="btn btn-default btn-email" onclick="Secretary.Ajax.call(this,'document.testemail',<?php echo $this->item->id; ?>);"><?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_EMAIL_SEND_TO', $user->email); ?></a>
        
    </div>
    
</div>

	<input id="form-task" type="hidden" name="task" value="" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
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


<?php }
else
{
    echo '<div class="secretary-modal-content">'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_NONE') . ' ' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL') .'</div>'; } ?>

    