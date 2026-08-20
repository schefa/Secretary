<?php
 
defined('_JEXEC') or die;

// The trigger button carries data-joomla-dialog; that contract is implemented
// by this asset, which is not loaded on these pages by default.
Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager()
    ->useScript('joomla.dialog-autocreate');

//JHtml::_('formbehavior.chosen', 'select');

$options = array(
	\Joomla\CMS\HTML\HTMLHelper::_('select.option', 'c', \Joomla\CMS\Language\Text::_('JLIB_HTML_BATCH_COPY')),
	\Joomla\CMS\HTML\HTMLHelper::_('select.option', 'm', \Joomla\CMS\Language\Text::_('JLIB_HTML_BATCH_MOVE'))
);

$extension	= 'events';
?>
<template id="secretary-batch-dialog">
	<div class="secretary-modal-top">
		<button type="button" class="close" onclick="this.closest('joomla-dialog').close();">x</button>
		<h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_BATCH_OPTIONS');?></h3>
	</div>

    <div class="secretary-modal-content fullwidth">

		<div class="col-md-6">
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
                </label>
                
                <select name="batch[folder_id]" class="inputbox" id="batch-category-id">
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                </select>
            </div>
            
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-states" class="control-label">
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_STATUS'); ?>
                </label>
                
                <select name="batch[states]" class="inputbox" id="batch-states">
                    <option value=""><?php echo \Joomla\CMS\Language\Text::_('JSELECT') ?></option>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->states );?>
                </select>
            </div>
            
            <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TASKS') ?></h3>
            <p class="secretary-desc"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_BATCH_TASK_INFO'); ?></p>
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="project_id_copy" class="control-label">
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_BATCH_TASK_COPY_TO_PROJECT'); ?>
                </label>
                
                <select name="batch[project_id_copy]" class="inputbox" id="project_id_copy">
                    <option value=""><?php echo \Joomla\CMS\Language\Text::_('JSELECT') ?></option>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->projects, 'id', 'title');?>
                </select>
            </div>
            
        </div>
        
        
		<?php echo Secretary\HTML::_('datafields.item'); ?>
		<div class="col-md-6">
			<h4><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_BATCH_REMOVE_FIELD'); ?></h4>
			<div class=" select-arrow-control">
    			<span class="select-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'); ?></span>
    			<input type="text" name="batch[removefield]" value="" />
			</div>
			<h4><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_BATCH_ADD_FIELD'); ?></h4>
            <div class="fields-items"></div>
            <div class="field-add-container clearfix">
                <?php echo Secretary\HTML::_('datafields.listOptions', 'times' ); ?>
                <div id="field-add" counter="0"><span class="fa fa-plus"></span> <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NEW'); ?></div>
            </div>
		</div>
        
        <script type="text/javascript">Secretary.printFields( [] );</script>

	</div>
	

    <div class="secretary-modal-bottom">
		<button class="btn" type="button" onclick="document.getElementById('batch-category-id').value='';this.closest('joomla-dialog').close();">
			<?php echo \Joomla\CMS\Language\Text::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('time.batch');">
			<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</template>