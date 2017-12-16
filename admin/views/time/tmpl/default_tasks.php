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

$extension = 'times';

$user = JFactory::getUser();

// Project
$projects = array();
$standardProject = Secretary\Database::getQuery('times',$this->item->projectID,'id','title','loadResult');

$status = Secretary\Database::getQuery('status',$this->item->state,'id','title','loadResult');
?>
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="home">
    
        <div class="fullwidth">
            <div class="col-md-9">
            
                <div class="row">
                
                    <div class="col-md-6">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->item->title; ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PROJECT'); ?></label></div>
                        <div class="controls"><?php echo $standardProject; ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls"><?php echo JText::_($status); ?></div>
                    </div>
                    
                    <div class="col-md-12"><hr /></div>
                    
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('startDate'); ?></div>
                            <div class="controls"><?php echo $this->item->startDate; ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('endDate'); ?></div>
                            <div class="controls"><?php echo $this->item->endDate; ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    <hr />
                        <div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
                        <div class="controls"><?php echo $this->item->text; ?></div>
                    </div>
                    
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="fullwidth">
                
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                        <div class="controls">
                        <?php if($this->item->upload) { ?>
                        <div class="upload-file"><a class="modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}"  href="<?php echo JRoute::_('/administrator/components/com_secretary/uploads/'. $this->business['id'].'/time/'.$this->item->upload);?>"><?php echo $this->item->upload; ?></a></div>
                        <?php } ?>
						</div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('contacts'); ?></div>
                        <div class="controls">
						<?php if(!empty( $this->contacts)) { ?>
                        	<ul>
                            <?php foreach( $this->contacts AS $idx => $contact) {
								$subject = Secretary\Database::getQuery('subjects',$contact->id,'id','firstname,lastname'); ?>
								<li>
									<?php echo $subject->firstname.' '.$subject->lastname; ?>
                                    <a target="_blank" href="index.php?option=com_secretary&view=message&layout=edit&contact=<?php echo $contact->id;?>"><i class="fa fa-envelope-o"></i></a>
                                </li>
							<?php } ?>
                            </ul>
						<?php } ?>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('calctime'); ?></div>
                        <div class="controls">
						<?php echo \Secretary\Helpers\Times::secondsToWorktime($this->item->calctime); ?>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('totaltime'); ?></div>
                        <div class="controls secretary-total-hours">
						<?php echo \Secretary\Helpers\Times::secondsToWorktime($this->item->totaltime); ?>
                        </div>
                    </div>
                    
                    <?php 
                    $userContact = Secretary\Database::getQuery('subjects',$user->id,'created_by','id');
					$teamMember = 0;
					if($contacts = json_decode($this->item->contacts)) {
						foreach($contacts as $contact) {
							if(is_object($contact) && !empty($userContact) && $contact->id == $userContact->id) 
									$teamMember = $userContact->id;
						}
					}
					if($teamMember > 0) {
					?>
                                   
                    <div class="control-group"> 
                        <div class="fullwidth margin-bottom projectTimer" data-item="<?php echo $this->item->id; ?>" data-project="<?php echo $this->item->projectID; ?>">
                            <a class="btn btn-large" <?php if($this->projectTimer->action == "start") echo 'style="display:none;"'; ?>  data-task="start"><i class="fa fa-play"></i></a>
                            <a class="btn btn-large" <?php if($this->projectTimer->action != 'start') echo 'style="display:none;"'; ?> data-task="stop"><i class="fa fa-pause"></i></a>
                        </div>
                    </div>
                    <?php } ?>
                           
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('progress'); ?></div>
                                        
                        <?php if($this->canDo->get('core.edit')) { ?>
                            <div class="secretary-control-group input-range">
                                <input type="range" min="0" max="100" value="<?php echo $this->item->progress; ?>" step="0.1" name="power" list="powers"> 
                                <div class="secretary-input-prepend clearfix">
                                    <input type="number" min="0" step="0.1" max="100" value="<?php echo $this->item->progress; ?>" class="input-range-value" />
                                    <div class="secretary-add-on">%</div>
                                </div>
                                <div class="input-range-save btn btn-small btn-default" data-id="<?php echo $this->item->id; ?>" data-extension="tasks" style="display:none;"><?php echo JText::_('COM_SECRETARY_SAVE');?></div>
                            </div>
                        <?php } else { ?>
                        <div class="controls"><?php echo $this->item->progress; ?> %</div>
                        <?php }  ?>
                    </div>
                    
                </div>
            </div>
        </div>
    
    </div>
    
    <div class="tab-pane" id="fields">
    
        <div class="fields-items form-horizontal">
            <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                <?php foreach($fields as $field) { ?>
                    
                <?php $f = \Secretary\Helpers\Items::getFieldRaw($field[0],$field[2], true); ?>
                
                <div class="control-group">
                <div class="control-label">
                <label><?php echo $f[0]; ?></label>
                </div>
                <div class="controls"><?php echo $f[1]; ?></div>
                </div>
    
                <?php } ?>
            <?php } ?>
        </div>
        
    </div>
    
</div>
<input type="hidden" name="extension" value="task" />

<script>
(function($){
	$('.input-range input[type=range]').on('input', function () {
		$(this).trigger('change');
		$(this).change(function() {
			var value = $(this).val();
			$('.secretary-input-prepend input[type=number].input-range-value').val(value);
			$('.input-range-save').fadeIn().show();
		});
	});
	
	$('.input-range-save').click(function(){
		if($(this).is(':visible')) {
			$(this).addClass('ui-autocomplete-loading');
			var id = $(this).data('id');
			var extension = $(this).data('extension');
			var value = $('.input-range input[type=range]').val();
			$.ajax({
				url: "index.php?option=com_secretary&task=ajax.saveProgress&value=" + value + "&id=" + id + "&extension=" + extension ,
				type: 'get',
				success: function(response){
					$(this).removeClass('ui-autocomplete-loading');
					$('.input-range').append('<div class="input-range-save-message">'+response+'</div>').fadeIn();
				}
			});
			$(this).hide();
		}
	});
	
	$('.projectTimer a.btn').click(function() {
		$(this).parent().children().toggle();
		var itemID = $(this).parent().data("item");
		var projectID = $(this).parent().data("project");
		var task = $(this).data("task");
		$.ajax({
			url: "index.php?option=com_secretary&task=ajax.projectTimer&action=" + task + "&itemID=" + itemID + "&pid=" + projectID ,
			type: 'get',
			success: function(response){
				$('.secretary-total-hours').html(response).fadeIn();
			}
		});
	});
})(jQuery);
</script>