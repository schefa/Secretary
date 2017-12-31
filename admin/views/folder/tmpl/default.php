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
            <span><a href="<?php echo Secretary\Route::create('folders'); ?>"> <?php echo JText::_('COM_SECRETARY_FOLDERS'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <span><?php echo JText::_('COM_SECRETARY_FOLDER'); ?></span>
			<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('folder',$this->item->id,$this->item->created_by))) { ?>
            <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=folder.edit&id=<?php echo $this->item->id;?>&extension=<?php echo $this->item->extension;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
            <?php } ?>
        </div>
    </div>


<?php if($this->item->id) { ?>
    <ul class="nav nav-tabs margin-bottom fullwidth" id="myTab" role="tablist">
        <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    </ul>
 
    <div class="tab-content">
        <div class="tab-pane active" id="home">
    
            <div class="fullwidth">
                <div class="col-md-6">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->item->title; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                        <div class="controls"><?php echo $this->item->alias; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('parent_id'); ?></div>
                        <div class="controls"><?php echo $this->item->parent_id; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="fullwidth margin-bottom">
                <div class="col-md-12">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                        <div class="controls"><?php echo $this->item->description; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="fullwidth">
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('number'); ?></div>
                        <div class="controls"><?php echo $this->item->number; ?></div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls"><?php echo $this->item->state; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('business'); ?></div>
                        <div class="controls"><?php echo $this->item->business; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_EXTENSION'); ?></label></div>
                        <div class="controls"><?php echo $this->item->extension; ?></div>
                    </div>
                </div>
            </div>
            
            <hr />
        
            <div class="fullwidth">
                <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                
                <div class="fields-items form-horizontal">
                    <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                        <?php foreach($fields as $field) { ?>
                                    
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_($field[1]); ?></label></div>
                                <div class="controls"><?php echo $field[2]; ?></div>
                            </div>
    
                        <?php } ?>
                    <?php } ?>
                </div>
                
            </div>
                
        </div>
        
    </div>
    
<?php } ?>    
</div>  
</div>
