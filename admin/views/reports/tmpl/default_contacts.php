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
?>    
<div class="btn-toolbar-charts fullwidth">
    <div class="item-stats">
        <div class="subitem">
            <label class="control-label"><?php echo JText::_('COM_SECRETARY_TOTAL');?></label>
            <div class="controls">
                <strong><?php echo $this->contacts['total']; ?></strong>
            </div>
        </div>
    </div>
    <div class="item-stats">
        <?php $keys = array_keys($this->contacts['genders']['labels']);
         foreach($keys as $x) {?>
            <div class="subitem">
                <label class="control-label"><?php echo $this->contacts['genders']['labels'][$x];?></label>
                <div class="controls">
                    <strong><?php echo $this->contacts['genders']['series'][$x]; ?></strong>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <div class="item-stats">
        <?php $keys = array_keys($this->contacts['categories']['labels']);
         foreach($keys as $x) { ?>
            <div class="subitem">
                <label class="control-label"><?php echo $this->contacts['categories']['labels'][$x];?></label>
                <div class="controls">
                    <strong><?php echo $this->contacts['categories']['series'][$x]; ?></strong>
                </div>
            </div>
        <?php } ?>
    </div>
    
    
    <div class="item-stats pull-right">
        <div class="select-arrow select-arrow-white">
        <select class="form-control" name="filter_cont_state">
            <option value=""><?php echo JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL',  JText::_('COM_SECRETARY_STATUS')); ?></option>
            <?php echo JHtml::_('select.options', $this->contStates, 'value', 'text', $this->state->get('filter.contstate'), true);?>
        </select> 
        </div> 
    </div>

</div>

    
<?php if($this->contacts['total'] > 0) { ?>
<div class="row-fluid fullwidth">
    <div class="col-md-12">
        <h3><?php echo JText::_("COM_SECRETARY_CHARTS_NEW_CONTACTS");?></h3>
        <div id="secretary-chart-contacts-growth" class="secretary-charts" style="width:100%;"></div>
        <hr>
        <div class="col-md-12">
            <div class="col-md-4">
        	<h3><?php echo JText::_("COM_SECRETARY_ANREDE");?></h3>
            <div id="secretary-chart-contacts-genders" class="secretary-charts"></div>
            </div>
            <div class="col-md-4">
        	<h3><?php echo JText::_("COM_SECRETARY_CATEGORIES_SUBJECTS");?></h3>
            <div id="secretary-chart-contacts-categories" class="secretary-charts"></div>
            </div>
            <div class="col-md-4">
        	<h3><?php echo JText::_("COM_SECRETARY_LOCATION");?></h3>
            <div id="secretary-chart-contacts-locations" class="secretary-charts"></div>
            </div>
        </div>
    </div>
</div>
      
    <script>
        new Secretary.Charts( 'bars', {
             id : 'secretary-chart-contacts-growth',
             labels : <?php 
             $newContactsLabels = array_values($this->contacts['growth']['labels']); 
             echo json_encode($newContactsLabels); ?>,
             series : <?php
             $newContactsSeries = array_values($this->contacts['growth']['series']);
             echo json_encode($newContactsSeries); ?> ,
        });

        new Secretary.Charts( 'pie', {
            id : 'secretary-chart-contacts-genders',
            labels : <?php echo json_encode(array_values($this->contacts['genders']['labels'])); ?>,
            series : <?php echo json_encode(array_values($this->contacts['genders']["series"])); ?> ,
            classes : <?php echo json_encode(array_values($this->contacts['genders']["classes"])); ?> ,
			width : "200px",
		});
        new Secretary.Charts( 'pie', {
            id : 'secretary-chart-contacts-categories',
            labels : <?php echo json_encode(array_values($this->contacts['categories']['labels'])); ?>,
            series : <?php echo json_encode(array_values($this->contacts['categories']["series"])); ?> ,
            classes : <?php echo json_encode(array_values($this->contacts['categories']["classes"])); ?> ,
			width : "200px",
		});
        new Secretary.Charts( 'pie', {
            id : 'secretary-chart-contacts-locations',
            labels : <?php echo json_encode(array_values($this->contacts['location']['labels'])); ?>,
            series : <?php echo json_encode(array_values($this->contacts['location']["series"])); ?> ,
            classes : <?php echo json_encode(array_values($this->contacts['location']["classes"])); ?> ,
			width : "200px",
		});

    </script>
<?php } ?>
