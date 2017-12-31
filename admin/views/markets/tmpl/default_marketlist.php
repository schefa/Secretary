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
 
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?> 

<thead>
	<tr>
		<th width="1%" class="hidden-phone">
        	<?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
		</th>
        <th class='left'>
        	<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_NAME', 'a.name', $listDirn, $listOrder); ?> /
        	<?php echo JHtml::_('grid.sort','Watchlist','category', $listDirn, $listOrder); ?>
        </th>
        <th class='table-right-border right'></th>
        <th class='center'>
        	<?php echo JText::_('COM_SECRETARY_PRODUCT_PRICECOST'); ?>
		</th>
        <th class='market-latest-price'>
        	<?php echo JText::_('COM_SECRETARY_PRICE'); ?>
		</th>
        <?php 
        foreach($this->selectedColumns as $key => $value) {
            if($value) {
        ?>
        	<th class='right'><?php echo strlen($key) > 10 ? substr($key,0,10)."..." : $key; ?></th>
        <?php     
            $this->columnsCount++;
            }
        }
        ?>
	</tr>
</thead>
 

<tbody class="market-list">
<?php if (empty($this->items)) : ?>
	<tr>
	<td colspan="<?php echo 6 + $this->columnsCount; ?>" class="alert alert-no-items">
		<?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
	</td>
	</tr>
<?php 

else : 

$symbols = array();
foreach ($this->items as $i => $item) {
    $symbols[] = ($item->symbol);
}

$model = JModelAdmin::getInstance('Market','SecretaryModel');
$marketsData = $model->getStocksQuotes($symbols);

foreach ($this->items as $i => $item) :
    
	if(isset($marketsData[$item->symbol])) {
		$item = (object) array_merge( (array) $item, $marketsData[$item->symbol]);
	} elseif(isset($marketsData)) {
        $item = (object) array_merge( (array) $item, $marketsData);
	}

    $currency = (isset($item->Currency)) ? $item->Currency : $this->business['currency']; 
    $currency = Secretary\Database::getQuery('currencies',$currency,'currency','symbol','loadResult');
    
    $item->watchlistChange = 0;

    $item->currentTotal = 0;
    $item->watchlistTotal =  $item->ek_price * $item->quantity;
    $item->watchlistTotalChange = 0;
    
    if(isset($item->LastTradePriceOnly)) {
        $item->watchlistChange = ($item->ek_price > 0) ? round( ( $item->LastTradePriceOnly  * 100 / $item->ek_price ) - 100 ,2 ) : 0;
        if($item->watchlistChange<0)
            $item->watchlistChange = $item->watchlistChange."%";
        else if ($item->watchlistChange>0)
            $item->watchlistChange = "+".$item->watchlistChange."%"; 

        $item->currentTotal =  $item->LastTradePriceOnly * $item->quantity;
        $total = ( $item->LastTradePriceOnly - $item->ek_price ) * $item->quantity;
        $item->watchlistTotalChange = number_format($total, 2);
        if(!isset($this->depotVolume[$currency])) $this->depotVolume[$currency] = array('currentTotal'=>0,'watchlistTotal'=>0,'changeTotal'=>0);
        $this->depotVolume[$currency]['currentTotal'] += $item->currentTotal;
        $this->depotVolume[$currency]['watchlistTotal'] += $item->watchlistTotal;
        $this->depotVolume[$currency]['changeTotal'] += $total;
    }
?>
<tr class="row<?php echo $i % 2; ?> market-item" data-symbol="<?php echo $item->symbol; ?>" data-item="<?php echo $this->escape(json_encode(array($item->id,$item->quantity,$item->ek_price))); ?>">
    
	<td class="center hidden-phone">
		<?php echo JHtml::_('grid.id', $i, $item->id); ?>
        <span class="lbl"></span>
	</td>
	<td>
	<div class="market-item-name"><?php echo  $item->name; ?></div>
	<div class="market-item-subname"><?php echo  $item->exch; ?> - <?php echo  $item->symbol; ?></div>
	<?php if($item->catid > 0) { ?>
	<span class="market-item-watchlist"><?php echo $item->Watchlist; ?></span>
	<?php } ?>
	</td>

	<td class="right table-right-border">
    	<div class="market-item-totalvalue"><?php echo Secretary\Utilities\Number::getNumberFormat($item->currentTotal,$currency); ?></div>
    	<?php echo Secretary\Utilities\Number::getNumberFormat($item->watchlistTotal,$currency); ?><br>
		<div class="market-watchlist-change <?php if($item->watchlistTotalChange>0) echo "positiv"; elseif($item->watchlistTotalChange<0) echo "negativ"; ?>"><?php echo $item->watchlistTotalChange.' '.$currency; ?></div>
	</td>
	
	<td class="center">
    	<?php echo Secretary\Utilities\Number::getNumberFormat($item->ek_price,$currency); ?><br>
    	<span class="market-watchlist-change  <?php if($item->watchlistTotalChange>0) echo "positiv"; elseif($item->watchlistTotalChange<0) echo "negativ"; ?>"><?php echo $item->watchlistChange; ?></span>
	</td>
	
	<td class="market-latest-price">
	<?php if(isset($item->LastTradePriceOnly)) { ?>
	<div class="market-price"><?php echo number_format($item->LastTradePriceOnly,2).' '.$currency; ?></div>
	<div class="market-price-change <?php if(strpos($item->PercentChange,"+")!== false) echo "positiv"; else echo "negativ"; ?>"><?php echo ($item->PercentChange); ?></div>
	<?php } ?>
	</td>

	           
<?php 
foreach($this->selectedColumns as $key => $value) {
    if($value) {
?>
	<td class="right">
	<?php
	if(isset($item->$key)) {
	    if(is_numeric($item->$key))
	        echo number_format($item->$key, 2);
	    else
	       echo $item->$key;
	}
	?>
	</td>
<?php        
    }
}
?>
	
</tr>
<?php endforeach; ?>

<tfoot>
    <tr>
    	<td></td> 
    	<td><?php echo JText::_('COM_SECRETARY_TOTAL')?></td>
    	<td class="right table-right-border right">
    	<?php 
    	$cnt = 1;
    	if(isset($this->depotVolume)) {
    	foreach($this->depotVolume as $currency => $array) {
    	    $class = (count($this->depotVolume) !== $cnt) ? ' margin-bottom' : '';
    	    echo '<div class="fullwidth market-list-total '.$class.'">';
    	    foreach($array as $key => $value) {
    	       echo Secretary\Utilities\Number::getNumberFormat($value,$currency);
    	       if($key =='watchlistTotal' || $key =='currentTotal') echo '<br>';
    	    }
    	    echo '</div>';
    	    $cnt++;
    	}
    	}
    	?>
    	</div>
    	</td> 
    	<td colspan="2"></td>
    	<td colspan="<?php echo (int) $this->columnsCount; ?>">
    	</td>
    </tr>
    <tr>
    	<td colspan="<?php echo 6 + $this->columnsCount; ?>">
    		<?php echo $this->pagination->getListFooter(); ?>
    	</td>
    </tr>
</tfoot>

<?php endif; ?>
</tbody>