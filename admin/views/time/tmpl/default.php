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
?>

<div class="secretary-main-container">

<?php echo Secretary\HTML::_('datafields.item'); ?>
<div class="secretary-main-area entry-default">

    <div class="secretary-toolbar fullwidth">
      
        <div class="pull-left margin-right">
            <div class="secretary-title">
				<?php if($this->item->extension === 'tasks') { ?>  
                <span><a href="<?php echo Secretary\Route::create('time',array('extension'=>'projects','id'=>$this->item->projectID)); ?>"><?php echo JText::_('COM_SECRETARY_PROJECT'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
                <?php } ?>
    			<span><a href="<?php echo Secretary\Route::create('times',array('extension'=>$this->item->extension)); ?>"> <?php echo JText::_('COM_SECRETARY_'. $this->item->extension); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
           		<span><?php echo $this->title; ?></span>
                        
				<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('time',$this->item->id,$this->item->created_by))) { ?>
                <a class="pull-right btn btn-saveentry" href="index.php?option=com_secretary&task=time.edit&id=<?php echo $this->item->id;?>&extension=<?php echo $this->item->extension;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
                <?php } ?>
           	</div>
        </div>
        
    </div>
    
	<?php if($this->item->id > 0 && (in_array($this->extension,array('events','projects','tasks')))) echo $this->loadTemplate('subscription'); ?>
    
    <?php echo $this->loadTemplate($this->extension); ?>

	<hr />
    
    <div class="fullwidth">
        <div class="col-md-3">
            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_BUSINESS'); ?></label></div>
            <div class="controls"><?php echo Secretary\Database::getQuery('businesses',$this->item->business,'id','title','loadResult'); ?></div>
        </div>
    </div>
    
</div>
</div>

