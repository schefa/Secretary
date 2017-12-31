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

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage', 'com_secretary');

/*

*/
?>

	<div class="alert alert-warning"><?php echo JText::_('COM_SECRETARY_ACCOUNTING_INFO'); ?></div>
<table class="table table-hover" id="entriesList">
    <thead>
        <tr>
        
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            
            <?php if (isset($this->items[0]->id)): ?>
            <th width="1%" class="nowrap center hidden-phone">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <?php endif; ?>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_NR', 'a.nr', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_PID', 'a.kid', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'Ordering', 'a.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_TYPE', 'a.state', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
        
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
        <tr class="row<?php echo $i % 2; ?>">
            
            <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <span class="lbl"></span>
            </td>
            
            <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
            
            <td><?php echo $item->nr; ?></td>
            
            <td>
			
				<?php echo str_repeat( "&mdash;", $item->level); ?>
            
            
                <?php if ($canCheckin) : ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=accounting&layout=edit&id='.(int) $item->id .'&extension='.$this->extension ); ?>">
                    <?php echo $item->title; ?></a>
                <?php else : ?>
                    <?php echo $item->title; ?>
                <?php endif; ?>
            </td>
            
            <td><?php echo $item->parent_id; ?></td>
            <td><?php echo $item->ordering; ?></td>
            <td><?php echo $item->locked; ?> <?php echo $item->type; ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-list-pagination">
    <tr>
        <td colspan="4">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>
