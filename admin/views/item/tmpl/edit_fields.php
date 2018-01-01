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
 
// No direct access
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
	<div id="secretary-fields-values" class="controls"><?php if(in_array($this->item->type,array('newsletter','sql','search','accounts_tax'))) echo $this->form->getInput('values'); ?></div>
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
	
	<?php if($this->item->type == 'list' && !empty( $this->item->values )) { ?>
	var itemValues = <?php echo $this->item->values; ?>;
	<?php } ?>
	
	var createValues = function() {
		var value = $('#jform_type').val();
		if(!($('#secretary-fields-values.controls').is(':empty')))
			return;
console.log(value);
		switch (value) {
    		case 'list' :
    			$('#secretary-fields-values.controls').append('<div class="fields-items"></div><div id="value-add" class="btn btn-default" counter="<?php echo 0 + count($values); ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>');
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
    		case 'search' : case 'accounts_tax':
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
	
	$('#value-add').live('click',function(){
		createListOption('','');
	});
	
	$('.field-remove').live('click',function(){
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