<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die; 
?>

<div class="secretary-main-container">
<div class="secretary-main-area entry-default">

    <div class="form-horizontal">

        <div class="secretary-toolbar fullwidth">
            <div class="secretary-title">
    			<span><a href="<?php echo Secretary\Route::create('subjects'); ?>"> <?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
           		<span><?php echo JText::_('COM_SECRETARY_SUBJECT'); ?></span>
                        
				<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('subject',$this->item->id,$this->item->created_by))) { ?>
                <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=subject.edit&id=<?php echo $this->item->id;?>&catid=<?php echo $this->item->catid;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
                <?php } ?>
           	</div>
        </div>
                
        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
            <li><a href="#documents" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_DOCUMENTS', true); ?></a></li>
            <li><a href="#messages" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_MESSAGES', true); ?></a></li>
            <li><a href="#projects" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PROJECTS', true); ?></a></li>
        </ul>
                
        <div class="tab-content">
        
            <div class="tab-pane active" id="home">
             
                <div class="row-fluid form-horizontal">
    
                    <div class="col-md-6 form-horizontal">
                        <div class="control-group">
                            <div class="control-label"><?php echo JText::_('COM_SECRETARY_GROUP'); ?></div>
                            <div class="controls">
                            <?php if(isset($this->item->catid)) {
                               $catTitle = Secretary\Database::getQuery('folders', $this->item->catid,'id','title','loadResult'); 
                               echo JText::_($catTitle);
                            }
                            ?>
                            </div>
                        </div>
                        <hr />
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('gender'); ?></div>
                            <div class="controls"><?php echo Secretary\Utilities::getGender($this->item->gender); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('firstname'); ?></div>
                            <div class="controls"><?php echo $this->item->firstname; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('lastname'); ?></div>
                            <div class="controls"><?php echo $this->item->lastname; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('street'); ?></div>
                            <div class="controls"><?php echo $this->item->street; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('zip'); ?></div>
                            <div class="controls"><?php echo $this->item->zip; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                            <div class="controls"><?php echo $this->item->location; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
                            <div class="controls"><?php echo $this->item->email; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
                            <div class="controls"><?php echo $this->item->phone; ?></div>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-6">
                    
                        <?php if(!empty($this->item->id) && !empty($this->documents)) {?>
                        <div class="control-group">
                        
                            <?php $document = JText::_('COM_SECRETARY_DOCUMENT'); ?>
                            <h3 class="title"><?php echo JText::sprintf('COM_SECRETARY_CREATE_THIS', $document); ?></h3>
                            
                            <?php 
                            $documents	= array();
                            foreach($this->documents as $document) {
                                $documents[] = JHtml::_('select.option', $document->id, JText::_($document->title)); }
                            ?>
                            <select id="add_new_document" data-subject="<?php echo $this->item->id;?>" class="form-control inputbox">
                                <?php echo JHtml::_('select.options', $documents, 'value', 'text');?>
                            </select>
                            <div id="add_document" class="btn btn-primary"><?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                            <script>
                            (function($){
                                $('#add_document').click(function(){
                                    var subjectId = $('#add_new_document').data('subject');
                                    var documentId = $('#add_new_document').val();
                                    var url = 'index.php?option=com_secretary&view=document&layout=edit&catid='+documentId+'&subject='+subjectId;
                                    window.location.href = url;
                                })
                            })(jQuery);
                            </script>
                        </div>
                        <hr />
                        <?php } ?>
                        <?php
						if($this->item->upload > 0 && $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload,'id','id,business,title,folder,extension,itemID')) {
							\Secretary\Helpers\Uploads::getUploadFile($logoImage, 'subject-logo', 180);
							echo '<hr />';
						}
						?>
                        <div class="control-group">
                            <h3 class="title title-edit"><?php echo JText::_('COM_SECRETARY_CONNECTIONS'); ?></h3>
                            <ul>
                                <?php
                                if(!empty($this->myConnections)) { 
									foreach($this->myConnections as $idx => $connection) { 
									    $connectedPersonId = ($this->item->id != $connection->one) ? $connection->one : $connection->two;
									    $contact = Secretary\Database::getQuery('subjects',$connectedPersonId);
									    if(isset($contact->id)) { ?>
                                    	<li>
                                    	<a href="index.php?option=com_secretary&view=subject&id=<?php echo $connectedPersonId; ?>"><?php echo $contact->firstname .' '.$contact->lastname ?></a><br>
                                    	<p class="secretary-desc"><?php echo $connection->note;?></p>
                                    	</li>
                                    <?php }
									}
								} ?>
                            </ul>
                        </div>
                        <hr />
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                            <div class="controls"><?php echo $this->item->state; ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                            <div class="controls"><?php echo JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                            <div class="controls"><?php echo JFactory::getUser($this->item->created_by)->username; ?></div>
                        </div>
                    </div>
                
                </div>
                
                <hr />
                
                <div class="col-md-12">
                    <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                    
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
            
                <hr />
                
            </div>
        
            <div class="tab-pane" id="documents">
            <?php
                if(!empty($this->item->documents)) {
                    include_once(SECRETARY_ADMIN_PATH .'/views/subject/tmpl/edit_documents.php');
				} else {
					echo '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_NONE').'</div>';	
				}
			?>
            </div>
            
            <div class="tab-pane" id="messages">
            	<?php 
            	if(!empty($this->item->messages)) {
            	    include_once(SECRETARY_ADMIN_PATH.'/views/subject/tmpl/edit_messages.php');
            	} else {
            	    echo '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_NONE').'</div>';
            	}
            	?>
            </div>
            
            <div class="tab-pane" id="projects">
            	<?php 
            	if(!empty($this->item->projects)) {
            	    include_once(SECRETARY_ADMIN_PATH.'/views/subject/tmpl/edit_times.php');
            	} else {
            	    echo '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_NONE').'</div>';
            	}
            	?>
            </div>

        </div>
    </div>
    
</div>
</div>
