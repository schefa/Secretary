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
?>    
    <div class="btn-toolbar-charts fullwidth">
        <div class="item-stats">
            <div class="subitem">
                <label class="control-label"><?php echo JText::_('COM_SECRETARY_TOTAL');?></label>
                <div class="controls">
                    <strong><?php echo $this->products['total']; ?></strong>
                </div>
            </div>
        </div>
        
        <div class="item-stats pull-right">
            <div class="select-arrow select-arrow-white">
            <select class="form-control" name="filter_prodStates">
                <option value=""><?php echo JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL',  JText::_('COM_SECRETARY_STATUS')); ?></option>
                <?php echo JHtml::_('select.options', $this->prodStates, 'value', 'text', $this->state->get('filter.prodStates'), true);?>
            </select> 
            </div> 
        </div>

        <div class="item-stats pull-right">
            <div class="select-arrow select-arrow-white">
            <select class="form-control" name="filter_prodBestseller">
            <?php  
			    $prodBestsellerOptions[] = JHtml::_('select.option', 5,'- '.JText::_('COM_SECRETARY_LIMIT').' -'); 
			    $prodBestsellerOptions[] = JHtml::_('select.option', 5, 5 ); 
			    $prodBestsellerOptions[] = JHtml::_('select.option', 10, 10 ); 
			    $prodBestsellerOptions[] = JHtml::_('select.option', 20, 20 ); 
			    $prodBestsellerOptions[] = JHtml::_('select.option', 50, 50 ); 
			    $prodBestsellerOptions[] = JHtml::_('select.option', 100, 100 ); 

			    echo JHtml::_('select.options', $prodBestsellerOptions, 'value', 'text', $this->state->get('filter.prodBestseller'), true);
			?>
                
            </select> 
            </div> 
        </div>

    </div>
    
    
<?php if($this->products['total'] > 0) { ?>
    <div class="col-md-6">
        <h3><?php echo JText::sprintf("%s Bestseller", $this->state->get('filter.prodBestseller'));?></h3>
        <div id="secretary-chart-products" class="secretary-charts" style="width:100%;"></div>
    </div>
    <script>
        new Secretary.Charts( 'pie', {
            id : 'secretary-chart-products',
            labels : <?php echo json_encode(array_values($this->products['growth']['labels'])); ?>,
            series : <?php echo json_encode(array_values($this->products['growth']["series"]), JSON_NUMERIC_CHECK); ?> ,
            classes : <?php echo json_encode(array_values($this->products['growth']["classes"])); ?> ,
            width : "400px",
            donut : "170px"
		});
    </script>
<?php } ?>
