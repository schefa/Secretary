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

$user	= JFactory::getUser();
$userId	= (int) Secretary\Database::getQuery('subjects', (int) $user->id,'created_by','id','loadResult');

$status = Secretary\Database::getQuery('status',$this->item->state,'id','title','loadResult');
$category = Secretary\Database::getQuery('folders',$this->item->catid,'id','title','loadResult');

// Document
if($this->item->document_id > 0 ) {
	$object = Secretary\Database::getQuery('documents',$this->item->document_id);
	if(!empty($object)) {
    	$object->category = Secretary\Database::getQuery('folders',$object->catid,'id','title','loadResult');
    	$document = array();
    	\Secretary\Helpers\Documents::getDocumentsPrepareRow($document,$object);
	}
}
?>
           
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#tasks" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_TASKS', true); ?></a></li>
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
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls"><?php echo JText::_($status); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                        <div class="controls"><?php echo JText::_($category); ?></div>
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
                            
                        <?php if(!empty($this->item->upload)) {
                            $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload,'id','business,title,folder');?>
                            <div class="upload-file">
                            <?php \Secretary\Helpers\Uploads::getUploadFile( $logoImage, NULL, 200); ?>
                            </div>
                        <?php } ?>
                    
						</div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TEAM'); ?></label></div>
                        <div class="controls">
                        
						<?php if(!empty( $this->contacts)) { ?>
                        	<ul>
                            <?php foreach( $this->contacts AS $idx => $contact) {
								$subject = Secretary\Database::getQuery('subjects',$contact->id,'id','firstname,lastname'); ?>
								<li>
									<a href="index.php?option=com_secretary&view=subject&id=<?php echo $contact->id; ?>"><?php echo $subject->firstname.' '.$subject->lastname; ?></a>
                                    <a target="_blank" href="index.php?option=com_secretary&view=message&layout=edit&contact=<?php echo $contact->id;?>"><i class="fa fa-envelope-o"></i></a>
                                </li>
							<?php } ?>
                            </ul>
						<?php } ?>
                        
                        </div>
                    </div>
                    
                    <hr />
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('document_id'); ?></div>
                        <div class="controls">
						<?php
						    if(!empty($document) && $this->item->document_id > 0) {
								echo '<a href="index.php?option=com_secretary&view=document&id='.$this->item->document_id .'">'
									. $document['value'] .'</a>'
									. '<br>'.JText::_('COM_SECRETARY_BUDGET') . ': '
									.  Secretary\Utilities\Number::getNumberFormat($document['total']) .' '.$document['currency'];
							} elseif($user->authorise('com_secretary','time.'.$this->item->id)) { ?> 
                                            
                                <?php $document = JText::_('COM_SECRETARY_DOCUMENT'); ?>
                                <span class="select-label"><?php echo JText::sprintf('COM_SECRETARY_CREATE_THIS', $document); ?></span>
                                
                                <?php 
                                $documents = \Secretary\Helpers\Folders::getList("documents");
                                $options	= array();
                                foreach($documents as $document) {
                                    $options[] = JHtml::_('select.option', $document->id, JText::_($document->title)); }
                                ?>
                                <div class="select-arrow select-arrow-white">
                                <select id="add_new_document">
                                    <?php echo JHtml::_('select.options', $options, 'value', 'text');?>
                                </select>
                                </div>
                                <div id="add_document" class="btn btn-newentry"><?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                                <script>
                                (function($){
                                    $('#add_document').click(function(){
                                        var documentId = $('#add_new_document').val();
                                        var url = 'index.php?option=com_secretary&view=document&layout=edit&tid=<?php echo $this->item->id; ?>&catid='+documentId;
                                        window.location.href = url;
                                    })
                                })(jQuery);
                                </script>
                
						<?php } ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('location_id'); ?></div>
                        <div class="controls"><?php 
                        	if($this->item->location_id > 0) {
								echo '<a href="index.php?option=com_secretary&view=location&id='.$this->item->location_id .'">'
									. Secretary\Database::getQuery('locations',$this->item->location_id,'id','title','loadResult') .'</a>';
							}
							?>
						</div>
                    </div>
                    
                </div>
            </div>
        </div>
                  
    </div>
    
    <div class="tab-pane" id="tasks">
		<div class="col-md-12"> 
            <div class="project-task-items">
            <?php if(!empty($this->item->tasks)) {
                
                // P r o j e k t a u f g a b e n 
                foreach($this->item->tasks as $x => $task) :
                        
                   Secretary\HTML::_('times.singleViewProjectTask', $x , $task, $userId, $this->canDo->get('core.edit') );
                            
                    if(!empty($task->subtasks) && !empty($task->subtasks[0]->id)) {
                        
                        // U n t e r a u f g a b e n 
                        foreach($task->subtasks as $x => $subtask) :
                        if(is_int($x))
                        {
                            Secretary\HTML::_('times.singleViewProjectTask', $x , $subtask, $userId, $this->canDo->get('core.edit'), 2);
                        }
                        endforeach;
                    }
                    
                endforeach;
                ?>
                            
                <script>
                (function($){
                    $('.projectTimer a').click(function() {
                        $(this).parent().children().toggle();
                        var itemID = $(this).parent().data("item");
                        var projectID = $(this).parent().data("project");
                        var task = $(this).data("task");
                        $.ajax({
                            url: "index.php?option=com_secretary&task=ajax.projectTimer&action=" + task + "&itemID=" + itemID + "&pid=" + projectID ,
                            type: 'get',
                            success: function(response){
                                if(task == 'stop') $('.totalworktime-'+ itemID).text(response);
                            }
                        });
                    });
                })(jQuery);
                </script>
            <?php } ?>
            </div>
        </div>
    </div>
        
    <div class="tab-pane" id="fields">

        <div class="fields-items form-horizontal">
            <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                <?php foreach($fields as $field) { ?>
                    
                <div class="control-group">
                    <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                    <div class="controls"><?php echo $field[2]; ?></div>
                </div>
    
                <?php } ?>
            <?php } ?>
        </div>
                        
    </div>
    
</div>
<input type="hidden" name="extension" value="time" />
