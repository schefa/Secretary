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
<div class="container-fluid">

<div style="width:200px"> 
<?php echo Secretary\HTML::_('documents.summary', $this->item->documents_summary['data'], $this->item->documents_summary['totat_amount']); ?>
</div>
<hr />

<table class="table table-hover">
    <thead>
        <tr>
            <td></td>
            <td><?php echo JText::_('COM_SECRETARY_CREATED'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_DOCUMENT'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_NR'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_NETTO'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_TAX'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_TOTAL'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_STATUS'); ?></td>
        </tr>
    </thead>
	<tbody>
        <?php
        foreach($this->item->documents AS $i => $item) { 
		
		if( ($taxTotal = json_decode($item->taxtotal, true)) && is_array($taxTotal)) {
			$taxTotal = array_sum($taxTotal);
		} else {
			$taxTotal = floatval($item->taxtotal);
		}
		?>
        <tr>
            <td>
            <?php if($item->template > 0) { ?>
                <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" href="<?php echo Secretary\Route::create('document', array('id' => $item->id)); ?>"><i class="fa fa-newspaper-o"></i></a>
                 
				<?php if(COM_SECRETARY_PDF) { ?>
				<?php $href = Secretary\Route::create('document', array('id' => $item->id, 'format' => 'pdf')); ?>
				<a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
				<?php } ?>
				            
			<?php } ?>
            </td>
            <td><a href="<?php echo Secretary\Route::create('document', array('id'=>(int) $item->id)); ?>"><?php echo $item->created; ?></a></td>
            <td><?php echo JText::_($item->category_title); ?></td>
            <td><?php echo $item->nr; ?></td>
            <td><?php echo Secretary\Utilities\Number::getNumberFormat($item->subtotal) .' '.$item->currencySymbol; ?></td>
            <td><?php echo Secretary\Utilities\Number::getNumberFormat($taxTotal).' '.$item->currencySymbol; ?></td>
            <td><strong><?php echo Secretary\Utilities\Number::getNumberFormat($item->total).' '.$item->currencySymbol; ?></strong></td>
            <td>
            <?php
            $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon );
            echo Secretary\HTML::_('status.state', $item->state, $i, 'documents.', false, $state );
            ?>
            </td>
        </tr>
        <?php } ?>
    </tbody> 
	</table>
</div>
