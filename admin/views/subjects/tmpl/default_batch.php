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
?>
<div class="hide" id="collapseModal">
<div class="secretary-modal">
    <div class="secretary-modal-inner">
    
	<div class="secretary-modal-top">
		<button type="button" class="close" data-dismiss="modal">x</button>
        <h3><?php echo JText::_('COM_SECRETARY_CATEGORIES_BATCH_OPTIONS'); ?></h3>
    </div>
        
    <div class="secretary-modal-content fullwidth">
		<div class="col-md-6">
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">
                    <?php echo JText::_('COM_SECRETARY_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
                </label>
                
                <select name="batch[folder_id]" class="inputbox" id="batch-category-id">
                    <?php echo JHtml::_('select.options', $this->folders, 'id', 'title', $this->catid, true);?>
                </select>
            </div>
            
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-states" class="control-label">
                    <?php echo JText::_('COM_SECRETARY_STATUS'); ?>
                </label>
                
                <select name="batch[states]" class="inputbox" id="batch-states">
                    <option value=""><?php echo JText::_('JSELECT') ?></option>
                    <?php echo JHtml::_('select.options', $this->states );?>
                </select>
            </div>
            
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-newsletter" class="control-label">
                    <?php echo JText::_('COM_SECRETARY_NEWSLETTER'); ?>
                </label>
                
                <select name="batch[newsletter]" class="inputbox" id="batch-states">
                    <option value=""><?php echo JText::_('JSELECT') ?></option>
                    <?php echo JHtml::_('select.options', $this->newsletter, 'id', 'title' );?>
                </select>
            </div>
            
            <div class="control-group">
                <label id="batch-choose-action-lbl" for="batch-template" class="control-label">
                    <?php echo JText::_('COM_SECRETARY_TEMPLATE'); ?>
                </label>
                
                <select name="batch[template]" class="inputbox" id="batch-template">
                    <option value=""><?php echo JText::_('JSELECT') ?></option>
                    <?php echo JHtml::_('select.options', $this->templates );?>
                </select>
            </div>
		</div>
		
		<?php echo Secretary\HTML::_('datafields.item'); ?>
		<div class="col-md-6">
			<h4><?php echo JText::_('COM_SECRETARY_BATCH_REMOVE_FIELD'); ?></h4>
			<div class=" select-arrow-control">
    			<span class="select-label"><?php echo JText::_('COM_SECRETARY_TITLE'); ?></span>
    			<input type="text" name="batch[removefield]" value="" />
			</div>
			<h4><?php echo JText::_('COM_SECRETARY_BATCH_ADD_FIELD'); ?></h4>
            <div class="fields-items"></div>
            <div class="field-add-container clearfix">
                <?php echo Secretary\HTML::_('datafields.listOptions', 'subjects' ); ?>
                <div id="field-add" counter="0"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
            </div>
		</div>
        
        <script type="text/javascript">Secretary.printFields( [] );</script>

	</div>

    <div class="secretary-modal-bottom">
		<button class="btn" type="button" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('subject.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
</div>
</div>