<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

$user		= \Secretary\Joomla::getUser();
$endDate	= strtotime( date('Y-m-d') );
$category	= (!empty($this->categoryId)) ? '&catid='.$this->categoryId : '';

?>
<div class="secretary-modal-top">
    <button class="modal-close" data-dismiss="modal" type="button">x</button>
    <h3><?php echo JText::_('COM_SECRETARY_REPETITIONS'); ?></h3>
</div>

<?php if(!empty($this->itemsRepeat)) { ?>
<form action="<?php echo JRoute::_('index.php?option=com_secretary&layout=repetition&view=documents'.$category); ?>" method="post" name="adminForm" id="adminForm">
    
    <div id="section-to-print" class="secretary-modal-content"> 
    <table class="table">
        <tr>
            <td></td>
            <td></td>
            <td><?php echo JText::_('COM_SECRETARY_NR'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_DATE'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_FOLDER'); ?></td>
            <td width="20%"><?php echo JText::_('COM_SECRETARY_TITLE'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_SUBJECT'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_TOTAL'); ?></td>
        </tr>
        
        <?php foreach($this->itemsRepeat AS $i => $item) { ?>
        <tr>
        
            <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->repetition_id); ?><span class="lbl"></span></td>
            <td>
            <?php 
            $item->category_title = (empty($item->category_title)) ? JText::_('COM_SECRETARY_DOCUMENTS') : JText::_($item->category_title);
            
            $totalReps = round( ($item->endTime - $item->startTime) / $item->intervall );
            
            // Count
            $willDone = 0;
            $endDate = ($item->endTime < $endDate) ? $item->endTime : $endDate;
            while($item->nextTime <= $endDate) {
                $item->nextTime = strtotime($item->int_in_words, $item->nextTime);
                $willDone++;
            }
            
            echo JText::sprintf('COM_SECRETARY_REPETITION_WILL_BE_CREATED', $willDone, $totalReps);
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
        <?php if($user->authorise('core.create','com_secretary.document') && $user->authorise('core.create','com_secretary.time') ) { ?>
        <div data-value="documents.updateRepetitions" class="btn btn-submittask btn-default">
        	<i class="fa fa-file-o"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_CREATE');?>
        </div>
        <?php } ?>
        <?php if($user->authorise('core.delete','com_secretary.time') ) { ?>
        <div data-value="documents.deleteRepetitions" class="btn btn-submittask btn-default">
        	<i class="fa fa-trash"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_DELETE');?>
        </div>
        <?php } ?>
        <div class="btn modal-close" ><?php echo JText::_('COM_SECRETARY_TOOLBAR_CLOSE'); ?></div>
    </div>

    <input type="hidden" id="form-task" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" />
    <?php echo JHtml::_('form.token'); ?>

</form>

<?php } ?>