<?php
 
defined('_JEXEC') or die;

$user		= \Secretary\Joomla::getUser();
$endDate	= strtotime( date('Y-m-d') );
$category	= (!empty($this->categoryId)) ? '&catid='.$this->categoryId : '';

?>
<div class="secretary-modal-top">
    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPETITIONS'); ?></h3>
</div>

<?php if (!empty($this->itemsRepeat))
{
    ?>
<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&layout=repetition&view=documents&tmpl=component'.$category); ?>" method="post" name="adminForm" id="adminForm">
    
    <div id="section-to-print" class="secretary-modal-content"> 
    <table class="table">
        <tr>
            <td><?php echo Secretary\HTML::_('status.checkall'); ?></td>
            <td></td>
            <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NR'); ?></td>
            <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DATE'); ?></td>
            <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FOLDER'); ?></td>
            <td width="20%"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'); ?></td>
            <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECT'); ?></td>
            <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOTAL'); ?></td>
        </tr>
        
        <?php foreach ($this->itemsRepeat AS $i => $item)
        {
            ?>
        <tr>
        
            <td class="center hidden-phone"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->repetition_id); ?><span class="lbl"></span></td>
            <td>
            <?php 
            $item->category_title = (empty($item->category_title)) ? \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS') : \Joomla\CMS\Language\Text::_($item->category_title);
            
            $totalReps = round( ($item->endTime - $item->startTime) / $item->intervall );
            
            // Count
            $willDone = 0;
            $endDate = ($item->endTime < $endDate) ? $item->endTime : $endDate;
            
            while ($item->nextTime <= $endDate)
            {
                $item->nextTime = strtotime($item->int_in_words, $item->nextTime);
                $willDone++;
            }
            
            echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_REPETITION_WILL_BE_CREATED', $willDone, $totalReps);
            ?>
            </td>
    
            <td><?php echo $item->nr; ?></td>
            <td><?php echo $item->created; ?></td>
            <td><?php echo $item->category_title; ?></td>
            <td><?php echo $item->title; ?></td>
            <td><?php echo $item->contact; ?></td>
            <td><?php echo Secretary\Utilities\Number::getNumberFormat($item->total,$item->currencySymbol) ; ?></td>
            
         </tr>
        <?php } ?>
    </table>
    </div>
    
    <div class="secretary-modal-bottom">
        <?php if ($user->authorise('core.create','com_secretary.document') && $user->authorise('core.create','com_secretary.time') )
        {
            ?>
        <div data-value="documents.updateRepetitions" class="btn btn-submittask btn-default">
        	<i class="fa fa-file-o"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CREATE');?>
        </div>
        <?php } ?>
        <?php 
        if ($user->authorise('core.delete','com_secretary.time') )
        {
            ?>
        <div data-value="documents.deleteRepetitions" class="btn btn-submittask btn-default">
        	<i class="fa fa-trash"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DELETE');?>
        </div>
        <?php } ?>
    </div>

    <input type="hidden" id="form-task" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" />
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>

</form>

<?php } ?>