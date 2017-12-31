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

    <div class="secretary-toolbar fullwidth">
        <div class="secretary-title">
            <span><a href="<?php echo Secretary\Route::create('messages'); ?>"> <?php echo JText::_('COM_SECRETARY_MESSAGES'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <span><?php echo JText::_('COM_SECRETARY_MESSAGE'); ?></span>
            
			<?php if(\Secretary\Helpers\Access::edit('message',$this->item->id,$this->item->created_by)) { ?>
            <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=message.edit&id=<?php echo $this->item->id;?>&catid=<?php echo $this->item->catid;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
			<?php } ?>
            
        </div>
    </div>
    
    <hr />
    
    <div class="fullwidth">
    	<?php if( $this->item->refer_to > 0 ) { ?>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('refer_to');?></div>
            <?php $parentSubject = Secretary\Database::getQuery('messages',$this->item->refer_to,'id','subject','loadResult'); ?>
            <div class="controls"><?php echo Secretary\Utilities::cleaner($parentSubject,true); ?></div>
        </div>
		<?php } ?>
        
        <div class="row">
            <div class="col-md-5 pull-left">
                <div class="row">
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('contact_to');?></div>
                            <div class="controls"><?php
							if(is_numeric($this->item->contact_to)) 
								$this->item->contact_to = Secretary\Database::getQuery('subjects',$this->item->contact_to,'id','CONCAT(firstname,lastname)','loadResult');
							echo $this->item->contact_to;
							
							?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('contact_to_alias');?></div>
                            <div class="controls"><?php echo $this->item->get('contact_to_alias');?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5 pull-right">
            	<div class="row">
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created_by');?></div>
                            <div class="controls"><?php echo $this->item->get('created_by');?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created_by_alias');?></div>
                            <div class="controls"><?php echo $this->item->get('created_by_alias');?></div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <hr />
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('subject');?></div>
            <div class="controls"><?php echo $this->item->subject; ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('message');?></div>
            <div class="controls"><?php echo $this->item->message; ?></div>
        </div>
        
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('upload');?></div>
                    <div class="controls">
                        <?php
                            if($this->item->upload) {
                                $uploadTitle = Secretary\Database::getQuery('uploads',$this->item->upload,'id','id,extension,itemID,business,title,folder');
                        ?>
                            <div class="document-document-file">
                                <?php \Secretary\Helpers\Uploads::getUploadFile($uploadTitle, '', 200); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('created');?></div>
                    <div class="controls"><?php echo JHtml::_('date', $this->item->created);?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('priority');?></div>
                    <div class="controls"><?php echo $this->item->priority; ?></div>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="fullwidth">
        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
        <div class="fields-items">
        <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
            <?php foreach($fields as $field) { ?>
                <div class="field-item">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                        <div class="controls"><?php echo $field[2]; ?></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        </div>
        
    </div>
    
</div>
</div> 