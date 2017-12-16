<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\HTML;

require_once JPATH_ADMINISTRATOR .'/components/com_secretary/application/HTML.php';

use JHtml;
use JRoute;
use JText;

defined('_JEXEC') or die;

class Times
{
    
    /**
     * Displays a month view
     * 
     * @param number $month
     * @param number $year
     * @return string
     */
    public static function monthTable( $month, $year, $setitems = false)
    {
        $model = \Secretary\Model::create('Times');
        $items = $model->getMonthItems( $month, $year, $setitems );
        
        $calendar = '<table class="calendar">';
        $calendar.= '<tr class="calendar-row"><td class="calendar-day-head" colspan="7"><span>'. JText::_( \Secretary\Helpers\Times::getMonthname($month) ).'</span></td></tr>';
        
        // Headings
        $headings = array();
        for($a = 0; $a < 7; $a++) {
            $headings[] = date('D', strtotime("Sunday +{$a} days"));
        }
        
        $calendar.= '<tr class="calendar-row"><td class="calendar-day-head-col"><span>'.implode('</td><td class="calendar-day-head-col">',$headings).'</span></td></tr>';
        
        // row for week one
        $calendar.= '<tr class="calendar-row">';
        
        // print "blank" days until the first of the current week
        for($x = 0; $x < $items['running_day']; $x++):
        $calendar.= '<td class="calendar-day-np"><span></span></td>';
        $items['days_in_this_week']++;
        endfor;
        
        // keep going with days....
        for($list_day = 1; $list_day <= $items['days_in_month']; $list_day++):
        $calendar.= '<td class="calendar-day">';
        
        // add in the day number 
        $calendar.= '<div class="day-number">';
        $calendar.= '<a href="'. JRoute::_('index.php?option=com_secretary&amp;view=times&amp;section=day&amp;month='.$month.'&amp;day='.$list_day) .'">'.$list_day.'</a>';
        $calendar.= '</div>';
        
        $calendar.= '</td>';
        if($items['running_day'] == 6):
        $calendar.= '</tr>';
        if(($items['day_counter']+1) != $items['days_in_month']):
        $calendar.= '<tr class="calendar-row">';
        endif;
        $items['running_day'] = -1;
        $items['days_in_this_week'] = 0;
        endif;
        $items['days_in_this_week']++; $items['running_day']++; $items['day_counter']++;
        endfor;
        
        // finish the rest of the days in the week
        //if($items['days_in_this_week'] < 8):
        //	for($x = 1; $x <= (8 - $items['days_in_this_week']); $x++):
        //	$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
        //	endfor;
        //endif;
        
        // final row
        $calendar.= '</tr>';
        
        // end the table
        $calendar.= '</table>';
        
        return $calendar;
    }
    
	
	public static function listViewProject($i , $item, $vorspannSecs, $intervall)
	{
		?>
		
            <div class="secretary-row-main secretary-row-inner clearfix">
                
                <div class="checkbox center hidden-phone" 
                data-extension="<?php echo $item->extension; ?>">
                <?php // echo JHtml::_('grid.id', $i, $item->time_id); ?>
                <input id="cb<?php echo $item->time_id ?>" name="cid[]" value="<?php echo $item->time_id ?>" onclick="Joomla.isChecked(this.checked);" type="checkbox"><span class="lbl"></span>
                </div>
                
                <div class="order nowrap center hidden-phone">
                	<div class="secretary-sort">
                    	<span class="move-up"><i class="fa fa-caret-up"></i></span>
                    	<span class="move-down"><i class="fa fa-caret-down"></i></span>
                	</div>
                    <input type="hidden" name="order[<?php echo $item->time_id;?>]" />
                </div>
                   
                <div class="progress-td">&nbsp;</div>
                   
                <div class="title">
                    <div class="secretary-time-title">
                    	<a href="<?php echo JRoute::_('index.php?option=com_secretary&view=time&extension='.$item->extension.'&id='.(int) $item->time_id); ?>"><?php echo $item->time_title; ?></a>
                    </div>
                    
                    <div class="time-btn-group-white clearfix">
                        <div class="btn-group-col"><?php echo JHtml::date( $item->startDate , 'd.m.Y'); ?></div>
                        <div class="btn-group-col"><?php echo JHtml::date( $item->startDate , 'h:i'); ?></div>
                        <?php if($item->endDate > 0) { ?>
                        <div class="time-btn-group-white-sep">&#8212;</div>
                        <div class="btn-group-col"><?php echo JHtml::date( $item->endDate , 'd.m.Y'); ?></div>
                        <div class="btn-group-col"><?php echo JHtml::date( $item->endDate , 'h:i'); ?></div>
                        <?php } ?>
                    </div>
                    
                	<?php if(!empty( $item->contacts )) { ?>
                		<div class="times-list-attendees"><span><?php echo JText::_('COM_SECRETARY_TIME_CONTACTS');?>:</span><?php echo $item->contactsNames; ?></div>
                    <?php } ?>
                </div>
                
                <div class="time-list-action">
                    <?php echo $item->additionalTitle; ?>
                </div>
                
                <div class="time-calender-td">
                    <div class="time-calender-container">
                    <?php
                    $nextTimeStart	= strtotime( $item->startDate );
                    $nextTimeTill	= strtotime( $item->endDate);
                    	
                    if( $nextTimeStart > 0)
                    { 
                        // Abstand Intervall-Laenge / Abstand this.time zu Intervall Start
                        $abstand =  $nextTimeStart - $vorspannSecs;
                        $startPos = round( ($abstand / $intervall * 100), 2) ;
                        
                        // Start bis Ende ist die Breite 
                        $widthPos = ';width:'. (( $nextTimeTill - $nextTimeStart ) * 100 / $intervall ) ."%;";
                        ?>
					<div date-title="<?php echo  date('d.m.Y', $nextTimeStart);?>" class="hasTooltip start-position" style="margin-left:<?php echo $startPos?>%<?php echo $widthPos; echo $item->timeColor;?>"></div>
                    <?php } ?>
                    </div>
                </div>
                
            </div>
		<?php
	}
	
