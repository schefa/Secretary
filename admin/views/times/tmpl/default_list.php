<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;
 				
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$ordering   = ($listOrder == 'ordering'); 

if (empty($this->items)) {

	echo '<div class="alert alert-no-items">'. JText::_('COM_SECRETARY_NO_MATCHING_RESULTS') .'</div>';
	
} else {

    // Heute  
    $horzLines = $this->listOffsetPast + $this->listOffsetFuture;
    $line = ($horzLines > 0) ? round( (100 / $horzLines), 2) : 100;
	
    // Linien + Tage hervorheben
    // Berechnung der Startposition der grafischen Hervorhebung für Heute und Alle Tage
   	if($this->intervall > 0) { $lineToday =  $horzLines *  ( ($this->today - $this->vorspannSecs) / $this->intervall ) ; }
	else { $lineToday = 1; }
	
	// Sorgt fuer die Anzeige der kommenden und vorherigen TH-Werte
	// 86400 ist 1 Tag, aber 1 Monat ist unterschiedlich
	$nextScaleSeconds = array();
	switch ($this->listscale) {
		default : $format = 'd.m.'; $todayFlag = date($format, $this->today); $nextScaleSeconds[] = 86400; break;
		case 'hours' : $format = 'h:i'; $todayFlag = date($format, $this->today); $nextScaleSeconds[] = 3600; break;
		case 'weeks' : $format = 'd.m.'; $todayFlag = date($format, $this->today); $nextScaleSeconds[] = 604800; break;
		case 'months' :
			$format = 'd.m.'; 
			$todayFlag = date($format, $this->today);
			$startMonth = $this->today;
			for($i = 0; $i <= $horzLines; $i++) {
				$nextScaleSeconds[$i] = strtotime("+1 month", $startMonth) - $startMonth;
				$startMonth = strtotime("+1 month", $startMonth);
			}
			break;
	}
	
    $lineDaysTmp	= array();
	$lineDays		= array();
	$tmp			= $this->today + $nextScaleSeconds[0];
	
	for($i = 1; $i <= $horzLines; $i++) {
        $lineDaysTmp[]	= $tmp;
        $lineDays[]		= round( $horzLines *  ( ($tmp - $this->vorspannSecs) / $this->intervall ) ) ;
        if($this->listscale === 'months') {
			$tmp		+= $nextScaleSeconds[$i];
		} else {
			$tmp		+= $nextScaleSeconds[0];
		}
    } 
    
	// Hoehe der Linien
    $lineHeight	= ((  96 * $this->items[0]->countItems )  + ( $this->items[0]->countTasks * 56) ) + 96;
	unset($this->items[0]->countItems);
	unset($this->items[0]->countTasks);
	
	$dayLineVert = false;
	$lineDaysPos = -1;
    ?>
    
<div class="time-calender fullwidth">
    
    <div id="documentsList" style="overflow:hidden;">
    
        <div class="secretary-row-inner clearfix thead">

            <div class="checkbox hidden-phone"><?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span></div>
            
            <div class="order nowrap center hidden-phone"><?php if (true === $this->canDo->get('core.edit')) { ?><a onclick="Joomla.submitbutton('times.saveOrder')"><i class="fa fa-save"></i></a><?php } ?>&nbsp;</div>
            
			<div class="progress-td">&nbsp;</div>
                
            <div class="title">
                <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TITLE', 'time.title', $listDirn, $listOrder); ?> / <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TIMES_STARTDATE', 'startDate', $listDirn, $listOrder); ?>
            </div>
            <div class="time-list-action">&nbsp;</div>
            <div class="left time-calender-th">
                <div class="time-calender-container">
                    <?php
					for ($i = 0; $i < $horzLines; $i++) {
						$lineClass = "";
						if($i == $lineToday) {
							$lineClass = "le-today";
						} elseif(in_array($i, $lineDays)) {
							$lineClass = "le-day"; $dayLineVert = true; $lineDaysPos++;
						}
					?>
                        <div class="le <?php echo $lineClass; ?>" style="height:<?php echo $lineHeight;?>px;left:<?php echo $line * $i;?>%;">
                            <?php
							if($dayLineVert){
								echo "<span>". date($format, $lineDaysTmp[$lineDaysPos]) ."</span>";
								$dayLineVert = false;
							}
							?>
                        </div>
                    <?php } ?>
                    <div class="le-date-of-view" style="left:<?php echo $lineToday * $line; ?>%;">
						<?php echo $todayFlag; ?>
					</div>
                 </div>  
            </div>

        </div>
        
        <div class="tbody">
        <?php echo $this->loadTemplate('list_items');  ?> 
		</div> <!-- tbody -->

	</div> <!-- documentsList -->

</div>                 
<?php } ?> 
 

<div class="fullwidth table-list-pagination">
<div class="margin-top fullwidth">

	<div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
	
	<div class="pull-right">
		
		<div class="pull-left limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
		
		<div class="pull-left">
			<select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()">
			<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
			<?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
			</select>
		</div>
		
		<div class="pull-left">
	    <select id="filter_published" class="filter_category" onchange="this.form.submit()" name="filter_published">
	        <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
	        <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
	    </select>
		</div>
		<div class="pull-left">
		
			<select name="filter_order_Dir" onchange="this.form.submit()" >
	        <option <?php if($listDirn === 'asc') { echo 'selected="selected"'; } ?> value="asc"><?php echo JText::_('COM_SECRETARY_ASCENDING') ?></option>
	        <option <?php if($listDirn === 'desc') { echo 'selected="selected"'; } ?> value="desc"><?php echo JText::_('COM_SECRETARY_DESCENDING') ?></option>
	        </select>
	        
		</div>
	</div>
       	 		
</div>		
</div> 
      
<input type="hidden" value="" name="extension" id="extension" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
