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

//JHtml::_('formbehavior.chosen', 'select');

$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);

$extension	= 'products';
?>
<div class="hide fade" id="collapseModal">
<div class="secretary-modal">
    <div class="secretary-modal-inner">
    <div class="secretary-modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_SECRETARY_CATEGORIES_BATCH_OPTIONS');?></h3>
	</div>
	<div class="modal-body">

        <div class="control-group">
            <label id="batch-choose-action-lbl" for="batch-category-id" class="control-label">
                <?php echo JText::_('COM_SECRETARY_CATEGORIES_BATCH_CATEGORY_LABEL'); ?>
            </label>
            
            <select name="batch[category_id]" class="inputbox" id="batch-category-id">
                <option value=""><?php echo JText::_('JSELECT') ?></option>
                <?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
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
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-category-id').value='';" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('product.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
</div>
</div>
</div>