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

$extension = 'times';

$user = \Secretary\Joomla::getUser(); 
?>
           
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#details" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_TAB_ERWEITERT', true); ?></a></li>
    <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
    <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
    <?php endif; ?>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="home">
    
        <div class="row">
            <div class="col-md-9">
            
            <fieldset class="adminform">
            
                <div class="row-fluid">
                
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('projectID'); ?></div>
                        <div class="controls select-large select-arrow select-arrow-white">
                        <select type="text" name="jform[projectID]" >
                            <?php echo JHtml::_('select.options', $this->projects['items'], 'value', 'text', $this->projects['default'] , true); ?>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('parentID'); ?></div>
                        <div class="controls select-large select-arrow select-arrow-white"><?php echo $this->form->getInput('parentID'); ?></div>
                    </div>
                    
                    <div class="col-md-12">
                    	<hr class="fullwidth" />
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TEAM'); ?></label></div>
                            <div class="posts multiple-input-selection clearfix" data-counter="<?php echo $this->contactsCounts; ?>">
                                <div>
                                <input class="search-features" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
                                </div>
                            </div>
                        </div>
                        <hr class="fullwidth" />
                    </div>
                    
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('startDate'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('startDate'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('endDate'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('endDate'); ?></div>
                        </div>
                    </div>
                    
                    <div id="entry-repetition" class="col-md-12">
            <label><?php echo JText::_('COM_SECRETARY_REPETITION');?></label>
            <?php
			if(empty($this->item->id)) {
				echo JText::_('COM_SECRETARY_REPETITION_CONDITION_TO_USE');
			} else {
			?>
            <select id="document-repetition-check" type="text" class="form-control" name="jform[repetition][check]" >
                <option value="0" <?php if(isset($this->item->repetition) && $this->item->repetition['check'] == 0) echo ' selected="selected"' ?>><?php echo JText::_('JNO'); ?></option>
                <option value="1" <?php if(isset($this->item->repetition) && $this->item->repetition['check'] == 1) echo ' selected="selected"' ?>><?php echo JText::_('JYES'); ?></option>
            </select>
            
            <div class="repetition-container clearfix <?php if($this->item->repetition['check'] == 1) { echo 'out';}?>">
                        
                 <table>
                
                	<tr>
                    	<td><?php echo JText::_('COM_SECRETARY_REPETITION_PLAN');?></td>
                    	<td><input name="jform[repetition][endTime]" type="number" step="1" min="1" class="form-control" value="<?php if(isset($this->item->repetition['endTime'])) { echo $this->item->repetition['endTime']; } else {echo 1;}?>" placholder="<?php echo JText::_('COM_SECRETARY_END');?>" /> <?php echo JText::_('COM_SECRETARY_ENTRIES');?></td>
                    </tr>
                    
                	<tr>
                    	<td colspan="2"><div class="repetition-container-hint"><?php echo JText::_('COM_SECRETARY_REPETITION_PLAN_DESC');?></div>
                        </td>
                    </tr>
                
                	<tr>
                    	<td><?php echo JText::_('COM_SECRETARY_REPETITION_PERIOD');?></td>
                    	<td>
                        <input name="jform[repetition][zyklus]" class="form-control" type="number" step="1" min="1" value="<?php if(isset($this->item->repetition['zyklus'])){ echo $this->item->repetition['zyklus']; } else { echo 1; } ?>" placeholder="Interval" />
                        
                        <select type="text" name="jform[repetition][type]" class="form-control">
                            <?php
                            $rows = array( 'days' =>"COM_SECRETARY_DAYS", 'weeks' =>"COM_SECRETARY_WEEKS", 'months' =>"COM_SECRETARY_MONTHS" ); $type = array();
                            foreach($rows AS $key => $value) {
                                $type[]	= JHtml::_('select.option',	$key, JText::_($value)); }
                            echo JHtml::_('select.options', $type, 'value', 'text', $this->item->repetition['type'] , true);
                            ?>
                        </select>
                        </td>
                    </tr>
                    
                	<tr>
                    	<td colspan="2"><div class="repetition-container-hint"><?php echo JText::_('COM_SECRETARY_REPETITION_PERIOD_DESC');?></div>
                        </td>
                    </tr>
                	<tr>
                    	<td colspan="2"><div class="repetition-container-hint"><?php echo JText::_('COM_SECRETARY_REPETITION_PERIOD_EXAMPLE');?></div>
                        </td>
                    </tr>
                    
                </table>
                
                        </div>
                        <?php } ?>
                    </div>
                    
                    <div class="col-md-12">
                    <hr />
                        <div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('text'); ?></div>
                    </div>
                    
                </div>
                
            </fieldset>
                
            </div>
            
            <div class="col-md-3">
                <div class="row-fluid">
                
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls select-arrow"><?php echo $this->form->getInput('state'); ?></div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('calctime'); ?></div>
                        <div class="controls">
                            <div class="secretary-input-group clearfix">
                                <div class="secretary-input-group-left">
								<?php echo $this->form->getInput('calctime'); ?>
                                </div>
                                <div class="secretary-input-group-right currency-control"><?php echo JText::_('COM_SECRETARY_HOURS'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('progress'); ?></div>
                        <div class="controls">
                            <div class="secretary-input-group clearfix">
                                <div class="secretary-input-group-left">
								<?php echo $this->form->getInput('progress'); ?>
                                </div>
                                <div class="secretary-input-group-right currency-control">%</div>
                            </div>
                        </div>
                    </div>
            
				</div>
 			</div>
            
        </div>
    
    </div>
    
    <div class="tab-pane" id="details">
        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
        <div class="fields-items"></div>
        <div class="field-add-container clearfix">
            <?php echo Secretary\HTML::_('datafields.listOptions', $extension ); ?>
            <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
        </div>
    </div>
    
    <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
        <div class="tab-pane" id="permission">
            <fieldset>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
        </div>
    <?php endif; ?>
    
</div>	 