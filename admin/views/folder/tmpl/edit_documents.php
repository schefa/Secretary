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

