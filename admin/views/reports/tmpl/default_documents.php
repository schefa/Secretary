<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;

// Get Business Data
$user			= \Secretary\Joomla::getUser();
$currency		= $this->business['currencySymbol'];
$listOrder		= $this->state->get('list.ordering');
$listDirn		= $this->state->get('list.direction');
$filterZeitraum = $this->state->get('filter.zeitraum');
$filterZeitraum = (empty($filterZeitraum)) ? 1 : $filterZeitraum;

$documentsCharts = array();
$documentsCharts['labels']	= array();
$documentsCharts['series']		= array();
$documentsCharts['classes']	= array();

$highest        = $this->documents['tops'];
$currencyRates  = (!$this->state->get('filter.docscurrency')) ? Secretary\Webservice::currencyConverter($this->documents['currency'],$this->business['currency']) : false;
 
$currOptions[] = JHtml::_('select.option', 0, JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL',  JText::_('COM_SECRETARY_CURRENCIES')) );
foreach($this->documents['currency'] as $cur) {
    $currOptions[] = JHtml::_('select.option', $cur, $cur );
}

foreach($this->docsStates AS $state) {
    $documentsCharts['legend'][] = '<span class="legend-colorbox legend-income status-'.$state->class.'"></span><span class="legend-colorbox legend-costs status-'.$state->class.'"></span><span class="legend-text">'. JText::_($state->text) .'</span>';
}
?>

<div class="btn-toolbar-charts fullwidth">
    <div class="item-stats">
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_ERGEBNIS'); ?></label>
            <div class="controls">
            <?php 
            foreach($this->documents_total as $cur => $values) {
                $profit = $values['einnahmenNetto'] - $values['ausgabenNetto']; 
                echo '<strong>';
                if( $profit > 0) { echo '+'; } echo Secretary\Utilities\Number::getNumberFormat($profit,$cur); 
                echo '</strong><br>'; 
            }
            ?>
            </div>
        </div>
    </div>
        
    <div class="item-stats">
    
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_EINNAHMEN'); ?></label>
            <div class="controls income status-paid">
            <?php 
            foreach($this->documents_total as $cur => $values) { 
                echo Secretary\Utilities\Number::getNumberFormat($values['einnahmenNetto'],$cur).'<br>'; 
            }
            ?>
            </div>
        </div>
        
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_AUSGABEN'); ?></label>
            <div class="controls costs status-paid">
            <?php 
            foreach($this->documents_total as $cur => $values) { 
                echo Secretary\Utilities\Number::getNumberFormat($values['ausgabenNetto'],$cur).'<br>'; 
            }
            ?>
            </div>
        </div>
           
    </div>
    <div class="item-stats">
    
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_UMSATZSTEUER'); ?></label>
            <div class="controls">
            <?php 
            foreach($this->documents_total as $cur => $values) { 
                echo Secretary\Utilities\Number::getNumberFormat($values['einnahmenUmst'],$cur).'<br>'; 
            }
            ?>
            </div>
        </div>
        
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_VORSTEUER'); ?></label>
            <div class="controls">
            <?php 
            foreach($this->documents_total as $cur => $values) { 
                echo Secretary\Utilities\Number::getNumberFormat($values['ausgabenUmst'],$cur).'<br>'; 
            }
            ?></div>
        </div>
           
    </div>
            
    <div class="item-stats pull-right">
    	<div class="btn-group">
            <div class="pull-right select-arrow select-arrow-white">
            <select class="form-control"  name="filter_docs_state">
                <option value="0"><?php echo JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL',  JText::_('COM_SECRETARY_STATUS')); ?></option>
                <?php echo JHtml::_('select.options', $this->docsStates, 'value', 'text', $this->state->get('filter.docsstate'), true);?>
            </select> 
             
            </div> 
    	</div>
    	<div class="btn-group">
            <div class="pull-right select-arrow select-arrow-white">
            	<select class="form-control" name="filter_docs_currency">
                    <?php echo JHtml::_('select.options', $currOptions, 'value', 'text',  $this->state->get('filter.docscurrency') );?>
                </select>
    		</div>
    	</div>
        
    </div>

</div>
    
<div class="row-fluid fullwidth">
    <div class="col-md-12">
    <?php 
    if($this->state->get('filter.docscurrency'))
        $currencyIncomeCosts = "";
    else
        $currencyIncomeCosts = $this->business['currencySymbol'];
    
    ?>
        <h3><?php echo JText::_("COM_SECRETARY_CHARTS_INCOME_AND_COSTS") .' '. $currencyIncomeCosts;?></h3>
        <div id="secretary-chart-documents" class="secretary-charts" style="width:100%;"></div>
    </div>
</div>
      
<table class="table table-striped" id="overviewList">
    <thead>
        <tr>
        
        <?php if($filterZeitraum != 3) { ?>
        <th class='left'>
        <?php echo JText::_('COM_SECRETARY_YEAR'); ?>
        </th>
        <?php }?>
        
        <th class="left table-right-border">
        <?php echo JText::_('COM_SECRETARY_DATE'); ?>
        </th>
        <th class='left'>
        <?php echo JText::_('COM_SECRETARY_EINNAHMEN'); ?>
            <?php /*?><br />
            <?php if(!is_numeric($this->state->get('filter.state'))) { 
            foreach($this->states AS $state) {
                $cssClass = Secretary\Database::getQuery('status', $state->value,'id','class','loadResult');
                echo '<span class="income status-'. $cssClass .'">'. $state->text .'</span>';
            }} ?><?php */?>
        </th>
        <th class='left'>
        <?php echo JText::_('COM_SECRETARY_AUSGABEN'); ?>
        </th>
        <th width="1%" class="table-left-border left nowrap">
        <?php echo JText::_('COM_SECRETARY_DIFFERENCE'); ?>
        </th>
        
        </tr>
    </thead>
    <tbody>
    <?php

foreach ($this->documents['data'] as $i => $item) :
        
    $tmp = explode(":",$i);
    $i = (int) $tmp[1];
    ?>
        <tr class="row<?php echo $i % 2; ?>">
        
        <?php if($filterZeitraum != 3) { ?>
        <td width="1%"><?php echo $item['year']; ?></td>
        <?php }?>
        
        <td width="1%" class="table-right-border left nowrap">
        <strong>
            <?php 
            if ($filterZeitraum == 2) {
                $dateObj   = DateTime::createFromFormat('!m', $i);
                echo $dateObj->format('F');
                $documentsCharts['labels'][$i][0] = $dateObj->format('F');
                $documentsCharts['labels'][$i][1] = $i .' / '. $item['year'];
            } elseif ($filterZeitraum == 3) {
                echo $i;
                $documentsCharts['labels'][$i][0] = $i;
                $documentsCharts['labels'][$i][1] = JText::_('COM_SECRETARY_YEAR') .': '. $i;
            } else {
                $weekHTML = array();
                $week_start = new DateTime();
                $week_start->setISODate($item['year'],$i);
                $week_end = new DateTime();
                $week_end->setISODate($item['year'],$i+1,0);
                $weekHTML[] = $week_start->format('d.m.') . ' - ' ;
                $weekHTML[] = $week_end->format('d.m.');
                $week = implode('',$weekHTML);
                echo $week . " (". $i .") ";
                $documentsCharts['labels'][$i][0] = $i;
                $documentsCharts['labels'][$i][1] = $week . $item['year'];
            }
			?>
		</strong>
        </td>
        <td class="left nowrap">
            <?php 
            if(!isset($item['i'])) {
                echo 0 . " ". $currency;
				$documentsCharts['series'][$i][0][] = 0;
                $documentsCharts['classes'][$i][0][] = 'income'; 
            } else {
                $es = Secretary\HTML::_('reports.documentsItem', $i, $item, 'income', $documentsCharts, $currencyRates);
                $documentsCharts = $es['data'];
                echo $es['html'];
            }
            ?>
        </td>
        
        <td class="nowrap">
            <?php
            if(!isset($item['c'])) {
                echo 0 . " ". $currency;
				$documentsCharts['series'][$i][1][] = 0;
                $documentsCharts['classes'][$i][(int) 1][] = 'costs'; 
            } else {
                $es = Secretary\HTML::_('reports.documentsItem', $i, $item, 'costs', $documentsCharts, $currencyRates);
                $documentsCharts = $es['data'];
                echo $es['html'];
            }
            ?>
        </td>
        <td class="table-left-border nowrap">
            <?php 
            if(isset($item['i'])) {
                foreach($item['i'] as $curr => $value) {
                    $sum = (isset($item['c'][$curr]['total'])) ? ( $value['total'] - $item['c'][$curr]['total']) :  $value['total'];
                    if($sum > 0) echo '+ ';
                    echo Secretary\Utilities\Number::getNumberFormat($sum,$curr).'<br>';
                }
            } elseif(isset($item['c'])) {
                foreach($item['c'] as $curr => $value) {
                    echo "- ". Secretary\Utilities\Number::getNumberFormat($item['c'][$curr]['total'],$curr).'<br>';
                }
            }
            ?>
        </td>

        </tr>
        <?php
        
		$documentsCharts['series'][$i] = array_values($documentsCharts['series'][$i]);
        
        endforeach; 
		
        $documentsCharts['labels']	= array_values($documentsCharts['labels']);
		$documentsCharts['series']	= array_values($documentsCharts['series']); 
		$documentsCharts['classes']	= array_values($documentsCharts['classes']);
		
        ?>
    </tbody>
</table>
 
<script>
    new Secretary.Charts( 'bars', {
		id : 'secretary-chart-documents',
		labels : <?php echo json_encode($documentsCharts['labels']); ?>,
		series : <?php echo json_encode($documentsCharts['series'],false); ?> ,
		classes :<?php echo json_encode($documentsCharts['classes'],false); ?>,
		legend : { series : <?php echo json_encode($documentsCharts['legend']); ?> , align : "center"},
	});
</script>
