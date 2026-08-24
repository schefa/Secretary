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
            
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_FIELD_TYPE', 'a.type', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
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
            
            <td>
                <?php if ($canCheckin)
                {
                    ?>
                    <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&task=item.edit&id='.(int) $item->id .'&extension='.$this->extension ); ?>">
                    <?php echo \Joomla\CMS\Language\Text::_($item->title); ?></a>
                <?php }
                else
                {
                    ?>
                    <?php echo \Joomla\CMS\Language\Text::_($item->title); ?>
                <?php } ?>
            </td>
            
            <td><?php echo $item->type; ?></td>
            <td><?php echo $modules[$item->extension]; ?></td>
            
        </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-list-pagination">
    <tr>
        <td colspan="5">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY');?></option><?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->getSortFieldsUploads(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>
