<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = JFormHelper::loadFieldType('SecretarySections', false)->getModulesArray();

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage',		'com_secretary');

?>

<table class="table table-hover" id="entriesList">
    <thead>
        <tr>
        
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            
            <th width="1%">
            
            <div class="order nowrap center hidden-phone"><?php if ($this->canDo->get('core.edit')) { ?><a onclick="Joomla.submitbutton('items.saveOrder')"><i class="fa fa-save"></i></a><?php } ?>&nbsp;</div>
            
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_STATUS', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?> (<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_CLOSETASK', 'a.closeTask', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>)
            </th>
            <th width="1%" class='left'>
            <?php echo JText::_('COM_SECRETARY_PREVIEW'); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_DESCRIPTION', 'a.desc', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort', 'COM_SECRETARY_SECTION', 'a.extension', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
        
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
        <tr class="row<?php echo $i % 2; ?> secretary-sort-row">
            
            <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <span class="lbl"></span>
            </td>
            
            <td>
                <div class="order nowrap center hidden-phone">
                	<div class="secretary-sort">
                    	<span class="move-up"><i class="fa fa-caret-up"></i></span>
                    	<span class="move-down"><i class="fa fa-caret-down"></i></span>
                	</div>
                    <input type="hidden" name="order[<?php echo $this->escape($item->extension)?>][]" value="<?php echo (int) $item->id;?>" />
                </div>
            </td>

            <td>
                <?php if ($canCheckin) : ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=item&layout=edit&id='.(int) $item->id .'&extension='.$this->extension.'&module='.$item->extension); ?>">
                    <?php echo JText::_($item->title); ?></a>
                <?php else : ?>
                    <?php echo JText::_($item->title); ?>
                <?php endif; ?>
            &#8594; 
            	<?php echo  JText::_(Secretary\Database::getQuery('status',$item->closeTask,'id','title','loadResult')); ?>
            </td>
            
            <td>
            <?php $state = array('title' => $item->title,'class' => $item->class,'description' => $item->description,'icon' => $item->icon ); ?>
            <?php echo Secretary\HTML::_('status.state', $item, $i, $item->extension , false, $state ); ?>
            </td>
            <td><div class="secretary-status-tooltip-preview-triagle"></div><div class="secretary-status-tooltip-preview"><?php echo JText::_($item->description); ?></div></td>
            <td><?php if( $item->extension !='root') echo $modules[$item->extension]; ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-list-pagination">
        <?php 
        if(isset($this->items[0])){
            $colspan = count(get_object_vars($this->items[0]));
        }
        else{
            $colspan = 10;
        }
    ?>
    <tr>
        <td colspan="<?php echo $colspan ?>">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFieldsStatus(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>

<input type="hidden" name="module" value="<?php echo $this->module; ?>" />