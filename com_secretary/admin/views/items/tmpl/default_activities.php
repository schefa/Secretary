<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage', 'com_secretary');

?>

<table class="table table-hover" id="entriesList">
    <thead>
        <tr>
        
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            
            <?php if (isset($this->items[0]->id))
            {
                ?>
            <th width="1%" class="nowrap center hidden-phone">
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <?php } ?>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_ENTITY_SHORT', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_ENTITY_LONG', 'a.desc', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
        
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item)
    {
        ?>
        <tr class="row<?php echo $i % 2; ?>">
            
            <td class="center hidden-phone">
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                <span class="lbl"></span>
            </td>
            
            <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
            
            <td>
                <?php if ($canCheckin)
                {
                    ?>
                    <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&layout=edit&id='.(int) $item->id .'&extension='.$this->extension ); ?>">
                    <?php echo $item->title; ?></a>
                <?php }
                else
                {
                    ?>
                    <?php echo $item->title; ?>
                <?php } ?>
            </td>
            
            <td><?php echo $item->description; ?></td>
            
        </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-list-pagination">
    <tr>
        <td colspan="4">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY');?></option><?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->getSortFieldsEntities(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>
