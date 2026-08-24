<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = \Joomla\CMS\Form\FormHelper::loadFieldType('SecretarySections', false)->getModulesArray();

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
            
            <div class="order nowrap center hidden-phone"><?php if ($this->canDo->get('core.edit'))
            {
                ?><a onclick="Joomla.submitbutton('items.saveOrder')"><i class="fa fa-save"></i></a><?php } ?>&nbsp;</div>
            
            </th>

            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_STATUS', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?> (<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_CLOSETASK', 'a.closeTask', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>)
            </th>
            <th width="1%" class='left'>
            <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PREVIEW'); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_DESCRIPTION', 'a.desc', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_SECTION', 'a.extension', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
        
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item)
    {
        ?>
        <tr class="row<?php echo $i % 2; ?> secretary-sort-row">
            
            <td class="center hidden-phone">
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
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
                <?php if ($canCheckin)
                {
                    ?>
                    <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=item&layout=edit&id='.(int) $item->id .'&extension='.$this->extension.'&module='.$item->extension); ?>">
                    <?php echo \Joomla\CMS\Language\Text::_($item->title); ?></a>
                <?php }
                else
                {
                    ?>
                    <?php echo \Joomla\CMS\Language\Text::_($item->title); ?>
                <?php } ?>
            &#8594; 
            	<?php echo  \Joomla\CMS\Language\Text::_(Secretary\Database::getQuery('status',$item->closeTask,'id','title','loadResult')); ?>
            </td>
            
            <td>
            <?php $state = array('title' => $item->title,'class' => $item->class,'description' => $item->description,'icon' => $item->icon ); ?>
            <?php echo Secretary\HTML::_('status.state', $item, $i, $item->extension , false, $state ); ?>
            </td>
            <td><div class="secretary-status-tooltip-preview-triagle"></div><div class="secretary-status-tooltip-preview"><?php echo \Joomla\CMS\Language\Text::_($item->description); ?></div></td>
            <td><?php 
            if ( $item->extension !='root')
            {
                echo $modules[$item->extension];
            } ?></td>
            
        </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-list-pagination">
        <?php 
        if (isset($this->items[0]))
        {
            $colspan = count(get_object_vars($this->items[0]) ?? []);
        }
        else
        {
            $colspan = 10;
        }
        ?>
    <tr>
        <td colspan="<?php echo $colspan ?>">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY');?></option><?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->getSortFieldsStatus(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>

<input type="hidden" name="module" value="<?php echo $this->module; ?>" />