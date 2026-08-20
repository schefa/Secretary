<?php
 
defined('_JEXEC') or die;

$user = Secretary\Joomla::getUser();
$foldersLink = $user->authorise('core.show','com_secretary.folder');

if ( !extension_loaded ( "Calendar" ))
{
    throw new Exception('PHP Calendar is not enabled. Installation required. http://php.net/manual/en/book.calendar.php');
    die;
}
?>

<div class="secretary-times secretary-main-container">

<form action="<?php echo Secretary\Route::create('times', array('section' => $this->section)); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
    
	<div class="secretary-main-area">
		
    <?php if ($this->canDo->get('core.show'))
    {
        ?>
		<div class="fullwidth">
			<div class="pull-left">
        		<h2 class="documents-title">
                    <span class="documents-title-first"><?php echo $this->title; ?></span>
                    <span class="documents-title-second">
                    <?php if ($foldersLink)
                    {
                        ?>
                    <a href="<?php echo Secretary\Route::create('folders', array('extension' => 'times')); ?>">
                    <?php } ?>
                    <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORIES');?>
                    <?php 
                    if ($foldersLink)
                    {
                        ?></a><?php } ?>
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
            
		<div class="fullwidth times-filter">
        
            <div class="pull-left">
                <div class="btn-group">
                    <a href="<?php echo Secretary\Route::create('times', array('section' => 'list')); ?>" class="btn <?php if ($this->section == 'list')
                    {
                        echo 'active';
                             }?>">
                        <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_LIST'); ?>
                    </a>
                </div>
                <div class="btn-group">
                	<?php echo \Secretary\Helpers\Times::getTimesToolbar($this->section); ?>
                </div>
                <div class="toolbar-view-text">
                	<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_VIEW'); ?>
                </div>
            </div>
            
			<div class="pull-right select-arrow-control">
            	<span class="select-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_WEEK');?></span>
				<div class="select-arrow select-arrow-white select-small">
				<select name="filter_week" id="filter_week" class="form-control" onchange="this.form.submit()">
                    <?php $week = \Secretary\Helpers\Times::getIsoWeeksInYear($this->year);?>
                    <option value="0"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION');?></option>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $week, 'value', 'text', $this->week);?>
				</select>
				</div>
			</div>
			<div class="pull-right select-arrow-control margin-right">
            	<span class="select-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_MONTH');?></span>
				<div class="select-arrow select-arrow-white select-md">
				<select name="filter_month" id="filter_month" class="form-control" onchange="this.form.submit()">
                    <?php $month = \Secretary\Helpers\Times::getMonthname(); ?>
                    <option value="0"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION');?></option>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $month, 'value', 'text', $this->month);?>
				</select>
				</div>
			</div>
			<div class="pull-right select-arrow-control margin-right">
            	<span class="select-label"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DAY');?></span>
				<div class="select-arrow select-arrow-white select-small">
				<select name="filter_day" id="filter_day" class="form-control" onchange="this.form.submit()">
                    <option value="0"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SELECT_OPTION');?></option>
                    <?php $days = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year); for ($i = 1; $i <= $days; $i++)
                    {
                        ?>
                    <option value="<?php echo $i ?>" <?php if ($i == $this->day)
                    {
                        echo 'selected="selected"';
                                   }?>><?php echo $i;?></option>
                    <?php } ?>
				</select>
				</div>
			</div>
			<div class="pull-right">
                <div class="toolbar-view-text">
                	<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FILTER'); ?>
                </div>
            </div>
		</div>
        
		<div class="fullwidth times-toolbar">
            <div class="pull-left secretary-toolbar">
                <div class="select-arrow-bg select-arrow-toolbar">
                    <div class="select-arrow">
                    <select id="filter_category" class="filter_category" onchange="this.form.submit()" name="filter_category">
                        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                    </div>
                </div>
                <?php if (true === $user->authorise('core.create','com_secretary.time'))
                {
                    ?>
                <div class="select-arrow-toolbar-next">
                    &#10095; 
                </div>
                <div class="secretary-control-group pull-left margin-right">
                    <div class="pull-right select-arrow-control">
                        <span class="select-label" id="add_document"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NEW'); ?></span>
                        <div class="select-arrow select-arrow-white select-md">
                            <select id="add_new_document" class="form-control inputbox">
                            	<option value="events"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EVENT'); ?></option>
                                <optgroup label="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PROJECTS'); ?>">
                                    <option value="projects"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PROJECT'); ?></option>
                                    <?php 
                                    if ($this->projectExists > 0)
                                    {
                                        ?><option value="tasks"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TASK'); ?></option><?php } ?>
                                </optgroup>
                                <optgroup label="<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_OCCUPANCY'); ?>">
                                    <option value="locations"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION'); ?></option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php $this->addToolbar();?>
			</div>
            <?php if ($this->section == 'list')
            {
                $this->addToolbarList();
            }?>
		</div>    
            
		<?php
        switch ($this->section)
			{
            case 'year' : echo $this->loadTemplate('year'); break;
            case 'month' : echo $this->loadTemplate('month'); break;
            case 'week' : echo $this->loadTemplate('week'); break;
            case 'day' : case 'list' : default : echo $this->loadTemplate('list'); break;
        }
        ?>

		<input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        
        
	<?php }
    else
    {
        ?>
        <div class="alert alert-danger"><?php echo \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?>
    
        <?php if (!empty($this->checkRep))
        {
            ?>
        <hr class="secretary-main-area-right-hr" />
        <div class="row-fluid">
            <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPETITIONS'); ?><a href="<?php echo Secretary\Route::create(false, array( 'task'=>'times.updateRepetitions', 'catid'=>$this->categoryId ) );?>" class="btn headline-sidebar-button"><i class="fa fa-refresh"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPETITION_UPDATE');?></a></h3>
            <div class="documents-sidebar-repetitions">
               <?php echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_REPETITION_WAIT_FOR_CREATION', count($this->checkRep ?? [])); ?>
               <a data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo Secretary\Route::create('times', array('layout'=>'repetition','tmpl'=>'component')); ?>"}' ><i class="fa fa-share-square-o"></i> <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_MORE_INFORMATION'); ?></a>
            </div>
        </div>
		<?php } ?>
        
	</div>

    <?php echo $this->loadTemplate('batch'); ?>
    
</form>
</div>
