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

$input		= Secretary\Joomla::getApplication()->input;
$email		= $input->getInt('email');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<div class="row-fluid secretary-contacts-modal">

<?php if(!empty($email)) { ?>
	<div class="alert alert-message"><?php echo JText::_('Kontakte mit Email') ?></div>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=subjects&layout=modal&tmpl=component&excluded=' . $input->get('excluded', '', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="secretary-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_USERS_SEARCH_IN_NAME'); ?>" data-placement="bottom"/>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				<button type="button" class="btn" onclick="if (window.parent) window.parent.jSelectUser('', '', '', '', '', '', '', '');"><?php echo JText::_('JOPTION_NO_USER'); ?></button>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_SECRETARY_NAME', 'a.lastname', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" >
					<?php echo JText::_('COM_SECRETARY_STREET'); ?>
				</th>
				<th class="nowrap">
					<?php echo JText::_('COM_SECRETARY_LOCATION'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) : 
				$connections = \Secretary\Helpers\Connections::getConnectionsSubjectData($item->id); ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.jSelectUser('<?php echo addslashes($item->fullname); ?>', '<?php echo $item->street; ?>', '<?php echo $item->zip; ?>', '<?php echo $item->location; ?>', '<?php echo $item->phone; ?>', '<?php echo $item->email; ?>', '<?php echo $item->gender; ?>', '<?php echo $item->id; ?>', '<?php echo $this->escape(json_encode($connections)); ?>');">
						<?php echo $item->lastname; ?></a>
                        <?php if(!empty($item->firstname)) echo ', '. $item->firstname; ?>
				</td>
				<td align="center">
					<?php echo $item->street; ?>
				</td>
				<td align="left">
					<?php echo $item->zip.' '.$item->location; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>
