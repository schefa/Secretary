<?php
 
defined('_JEXEC') or die;
?>

<div class="secretary-main-container">
 
<?php if ($this->extension == 'fields')
{
    ?>
<div class="field-item" style="display:none;">
	<div class="field-item-title">
		<input id="jform_values_##counter##_key" type="text" class="form-control" name="jform[values][##counter##][key]" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_KEY');?>" value="##key##" />
    </div>
	<div class="field-item-values">
		<input id="jform_values_##counter##_value" type="text" class="form-control" name="jform[values][##counter##][value]" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAME');?>" value="##value##" />
    </div>
    <div class="btn btn-default field-remove"><i class="fa fa-remove"></i></div>
</div>
<?php } ?>

<div class="secretary-main-area">
 
	<div class="fullwidth">
        <h2 class="documents-title">
            <span class="documents-title-first"><?php echo $this->title; ?></span>
            <?php 
            if ($this->extension !== 'settings')
            {
                ?>
                <a class="pull-right btn btn-default" href="<?php echo \Joomla\CMS\Router\Route::_("index.php?option=com_secretary&view=items&extension=".$this->extension); ?>"><i class="fa fa-angle-double-left"></i>&nbsp;<?php echo $this->title; ?></a>
            <?php } ?>
        </h2>
        <hr />
    </div>
      
	<div class="secretary-toolbar clearfix">
		<?php $this->addToolbar(); ?>
	</div> 
        
    <fieldset class="form-horizontal">   
        <form action="<?php echo Secretary\Route::create('item', array('layout'=>'edit','extension'=>$this->extension,'id'=> (int) $this->item->id)); ?>" 
        method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
        
        	<?php  echo $this->loadTemplate($this->extension); ?>
        	
            <input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
            <input type="hidden" name="task" value="" />
            <?php echo $this->form->getInput('id'); ?>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </form>
    </fieldset>
    
</div>

</div>