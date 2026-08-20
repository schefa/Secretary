<?php
 
defined('_JEXEC') or die; 

$input		= Secretary\Joomla::getApplication()->input;
$email		= $input->getInt('email');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<div class="row-fluid secretary-contacts-modal">

<?php if (!empty($email))
{
    ?>
	<div class="alert alert-message"><?php echo \Joomla\CMS\Language\Text::_('Kontakte mit Email') ?></div>
<?php } ?>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=subjects&layout=modaljusers&tmpl=component&excluded=' . $input->get('excluded', '', 'BASE64'));?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="secretary-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('COM_USERS_SEARCH_IN_NAME'); ?>" data-placement="bottom"/>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo \Joomla\CMS\HTML\HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				<button type="button" class="btn" onclick="if (window.parent) window.parent.jSelectJUser('', '', '');"><?php echo \Joomla\CMS\Language\Text::_('JOPTION_NO_USER'); ?></button>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="left">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" >
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'COM_SECRETARY_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_USERGROUPS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item)
        {
            ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.jSelectJUser('<?php echo addslashes($item->name); ?>', '<?php echo $item->username; ?>','<?php echo $item->id; ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo $item->group_title; ?>
				</td>
			</tr>
		<?php } ?>
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
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
	</div>
</form>
</div>
