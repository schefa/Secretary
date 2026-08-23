<?php
 
defined('_JEXEC') or die;
 
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div class="secretary-main-container">

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=locations'); ?>" method="post" name="adminForm" id="adminForm">

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
                    <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=folders&extension=locations'); ?>"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES');?></a>
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
            
            	<?php foreach (\Secretary\Helpers\Locations::$options AS $area => $title)
            	{
                    ?>
                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=locations&extension='.$area); ?>" class="btn btn-large <?php if ($this->extension === $area)
                {
                    echo 'active';
                         } ?>"><?php echo \Joomla\CMS\Language\Text::_($title);?></a>
                <?php } ?>
            </div>
        </div>
      
        <hr />
         
		<div class="secretary-toolbar clearfix">
            <div class="select-arrow-bg select-arrow-toolbar">
                <div class="select-arrow">
                    <select id="filter_category_id" class="filter_category" onchange="this.form.submit()" name="filter_category_id">
                        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                </div>
            </div>
            <div class="select-arrow-toolbar-next">
                &#10095; 
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
                        <select onchange="this.form.submit()" name="filter_published">
                            <option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                        </select>
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
		
            <?php
            $gmaps = $this->state->params->get('gMapsLocations', 0);
        
            if (!empty($this->items[0]->location) && isset($gmaps) && ($gmaps == 1))
            {
                        echo Secretary\HTML::_('google.maps', $this->items, 'locations' );
            }
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
				<tr class="row<?php echo $i % 2; ?>">
                    
					<td class="center hidden-phone">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        <span class="lbl"></span>
					</td>
                    
                    <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    
                    <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
					<td><?php echo Secretary\HTML::_('status.state', $item, $i, 'locations', $item->canChange, $state ); ?></td>
                    
                    <td><a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ACL_SHOW'); ?>" title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_ACL_SHOW'); ?>"  href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=location&id='.(int) $item->id); ?>"><i class="fa fa-newspaper-o"></i></a></td>
                    
                    <td>
                    	<?php $extension = (!empty($this->extension)) ? "&extension=".$this->extension : ""; ?>
                        <?php 
                        if ($item->checked_out)
                        {
                            ?>
                            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'locations.', $item->canCheckin); ?>
                        <?php } ?>
                        <?php 
                        if ($item->canEdit)
                        {
                            ?>
                            <a class="hasTooltip" data-original-title="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="index.php?option=com_secretary&task=location.edit&id=<?php echo (int) $item->id . $extension; ?>">
                            <?php echo \Joomla\CMS\Language\Text::_($item->title); ?></a>
                        <?php }
                        else
                        {
                            ?>
                            <?php echo \Joomla\CMS\Language\Text::_($item->title); ?>
                        <?php } ?>
                    </td>
                    
					<td><?php echo $this->sectionIcons[$item->extension] .' '.\Joomla\CMS\Language\Text::_('COM_SECRETARY_'.strtoupper($item->extension)); ?></td>
					<td><?php echo $item->currency; ?></td>
					<td><?php echo $item->category; ?></td>
                    
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
