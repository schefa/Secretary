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

$extension = 'times';

$user = JFactory::getUser();

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
