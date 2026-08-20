<?php
 
defined('_JEXEC') or die;

$app = Secretary\Joomla::getApplication();

if ($app->isClient('site'))
{
	\Joomla\CMS\Session\Session::checkToken('get') or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_content/helpers/route.php';

// Include the component HTML helpers. 
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

$extension	= $this->escape($this->state->get('filter.extension'));
$function  	= $app->input->getCmd('function', 'jSelectCategory');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=folders&layout=modal&tmpl=component&function='.$function.'&'.\Joomla\CMS\Session\Session::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_LABEL'); ?>
				</label>
			</div>
			<div class="btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SEARCH'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SEARCH'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<span class="icon-search"></span><?php echo '&#160;' . \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<span class="icon-remove"></span><?php echo '&#160;' . \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters pull-left">
			<select name="filter_access" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<select name="filter_published" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<?php if ($this->state->get('filter.forcedLanguage'))
            {
                ?>
			<input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
			<input type="hidden" name="filter_language" value="<?php echo $this->escape($this->state->get('filter.language')); ?>" />
			<?php }
            else
            {
                ?>
			<select name="filter_language" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
            <?php } ?>
		</div>
	</fieldset>

	<table class="table table-striped" id="categoryList">
		<thead>
			<tr>
			<th>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
			</th>
			<th width="10%" class="nowrap hidden-phone">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" class="nowrap hidden-phone">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
			</th>
				<th width="1%" class="nowrap hidden-phone">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item)
            {
                ?>
				<?php if ($item->language && \Joomla\CMS\Language\Multilanguage::isEnabled())
				{
					$tag = strlen($item->language);
					
                    if ($tag == 5)
                    {
						$lang = substr($item->language, 0, 2);
                    }
					elseif ($tag == 6)
					{
						$lang = substr($item->language, 0, 3);
					}
					else
					{
						$lang = "";
					}
				}
				elseif (!\Joomla\CMS\Language\Multilanguage::isEnabled())
				{
					$lang = "";
				}
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1) ?>
						<a href="javascript:void()" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', null, '<?php echo $this->escape(ContentHelperRoute::getCategoryRoute($item->id, $item->language)); ?>', '<?php echo $this->escape($lang); ?>', null);">
							<?php echo $this->escape($item->title); ?>
						</a>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center nowrap">
						<?php if ($item->language == '*')
                        {
                            ?>
							<?php echo \Joomla\CMS\Language\Text::alt('JALL', 'language'); ?>
						<?php }
                        else
                        {
                            ?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : \Joomla\CMS\Language\Text::_('JUNDEFINED'); ?>
                        <?php }?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?></span>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="extension" value="<?php echo $extension; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>

</form>
