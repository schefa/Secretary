<?php
 
defined('_JEXEC') or die;

$values = json_decode($this->item->values,true);

?>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('extension'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('type'); ?></div>
	<div id="secretary-fields-values" class="controls"><?php 
    if (in_array($this->item->type,array('newsletter','sql','search')))
	{
        echo $this->form->getInput('values');
    } ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('standard'); ?></div>
    <div id="secretary-fields-standard" class="controls"><?php echo $this->form->getInput('standard'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('required'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('required'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('hard'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('hard'); ?></div>
</div>

<script>
(function($){
	
	<?php if ($this->item->type == 'list' && !empty( $this->item->values ))
	{
        ?>
	var itemValues = <?php echo $this->item->values; ?>;
	<?php } ?>
	
	var createValues = function() {
		var value = $('#jform_type').val();
		if(!($('#secretary-fields-values.controls').is(':empty')))
			return;
console.log(value);
		switch (value) {
    		case 'list' :
    			$('#secretary-fields-values.controls').append('<div class="fields-items"></div><div id="value-add" class="btn btn-default" counter="<?php echo 0 + count($values ?? []); ?>"><span class="fa fa-plus"></span> <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NEW'); ?></div>');
    			if(typeof(itemValues) !== 'undefined') {
    				for( var key in itemValues) {
    					if(itemValues.hasOwnProperty(key)){
    						createListOption(key,itemValues[key]);
    					}
    				}
    			} else {
    				createListOption('','');
    			}
    			break;
    		case 'newsletter' : case 'sql':
    			$('#secretary-fields-values.controls').append('<textarea id="jform_values" value="" name="jform[values]"></textarea>');
    			break;
    		case 'search' :
    			$('#secretary-fields-values.controls').append('<input id="jform_values" type="text" value="" name="jform[values]">');
    			break;
		}

	}
	
	var createListOption = function(key, value) {
		var counter = $('#value-add').attr('counter'); 
		var html = $('.field-item:first').html();
		html = html.replace(/##counter##/g, counter);
		html = html.replace(/##key##/g, key);
		html = html.replace(/##value##/g, value);
		
		$('<div class="field-item">' + html + '</div>').appendTo('.fields-items').show();
		$('#value-add').attr('counter', parseInt(counter) + 1);
		
		return false;
	}
	
	createValues();
	
	$(document).on('click', '#value-add', function(){
		createListOption('','');
	});

	$(document).on('click', '.field-remove', function(){
		$(this).parents('.field-item').remove();
		return false;
	});
	
	$('#jform_type').on('change',function(){
		$('#secretary-fields-values.controls').empty();
		createValues();
	});
	
	$('#jform_title').on('keyup keydown',function(){
		var value = $(this).val();
		value = value.replace(" ","").replace(/[^a-z0-9]/gi,'');
		$('#jform_hard').val(value.toLowerCase());
	});
	
})(jQuery);
</script>