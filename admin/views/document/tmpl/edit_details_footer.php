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
 
if($this->item->rabatt != 0) {
	$rabattProz	= ($this->item->rabatt / $this->item->total) * 100;
} else {
	$rabattProz	= 0.0;	
}
?>

<div class="row-fluid">
    <div class="col-md-6">
        <div class="control-group">	
            <div class="control-label"><?php echo $this->form->getLabel('text');?></div>
            <?php echo $this->form->getInput('text');?>
        </div>
    </div>
    
    <div class="col-md-6 document-summary">
    	
        <table class="secretary-table">
        
            <?php $rabattCSS = ( $this->item->rabatt != 0) ? "table-row" : "none"; ?>
            
        	<tr class="table-rabatt-row" style="display:<?php echo $rabattCSS; ?>;">
            	<td><strong>-</strong></td>
            	<td>
                    <?php echo $this->form->getLabel('rabatt');?>
                </td>
            	<td>
                    <div class="secretary-input-group clearfix pull-left">
                        <div class="secretary-input-group-left"><input type="number" step="0.01" value="<?php echo round($rabattProz,3); ?>" class="table-rabatt-proz text-center" id="jform_rabatt_proz" min="0" /></div>
                        <div class="secretary-input-group-right">%</div>
                    </div>
                </td>
            	<td>
                    <div class="pull-right secretary-input-group clearfix">
                        <div class="secretary-input-group-left"><?php echo $this->form->getInput('rabatt');?></div>
                        <div class="secretary-input-group-right currency-control"><?php echo $this->item->currencySymbol; ?></div>
                    </div>
                </td>
            </tr>
        
        	<tr>
            	<td></td>
            	<td><?php echo $this->form->getLabel('subtotal');?>
                </td>
            	<td>
                </td>
            	<td>
                    <div class="pull-right secretary-input-group document-clean-input clearfix">
                        <div class="secretary-input-group-left"><input type="text" class="btn-no-bg document-subtotal" name="jform[subtotal]" value="<?php //echo $this->item->subtotal; ?>" readonly="readonly" /></div>
                        <div class="secretary-input-group-right currency-control"><?php echo $this->item->currencySymbol; ?></div>
                    </div>
                </td>
            
            </tr>
        	<tr valign="top">
            	<td><span class="btn-blank-text"><strong>+</strong></span></td>
            	<td>
                    <span class="label-blank-text"><?php echo $this->form->getLabel('taxtype');?></span>
                </td>
            	<td><div class="pull-left select-arrow">
                    <?php echo $this->form->getInput('taxtype');?>
                    </div> 
                </td>
            	<td><div class="document-taxrate"></div>
                </td>
            </tr>

        	<tr>
            	<td colspan="4"><hr class="document-endsumme-hr" />
                </td>
            </tr>
        
        	<tr class="secretary-document-total">
            	<td></td>
            	<td><?php echo JText::_('COM_SECRETARY_TOTAL');?>
                </td>
            	<td>
                </td>
            	<td>
                    <div class="secretary-input-group document-clean-input clearfix">
                        <div class="secretary-input-group-left"><input type="text" class="btn-no-bg document-total" name="jform[total]" value="" readonly="readonly" /></div>
                        <div class="secretary-input-group-right currency-control"><?php echo $this->item->currencySymbol; ?></div>
                    </div>
                </td>
            </tr>
        
        </table>
        
    </div>
    
    <div class="fullwidth">
        <a id="openRabattDialog" class="btn btn-white pull-right <?php if($rabattCSS != 'none') echo 'active'; ?>"><?php echo JText::_('COM_SECRETARY_DOCUMENT_RABATTDIALOG');?></a>
        
    	<div id="currency-change" class=" pull-right"><?php echo $this->form->getInput('currency');?></div>
    </div>
    
</div>
   