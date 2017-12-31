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

?>

<hr>

<div class="fullwidth margin-bottom">
    <div class="col-md-4">
        <label><?php echo JText::_('COM_SECRETARY_TEMPLATE');?></label>
        <div class="control-group">
        <?php echo $this->itemtemplates; ?>
        </div>
    </div>
    <div class="col-md-4">
        <label><?php echo JText::_('COM_SECRETARY_EMAIL_TEMPLATE');?></label>
        <div class="control-group">
        <?php echo $this->emailtemplates; ?>
        </div>
    </div>
    <div class="col-md-4">
        <label><?php echo JText::_('COM_SECRETARY_PRODUCT_USAGE');?></label>
        <div class="control-group">
         <?php echo $this->productUsageOption ;?>  
        </div>
    </div>
</div>

<div class="fullwidth">
    <div class="col-md-3">
        <label><?php echo JText::_('COM_SECRETARY_FIELD_SOLL');?></label>
        <div class="control-group">
        	<input class="search-accounts_system fullwidth" type="text" value="<?php if(!empty($this->item->docsSollTitle)) echo $this->item->docsSollTitle->nr.' '. $this->item->docsSollTitle->title; ?>">
			<input type="hidden" name="jform[fields][docsSoll]" value="<?php if($this->item->docsSoll > 0) echo $this->item->docsSoll; ?>">
        </div>
    </div>
    <div class="col-md-3">
        <label><?php echo JText::_('COM_SECRETARY_FIELD_SOLL_TAX');?></label>
        <div class="control-group">
        	<input class="search-accounts_system fullwidth" type="text" value="<?php if(!empty($this->item->docsSollTaxTitle)) echo $this->item->docsSollTaxTitle->nr.' '. $this->item->docsSollTaxTitle->title; ?>">
			<input type="hidden" name="jform[fields][docsSollTax]" value="<?php if($this->item->docsSollTax > 0) echo $this->item->docsSollTax; ?>">
        </div>
    </div>
    <div class="col-md-3">
        <label><?php echo JText::_('COM_SECRETARY_FIELD_HABEN');?></label>
        <div class="control-group">
        	<input class="search-accounts_system fullwidth" type="text" value="<?php if(!empty($this->item->docsHabenTitle)) echo $this->item->docsHabenTitle->nr.' '. $this->item->docsHabenTitle->title; ?>">
			<input type="hidden" name="jform[fields][docsHaben]" value="<?php if($this->item->docsHaben > 0) echo $this->item->docsHaben; ?>">
        </div>
    </div>
    <div class="col-md-3">
        <label><?php echo JText::_('COM_SECRETARY_FIELD_HABEN_TAX');?></label>
        <div class="control-group">
        	<input class="search-accounts_system fullwidth" type="text" value="<?php if(!empty($this->item->docsHabenTaxTitle)) echo $this->item->docsHabenTaxTitle->nr.' '. $this->item->docsHabenTaxTitle->title; ?>">
			<input type="hidden" name="jform[fields][docsHabenTax]" value="<?php if($this->item->docsHabenTax > 0) echo $this->item->docsHabenTax; ?>">
        </div>
    </div>
</div>

