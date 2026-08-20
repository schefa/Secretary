<?php
 
defined('_JEXEC') or die;

$extension = 'times';

$user = \Secretary\Joomla::getUser();

$status = Secretary\Database::getQuery('status',$this->item->state,'id','title','loadResult');
?>
           
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="nav-item"><a href="#home" role="tab" data-bs-toggle="tab" class="nav-link active"><?php echo \Joomla\CMS\Language\Text::_('JDETAILS', true); ?></a></li>
    <li class="nav-item"><a href="#fields" role="tab" data-bs-toggle="tab" class="nav-link"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELDS', true); ?></a></li>
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
                        <div class="controls"><?php echo \Joomla\CMS\Language\Text::_($status); ?></div>
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
                        <?php if ($this->item->upload)
                        {
                            ?>
                        <div class="upload-file"><a data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo \Joomla\CMS\Router\Route::_('/administrator/components/com_secretary/uploads/'. $this->business['id'].'/time/'.$this->item->upload);?>"}'><?php echo $this->item->upload; ?></a></div>
                        <?php } ?>
						</div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('contacts'); ?></div>
                        <div class="controls">
						<?php if (!empty( $this->contacts))
						{
                            ?>
                        	<ul>
                            <?php foreach ( $this->contacts AS $idx => $contact)
                            {
								$subject = \Secretary\Database::getQuery('subjects',$contact->id,'id','firstname,lastname'); ?>
								<li>
									<a href="index.php?option=com_secretary&view=subject&id=<?php echo $contact->id; ?>"><?php echo $subject->firstname.' '.$subject->lastname; ?></a>
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
                        if ($this->item->location_id > 0)
                        {
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
            <?php if (!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true)))
            {
                ?>
                <?php foreach ($fields as $field)
                {
                    ?>
                   
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
