<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

$user = JFactory::getUser();
$canUpload = $user->authorise('core.upload', 'com_secretary');


?>

<div class="row">
    
    <div class="col-md-4">
        <div class="document-additional">
            <div id="productUsage" class="control-group">
                <h3 class="title"><i class="fa fa-shopping-cart"></i> <?php echo JText::_('COM_SECRETARY_PRODUCT_USAGE');?></h3>
                <?php echo $this->productUsageOption ;?>       
                <div class="secretary-desc margin-top"><?php echo JText::_('COM_SECRETARY_PRODUCT_USAGE_DESC');?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div id="document-repetition" class="document-additional">
            
            <h3 class="title"><i class="fa fa-calendar"></i> <?php echo JText::_('COM_SECRETARY_REPETITION');?></h3>
            <?php
			if(empty($this->item->id)) {
				echo JText::_('COM_SECRETARY_REPETITION_CONDITION_TO_USE');
			} else {
			?>
            <select id="document-repetition-check" type="text" class="form-control" name="jform[repetition][check]" >
                <?php
                $rows = array( 0	=>"JNO", 1	=>"JYES", ); $check	= array();
                foreach($rows AS $key => $value) {
                    $check[]	= JHtml::_('select.option',	$key, JText::_($value)); }
                $repetitionChecked = (isset($this->item->repetition)) ? $this->item->repetition['check'] : 0;
                echo JHtml::_('select.options', $check, 'value', 'text', $repetitionChecked, true);
                ?>
            </select>
            
            <div class="repetition-container clearfix <?php if($repetitionChecked == 1) { echo 'out';}?>">

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
    </div>
    
    
    <div class="col-md-12">
        <div class="document-additional">
            <h3 class="title"><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
            <div class="secretary-desc margin-bottom"><?php echo JText::_('COM_SECRETARY_BUSINESS_FIELDS_DESC');?></div>
            
            <div class="fields-items"></div>
            <div class="field-add-container clearfix">
                <?php echo Secretary\HTML::_('datafields.listOptions', $this->extension, array('pUsage','template','emailtemplate','docsSoll','docsSollTax','docsHaben','docsHabenTax','anrede') ); ?>
                <div id="field-add" counter="<?php echo 0 + $this->item->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
            </div>
        </div>
    </div>
    
</div>				
         