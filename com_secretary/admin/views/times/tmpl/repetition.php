<?php
 
defined('_JEXEC') or die;

$endDate	= strtotime( date('Y-m-d') );
?>
<div class="secretary-modal-top">
    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPETITIONS'); ?></h3>
</div>

<?php if (!empty($this->checkRep))
{
    ?>
<div id="section-to-print" class="secretary-modal-content"> 
<table class="table">
    <tr>
    	<td></td>
    	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'); ?></td>
    	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_STARTDATE');?></td>
        <td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_ENDDATE'); ?></td>
    	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORY'); ?></td>
    	<td><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION'); ?></td>
    </tr>
	
    <?php foreach ($this->checkRep AS $result)
    {
        ?>
    <tr>
    
    	<td>
		<?php 
		$totalReps = round( ($result->endTime - $result->startTime) / $result->intervall );
		
		// Zählen
		$willDone = 0;
		$endDate = ($result->endTime < $endDate) ? $result->endTime : $endDate;
		
        while ($result->nextTime <= $endDate)
        {
			$result->nextTime = strtotime($result->int_in_words, $result->nextTime);
			$willDone++;
        }
		
		echo \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_REPETITION_WILL_BE_CREATED', $willDone, $totalReps);
		?>
        </td>
		<td><?php echo $result->title; ?></td>
		<td><?php echo $result->startDate; ?></td>
		<td><?php echo $result->endDate; ?></td>
		<td><?php echo $result->category_title; ?></td>
		<td><?php echo $result->location_id; ?></td>
        
     </tr>
    <?php } ?>
</table>
</div>

<div class="secretary-modal-bottom">
	<a href="<?php echo Secretary\Route::create(false, array( 'task'=>'times.updateRepetitions', 'catid'=>$this->categoryId ) );?>" class="btn btn-default"><i class="fa fa-refresh"></i>&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_REPETITION_UPDATE');?></a>
</div>

<?php } ?>