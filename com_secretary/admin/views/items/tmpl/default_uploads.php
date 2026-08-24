<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = \Joomla\CMS\Form\FormHelper::loadFieldType('SecretarySections', false)->getIcons();

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage', 'com_secretary');

?>

<?php if (empty($this->items))
{
    ?>
    <div class="alert alert-no-items">
        <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
    </div>
<?php }
else
{
    ?>
<table class="table table-hover">
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
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_ENTRY', 'a.itemID', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_BUSINESS', 'a.business', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'></th>
        
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
            
            <?php
            if ($item->extension == 'system')
				{
                $item->extension = 'documents';
            }
				$ext = Secretary\Application::getSingularSection($item->extension);
				
            if ($ext === null)
            {
					// Legacy/orphaned extension value (e.g. "messages") with no dedicated
					// section view - plain, unlinked text instead of a broken "view=&id=..."
					// link or an unresolved "COM_SECRETARY_" translation key.
					$link = '';
					$title = $item->extension . (($item->itemID > 0) ? ' (' . $item->itemID . ')' : '');
            }
            elseif ($item->itemID > 0)
            {
					$link = \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view='.$ext.'&id='.$item->itemID);
					$title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_'.strtoupper($ext)) .' ('. $item->itemID.')';
            }
            else
            {
					$link = \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&task='.$ext.'.edit&secf='.$item->id) .'" class="btn btn-default';
					$addEntryText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_'.strtoupper($ext));
					$title = \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $addEntryText);
            }
			?>
            <td>
            	<?php if ($link !== '')
                {
                    ?>
            	<a href="<?php echo $link; ?>"><?php echo $title; ?></a>
            	<?php }
                else
                {
                    ?>
                    <?php echo $title; ?>
                <?php } ?>
            </td>
            
            <td><?php echo $modules[$item->extension] ?? ''; ?></td>
            <td><?php echo Secretary\Database::getQuery('businesses',$item->business,'id','title','loadResult'); ?></td>
            
            <td><?php \Secretary\Helpers\Uploads::getUploadFile($item, '', 40); ?></td>
            
        </tr>
        <?php } ?>
    </tbody>
    <tfoot class="table-list-pagination">
    <tr>
        <td colspan="7">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY');?></option><?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->getSortFieldsUploads(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>
<?php } ?>


<?php if (!empty($this->extraItems))
{
    ?>
<div style="height: 20px;"></div>
<div class="pull-left secretary-toolbar">
    <div class="select-arrow-toolbar">
    <h2 class="documents-title">
    	<span class="documents-title-first"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FILES_AUTOMATIC_GENERATED'); ?></span>
    </h2>
    </div>
    <div class="select-arrow-toolbar-next">
        &#10095; 
    </div>
	<?php echo Secretary\Navigation::ToolbarItem('items.deleteFiles', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash'); ?>
</div>

<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            <th><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE') ?></th>
    	</tr>
	</thead>
	<tbody>
    <?php foreach ($this->extraItems as $i => $item)
    {
        ?>
    <tr>
        <td class="center hidden-phone">
            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->file); ?>
            <span class="lbl"></span>
        </td>
    	<td>
    	<?php if (COM_SECRETARY_PDF)
    	{
            ?>
            <?php $href = (isset($item->document->id)) ? Secretary\Route::create('document', array('id' => $item->document->id, 'format' => 'pdf')) : ''; ?>
        <a class="hasTooltip printpdf" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo $href; ?>"}' data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PDF_PREVIEW') ; ?>"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
        <?php } ?><?php echo $item->title; ?></td>
    </tr>
	<?php } ?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="2">
		<?php
		$extraBase  = 'index.php?option=com_secretary&view=items&extension=uploads';
		$extraFrom  = $this->extraTotal ? $this->extraStart + 1 : 0;
		$extraTo    = min($this->extraStart + $this->extraLimit, $this->extraTotal);
		$extraPrev  = max(0, $this->extraStart - $this->extraLimit);
		$extraNext  = $this->extraStart + $this->extraLimit;
		?>
		<div class="pagination pagination-toolbar text-center mt-0 pull-left">
            <ul class="pagination ms-auto me-0">
			<?php if ($this->extraStart > 0)
            {
                ?>
				<li class="page-item"><a class="page-link" href="<?php echo \Joomla\CMS\Router\Route::_($extraBase . '&filestart=0&filelimit=' . $this->extraLimit); ?>">&laquo;</a></li>
				<li class="page-item"><a class="page-link" href="<?php echo \Joomla\CMS\Router\Route::_($extraBase . '&filestart=' . $extraPrev . '&filelimit=' . $this->extraLimit); ?>">&lsaquo;</a></li>
			<?php } ?>
			    <li class="page-item"><span class="page-link"><?php echo $extraFrom . '-' . $extraTo . ' / ' . $this->extraTotal; ?></span></li>
			<?php 
            if ($extraNext < $this->extraTotal)
            {
                ?>
				<li class="page-item"><a class="page-link" href="<?php echo \Joomla\CMS\Router\Route::_($extraBase . '&filestart=' . $extraNext . '&filelimit=' . $this->extraLimit); ?>">&rsaquo;</a></li>
			<?php } ?>
            </ul>
		</div>
		<div class="pull-right limit-box clearfix">
			<span class="pagination-filter-text"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LIMIT'); ?></span>
			<select onchange="location.href='<?php echo \Joomla\CMS\Router\Route::_($extraBase); ?>&filelimit='+this.value+'&filestart=0';">
				<?php 
                foreach (array(10, 25, 50, 100) as $opt)
                {
                    ?>
				<option value="<?php echo $opt; ?>" <?php echo ($opt == $this->extraLimit) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
				<?php } ?>
			</select>
		</div>
		</td>
	</tr>
	</tfoot>
</table>
<?php } ?>
