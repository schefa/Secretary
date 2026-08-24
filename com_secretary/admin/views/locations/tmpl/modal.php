<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 

$input     = Secretary\Joomla::getApplication()->input; 
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<div class="row-fluid secretary-contacts-modal">

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=locations&layout=modal&tmpl=component&extension='.$input->getCmd('extension').'&excluded=' . $input->get('excluded', '', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="secretary-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('COM_SECRETARY_SEARCH'); ?>" data-placement="bottom"/>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				<button type="button" class="btn" onclick="if (window.parent) window.parent.jSelectLocation('','','','','','');"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NONE'); ?></button>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">

        <thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone">
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_NAME', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                	<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CURRENCY'); ?>
                </th>
                <th class='left'>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort','COM_SECRETARY_CATEGORY','category', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        
		<tbody>
		<?php foreach ($this->items as $i => $item)
        {
            ?>

            <tr class="row<?php echo $i % 2 ; ?>">
            
                <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                
                <td>
                    <?php $extension = (!empty($this->extension)) ? "&extension=".$this->extension : ""; ?>
                    
                    <a class="pointer" onclick="if (window.parent) window.parent.jSelectLocation('<?php echo $item->id; ?>','<?php echo Secretary\Utilities::cleaner(\Joomla\CMS\Language\Text::_($item->title)); ?>','<?php echo Secretary\Utilities::cleaner( $item->extension ); ?>','<?php echo Secretary\Utilities::cleaner($item->category); ?>');"><?php echo \Joomla\CMS\Language\Text::_($item->title); ?></a>
                
                </td>
                
                <td><?php echo $this->sectionIcons[$item->extension].' '.\Joomla\CMS\Language\Text::_('COM_SECRETARY_'.strtoupper($item->extension)); ?></td>
                <td><?php echo $item->currency; ?></td>
                <td><?php echo $item->category; ?></td>
                
            </tr>
            
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>

</form>
</div>