	public static function listViewProjectTaskRow($x , $task, $userId, $prev, $intervall)
	{
		if(!empty($task->task_id) && !empty($task->time_id)) {
			$teamMember = 0;
			
			if($contacts = json_decode($task->contacts)) {
				foreach($contacts as $contact) {
					if(is_object($contact) && ($userId > 0) && intval($contact->id) === $userId) $teamMember = $userId;
				}
			}
			?>          
                <div class="fullwidth secretary-row-inner project-task-item row<?php echo $x % 2; ?> ">
                <div class="checkbox center hidden-phone" data-extension="tasks">
                <input id="cb<?php echo $task->task_id ?>" name="cid[task][]" value="<?php echo $task->task_id ?>" onclick="Joomla.isChecked(this.checked);" type="checkbox" />
                <span class="lbl"></span>
                </div>
                
                <div class="order nowrap center hidden-phone">
                	<div class="secretary-sort">
                    	<span class="move-up"><i class="fa fa-caret-up"></i></span>
                    	<span class="move-down"><i class="fa fa-caret-down"></i></span>
                	</div>
                    <input type="hidden" name="order[<?php echo $task->time_id;?>][]" value="<?php echo $task->task_id;?>" />
                </div>
                   
                <div class="progress-td nowrap center hidden-phone">
                    <div class="times-list-progress">
                        <span class="text"><?php echo round($task->progress,1); ?> %</span>
                        <span class="charger" style="width:<?php echo $task->progress; ?>%"></span>
                    </div>
                </div>
        
                <div class="title">
                	<?php echo str_repeat("&#8212;", $task->level); ?>
                    <span class="project-task-title">
                    	<a href="<?php echo JRoute::_('index.php?option=com_secretary&view=time&extension=tasks&id='.(int) $task->task_id); ?>">
                        <?php echo $task->task_title; ?>
                        </a>
                    </span>
                </div>
                
                <div class="time-list-action">
                    <div class="btn-group pull-right projectTimer-container">
                       
                       	<?php if($teamMember != 0) { ?>
                        <?php $taskprojectTimer	= \Secretary\Helpers\Times::getProjectStatus($task->task_id, $task->time_id); ?>
                        <div class="btn projectTimer" data-item="<?php echo $task->task_id; ?>" data-project="<?php echo $task->time_id; ?>">
                            <a <?php if($taskprojectTimer->action == "start") echo 'style="display:none;"'; ?>  data-task="start">
                                <i class="fa fa-play"></i>
                            </a>
                            <a <?php if($taskprojectTimer->action != 'start') echo 'style="display:none;"'; ?> data-task="stop">
                                <i class="fa fa-pause"></i>
                            </a>
                        </div>
                        <?php } ?>
                        
                        <div class="btn totalworktime-<?php echo $task->task_id; ?>">
                            <?php echo \Secretary\Helpers\Times::secondsToWorktime($task->totaltime, 'h'); ?>
                        <?php if($task->calctime > 0) { ?> / 
                            <?php echo round( $task->totaltime / $task->calctime , 1) .' %' ; ?> 
                        <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div class="time-calender-td">
                    <div class="time-calender-container">
                    <?php
                    $nextTimeStart	= strtotime( $task->startDate );
                    $nextTimeTill	= strtotime( $task->endDate);
                    
                    if( $nextTimeStart > 0)
                    { 
                        // Abstand Intervall-Länge / Abstand this.time zu Intervall Start
                        $abstand =  $nextTimeStart - $prev;
                        $startPos = round( ($abstand / $intervall * 100), 2) ;
                        
                        // Start bis Ende ist die Breite 
                        $widthPos = ';width:'. (( $nextTimeTill - $nextTimeStart ) * 100 / $intervall ) ."%;";
                        ?>    
                            <div date-title="<?php echo  date('d.m.Y', $nextTimeStart);?>" class="hasTooltip start-position" style="margin-left:<?php echo $startPos?>%<?php echo $widthPos; echo $task->timeColor;?>"></div>
                    <?php } ?>
                    </div>
                </div>
            	</div> 
		<?php
		}
	}
	
	public static function singleViewProjectTask($x , $task, $userId)
	{ 
		if(!empty($task->id) && !empty($task->projectID)) {
			
			$teamMember = 0;
			if($contacts = json_decode($task->contacts)) {
				foreach($contacts as $contact) {
					if(is_object($contact) && ($userId > 0) && intval($contact->id) === $userId) $teamMember = $userId;
				}
			}
		?>          
            <div class="project-task-item clearfix">
                
                <div class="project-task-item-progress">
                	<span class="text"><?php echo round($task->progress,1); ?> %</span>
                </div>
                
                <div class="pull-left project-task-item-title">
                	<?php echo str_repeat("&#8212; ", $task->level); ?>
                    <span class="project-task-title">
                    	<a href="<?php echo JRoute::_('index.php?option=com_secretary&view=time&extension=tasks&id='.(int) $task->id); ?>">
                        <?php echo $task->title; ?>
                        </a>
                    </span>
                </div>
             
                <div class="btn-group pull-right projectTimer-container">
                   
                    <?php if($teamMember != 0) { ?>
                    <?php $taskprojectTimer	= \Secretary\Helpers\Times::getProjectStatus($task->id, $task->projectID); ?>
                    <div class="btn projectTimer" data-item="<?php echo $task->id; ?>" data-project="<?php echo $task->projectID; ?>">
                        <a <?php if($taskprojectTimer->action == "start") echo 'style="display:none;"'; ?>  data-task="start">
                            <i class="fa fa-play"></i>
                        </a>
                        <a <?php if($taskprojectTimer->action != 'start') echo 'style="display:none;"'; ?> data-task="stop">
                            <i class="fa fa-pause"></i>
                        </a>
                    </div>
                    <?php } ?>
                    
                    <div class="btn totalworktime-<?php echo $task->id; ?>">
                        <?php echo \Secretary\Helpers\Times::secondsToWorktime($task->totaltime, 'h'); ?>
                    </div>
                </div> 
        
            </div>          
		<?php
		}
	}


}
