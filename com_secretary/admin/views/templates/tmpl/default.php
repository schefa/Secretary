<?php
 
defined('_JEXEC') or die;

$user		= \Secretary\Joomla::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$business	= Secretary\Application::company();

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = \Joomla\CMS\Form\FormHelper::loadFieldType('SecretarySections', false)->getIcons();

$areas = array('documents','subjects','products');
?>

<div class="secretary-main-container">
<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=templates'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
    
    <?php if ($this->canDo->get('core.show'))
    {
        ?>
	<div class="secretary-main-area">
    
		<div class="row-fluid fullwidth">
			<div class="pull-left">
        		<h2 class="documents-title">
                    <span class="documents-title-first"><?php echo $this->title; ?></span>
                    <span class="documents-title-second">
                    	<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=folders&extension=templates'); ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES');?></a>
                    </span>
                </h2>
			</div>
            
			<div class="pull-right">
            <div class="secretary-search btn-group">
                <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER'); ?>" />
                <button class="btn btn-default hasTooltip" type="submit" title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
                <button class="btn btn-default hasTooltip" type="button" title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
            </div>
            </div>
        </div>
        
        <hr />
        <div class="btn-toolbar">
            <div class="btn-group">
            
            	<?php foreach ($areas AS $area )
            	{
                    ?>
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=templates&extension='.$area); ?>" class="btn btn-large <?php if ($this->extension === $area)
                {
                    echo 'active';
                         } ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_'.$area);?></a>
                <?php } ?>
            </div>
        </div>
      
        <hr />
         
        
		<div class="secretary-toolbar clearfix">
            <div class="select-arrow-bg select-arrow-toolbar">
                <div class="select-arrow">
                    <select id="select_category">
                        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                </div>
            </div>
			<?php $this->addToolbar(); ?>
            <div class="pull-right">
                <div class="btn-group">
                	<div class="select-small select-arrow select-arrow-white">
                    <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                        <select id="filter_published" onchange="this.form.submit()" name="filter_published">
                            <option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                        </select>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                    <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY');?></option><?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?></select>
                    </div>
                </div>
            </div>
		</div>
        
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
        
		<table class="table table-hover" id="documentsList">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
                    	<?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
                    </th>
                    <th class='left'>
                    	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_NAME', 'a.title', $listDirn, $listOrder); ?>
					</th>
                    <th class='left'>
                    	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_DESCRIPTION', 'a.text', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                    	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                    	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort','COM_SECRETARY_CATEGORY', 'category', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                    	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  '<i class="fa fa-globe"></i>', 'a.language', $listDirn, $listOrder); ?>
                    </th>
				</tr>
			</thead>
			<tbody>
			<?php
				$cntBusinessTemplate = true;
			
            foreach ($this->items as $i => $item)
            {
                $canChange = false; $canCheckin = false; $canEdit = false;
					
                if ($user->authorise('core.edit', 'com_secretary.template'))
                {
						$canEdit = true; $canChange	= true; $canCheckin = true;
                }
                
                if (!$canCheckin)
                {
                    $canCheckin = $user->authorise('core.admin', 'com_secretary');
                }
                
                if (!$canChange)
                {
                    $canChange = $user->authorise('core.edit.state', 'com_secretary.template');
                }
					
                if ($business['id'] != $item->business)
                {
                    $businessTemplate = 'no-business-template';
                    $cntBusinessTemplate = false;
                }
                else
                {
						$businessTemplate = '';
                }
				?>
				<tr class="row<?php echo $i % 2 . ' '. $businessTemplate; ?>">
                    
					<td class="center hidden-phone">
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        <span class="lbl"></span>
					</td>
                    
                    <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    
                <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
					<td><?php echo Secretary\HTML::_('status.state', $item, $i, 'templates', $canChange, $state ); ?></td>
                    
                    <td width="1%">
                    <?php if (COM_SECRETARY_PDF)
                    {
                        ?>
                            <?php $href = Secretary\Route::create('template', array('id' => $item->id, 'format' => 'pdf')); ?>
                            <a class="hasTooltip printpdf" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo $href; ?>"}' data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PDF_PREVIEW') ; ?>"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                        <?php } ?>
                    </td>
                    <td>
                    <?php $extension = (!empty($item->extension)) ? "&extension=".$item->extension : ""; ?>
                    <?php 
                    if ($item->checked_out)
                    {
                        ?>
                            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'templates.', $canCheckin); ?>
                        <?php } ?>
                    <?php 
                    if ($canEdit)
                    {
                        ?>
                            <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="index.php?option=com_secretary&task=template.edit&id=<?php echo (int) $item->id . $extension; ?>">
                            <?php echo \Joomla\CMS\Language\Text::_($item->title); ?></a>
                        <?php }
                    else
                    {
                        ?>
                            <?php echo \Joomla\CMS\Language\Text::_($item->title); ?>
                    <?php } ?>
                    </td>
                    
                    <td><?php echo substr(htmlentities( $item->text), 0, 150); ?>…</td>
                    
					<td>
                    <?php echo $modules[$item->extension]; ?>
                    </td>
                    
					<td><?php echo $item->category; ?></td>
					<td><img src="<?php echo \Joomla\CMS\Uri\Uri::root(). '/media/mod_languages/images/'. substr($item->language, 0, 2) .'.gif'; ?>" /></td>
                    
				</tr>
            <?php } ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="8">
                            <?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
        
                    <?php if (!$cntBusinessTemplate)
                    {
                        ?>
    	<div class="fullwidth template-not-assigned">
            <span class="msg no-business-template"></span>
            <span><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE_NOT_FOR_BUSINESS'); ?></span>
        </div>
                    <?php } ?>

        <?php }?>

	</div>
    
	<?php }
    else
    {
        ?>
        <div class="alert alert-danger"><?php echo \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?>  
    
	<?php echo $this->loadTemplate('batch'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" />
    <input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>

    </div>
