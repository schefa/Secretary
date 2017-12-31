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
  
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<div class="row-fluid secretary-contacts-modal">

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=documents&layout=modal&tmpl=component&excluded=' . JFactory::getApplication()->input->get('excluded', '', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="secretary-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_SECRETARY_SEARCH'); ?>" data-placement="bottom"/>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				<button type="button" class="btn" onclick="if (window.parent) window.parent.jSelectDocument('','','','','','');"><?php echo JText::_('COM_SECRETARY_NONE'); ?></button>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">

        <thead>
            <tr>
                <th class='center'>
                <?php echo JHtml::_('grid.sort','COM_SECRETARY_NR', 'a.nr', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_DATE', 'a.created', $listDirn, $listOrder) ." / ". JHtml::_('grid.sort','COM_SECRETARY_FOLDER', 'a.catid', $listDirn, $listOrder) ; ?>
                </th> 
                <th class='left'>
                <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SUBJECT', 'subject', $listDirn, $listOrder); ?>
                </th>
                <th class='right'>
                <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TOTAL', 'a.total', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>

            <tr class="row<?php echo $i % 2 ; ?>"> 
                
                <td class="center">
                    <?php echo (!empty($item->nr)) ? $item->nr :  " - "; ?>
                </td>
                
                <td class="left">
                    <a class="pointer" onclick="if (window.parent) window.parent.jSelectDocument('<?php echo $item->id; ?>','<?php echo Secretary\Utilities::cleaner($item->nr); ?>','<?php echo Secretary\Utilities::cleaner( $item->created. ' / '. $item->category_title ); ?>','<?php echo Secretary\Utilities::cleaner($item->subject); ?>','<?php echo Secretary\Utilities\Number::getNumberFormat($item->total,$item->currencySymbol); ?>');">
                    <?php echo $item->created. ' / '. $item->category_title; ?>
                    </a>
                </td>
                
                <td><?php echo $item->contact_name; ?></td>
                
                <td class="right">
                    <span class="total-amount"><?php echo Secretary\Utilities\Number::getNumberFormat($item->total,$item->currencySymbol); ?></span>
                </td>
                
            </tr>
            
		<?php endforeach;  ?>
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
	<?php echo JHtml::_('form.token'); ?>

</form>
</div>
