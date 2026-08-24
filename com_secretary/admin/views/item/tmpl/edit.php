<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

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

    <?php if ($this->extension == 'settings' && \Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
    :
        ?>
    <div class="tab-content">
        <div class="tab-pane" id="settings_access">

            <div class="tabbable tabs-left">

                <ul class="nav nav-tabs">
                    <?php foreach ($this->rulesList as $title => $rule)
                    :
                        ?>
                    <li class="nav-item"><a class="nav-link<?php echo ($title == 'component') ? ' active' : ''; ?>" data-bs-toggle="tab" href="#permission-<?php echo $title; ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_'. strtoupper($title)); ?></a></li>
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content">
                    <?php foreach ($this->rulesList as $title => $rule)
                    :
                        ?>
                    <div id="permission-<?php echo $title ?>" class="tab-pane<?php echo ($title == 'component') ? ' active' : ''; ?>"><?php echo $rule; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="alert alert-info"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_RULES_SETTING_NOTES_ITEM');?></div>

        </div>
    </div>
    <?php endif; ?>

</div>

</div>