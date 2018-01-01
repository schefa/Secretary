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

$user = \Secretary\Joomla::getUser();
$canUpload = $user->authorise('core.upload', 'com_secretary');

$accounting_entry = Secretary\Database::getQuery('accounting',$this->item->accounting_id);

if(empty($this->item->id)) {
	echo JText::_('COM_SECRETARY_REPETITION_CONDITION_TO_USE');
	return;
} elseif($this->item->id > 0 && ( empty($accounting_entry) || $accounting_entry->state == 29 )) {
	
// Buchungssatz
$bookings = \Secretary\Helpers\Accounts::getBuchungssatz($this->item);
$this->document->addScriptDeclaration('var accJSON = '. json_encode($bookings,JSON_NUMERIC_CHECK) );
?>
<div id="secretary-document-accounting" class="fullwidth">
        
	<div class="alert alert-warning"><?php echo JText::_('COM_SECRETARY_ACCOUNTING_INFO'); ?></div>
    
	<?php if(isset($accounting_entry->state) && $accounting_entry->state == 29) { ?>
		<div class="alert alert-info"><?php echo JText::sprintf('Es wurde schon einmal für dieses Dokument ein Buchungssatz erstellt, allerdings wieder storniert ( #%s )', $accounting_entry->id); ?></div>
    <?php } ?>
    
    <div class="col-md-6 secretary-document-accounts-soll">
    	<h3><?php echo JText::_('COM_SECRETARY_SOLL'); ?> <span class="pull-right"> <?php echo $this->item->currencySymbol; ?></span><span class="pull-right secretary-acc-sum"></span></h3>
    
        <div class="secretary-acc-rowlist-s"></div>
        
        <div class="secretary-acc-add add-s btn" counter="0" data-type="s">+</div>
        
    </div>
    
    <div class="col-md-6 secretary-document-accounts-haben">
    	<h3><?php echo JText::_('COM_SECRETARY_HABEN'); ?> <span class="pull-right"> <?php echo $this->item->currencySymbol; ?></span><span class="pull-right secretary-acc-sum"></span></h3>
        
        <div class="secretary-acc-rowlist-h"></div>
        	
        <div class="secretary-acc-add add-h btn" counter="0" data-type="h">+</div>
        						
	</div>
    
    <?php if($this->item->id > 0) { ?>
    <div class="col-md-12">
        <div class="secretary-acc-checkbox">
            <div class="btn btn-buchen" data-id="<?php echo $this->item->id; ?>"><?php echo JText::_('Buchungssatz vormerken');?></div>			
        </div>
	</div>
    <?php } ?>
</div>
                    
<input id="acc_total_amount_total" class="secretary-acc-total" type="hidden" />
<input id="acc_total_amount_subtotal" class="document-subtotal" type="hidden" />
<input id="acc_total_amount_tax" class="document-taxtotal" type="hidden" />
<?php } elseif($this->item->id > 0 && $accounting_entry->state !== 29) {
	
	echo JText::sprintf('COM_SECRETARY_ACCOUNTS_SAVE_RECORD_SUCCESS',$this->item->accounting_id);
	
	$currency 	= $this->business['currency'];
	?>
	<hr>
	<h3><a href="index.php?option=com_secretary&view=accounting&id=<?php echo $this->item->accounting_id; ?>&layout=edit">#<?php echo $this->item->accounting_id; ?></a></h3>
    <div class="accountings-record clearfix">
        <div class="accountings-record-soll">
            <?php
                $soll =  json_decode($accounting_entry->soll,true);
                foreach( $soll as $key => $val) {
                    echo '<div class="accountings-record-item">';
                    $konto = Secretary\Database::getQuery('accounts_system',$val[0]);
                    $kontoTitle = (isset($konto->title)) ? $konto->title : JText::_('COM_SECRETARY_UNKNOWN');
                    echo $kontoTitle . '&nbsp;&nbsp;<span class="secretary-acc-sum-sub">'. $val[1].'</span>';
                    echo '</div>';
                }
            ?>
        </div>
        <div class="accountings-record-an">
        an
        </div>
        <div class="accountings-record-haben">
            <?php
                $haben =  json_decode($accounting_entry->haben,true);
                $html = array();
                foreach( $haben as $key => $value) {
                    $html[] = '<div class="accountings-record-item">';
                        $konto = Secretary\Database::getQuery('accounts_system',$value[0]);
                        $kontoTitle = (isset($konto->title)) ? $konto->title : JText::_('COM_SECRETARY_UNKNOWN');
                        $html[] =  $kontoTitle . '&nbsp;&nbsp;';
                        $html[] = '<span class="secretary-acc-sum-sub">'. $value[1]. '</span>';
                    $html[] =  '</div>';
                }
                echo implode('',$html);
            ?>
        </div>
    </div>
    
	<div class="vbottom"><?php echo JText::_('COM_SECRETARY_TOTAL') ?>: <strong><?php echo Secretary\Utilities\Number::getNumberFormat($accounting_entry->total,$currency); ?></strong></div>
                
	<?php 
} ?>
