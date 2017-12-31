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

$user		= Secretary\Joomla::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

//$this->document->addScript(SECRETARY_MEDIA_PATH.'/js/secretary.charts.js?'.time());
$this->columnsCount = 0;
$this->depotVolume = array();
$foldersLink = $user->authorise('core.show','com_secretary.folder');
?>

<div class="secretary-main-container">

<?php 
if(!function_exists("curl_init")) {
	echo '<div class="alert alert-warning">Library <em>php_curl.dll</em> is missing in your PHP environment</div>';
}
?>

<div class="market-item-details" style="display:none;">
    <div class="fullwidth">

		<div class="control-group"> 
			<select id="market-scale" data-symbol="##SYMBOL##">
        		<option value="1d">1 d</option>
        		<option value="5d">5 d</option>
        		<option value="1m">1 m</option>
        		<option value="3m">3 m</option>
        		<option selected value="6m">6 m</option>
        		<option value="1y">1 y</option>
        		<option value="2y">2 y</option>
        		<option value="5y">5 y</option>
        		<option value="8y">8 y</option> 
			</select>
    	</div>
    	
<?php if($this->canDo->get('core.edit')) { ?>
    	<hr>
    	<div class="secretary-control-group fullwidth">
        	
        	<div class="control-group">
        		<label for="qty_##ID##"><?php echo JText::_('COM_SECRETARY_WATCHLIST');?></label>
                <select id="category" name="detail[##ID##][catid]">
                <?php echo JHtml::_('select.options', $this->categories, 'id', 'title');?>
                </select>
            </div>
        	
        	<div class="control-group">
        		<label for="qty_##ID##"><?php echo JText::_('COM_SECRETARY_QUANTITY');?></label>
        		<input id="qty_##ID##" step="1" min="1" class="market-item-details-quantity" type="number" placeholder="<?php echo JText::_('COM_SECRETARY_QUANTITY');?>" name="detail[##ID##][quantity]" value="##QTY##" />
        	</div>
        	
        	<div class="control-group">
        		<label for="price_##ID##"><?php echo JText::_('COM_SECRETARY_MARKETS_EKPRICE');?></label>
        		<input id="price_##ID##" step="0.01" min="0.01" class="market-item-details-price" type="number" placeholder="<?php echo JText::_('COM_SECRETARY_MARKETS_EKPRICE');?>" name="detail[##ID##][ek_price]" value="##PRICE##" />
        	</div>
        	
        	<div class="btn btn-sm market-item-update"><?php echo JText::_('COM_SECRETARY_SAVE')?></div>
        	
        	<div class="btn btn-sm market-item-cancel"><?php echo JText::_('COM_SECRETARY_CANCEL')?></div>
    	
    	</div>
<?php } ?>
    	
    	<div id="market-message">
    	</div>
    	
    </div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=markets'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
        
    <?php if ($this->canDo->get('core.show')) { ?>
	<div class="secretary-main-area">
    
		<div class="row-fluid fullwidth">
			<div class="pull-left">
        		<h2 class="documents-title">
                    <span class="documents-title-first"><?php echo $this->title; ?></span>
                    <span class="documents-title-second">
                    <?php if($foldersLink) { ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=folders&extension=markets'); ?>">
                    <?php } ?>
                    <?php echo JText::_('COM_SECRETARY_CATEGORIES_MARKETS');?>
                    <?php if($foldersLink) { ?>
                    </a>
                    <?php } ?>
                    </span>
                </h2>
			</div>
            
			<div class="pull-right">
            <div class="secretary-search btn-group">
                <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                <button class="btn btn-default hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
                <button class="btn btn-default hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
            </div>
            </div>
        </div>
        
        <hr />
         
		<div class="secretary-toolbar clearfix">
            <div class="select-arrow-bg select-arrow-toolbar">
                <div class="select-arrow">
					<select id="watchlist_select">
                        <?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                    <div class="select-arrow-down"></div>
                </div>
            </div>
            <div class="select-arrow-toolbar-next">
                &#10095; 
            </div>
                            
            <div class="markets-buttons-toolbar">
                
                <?php if($this->canDo->get('core.create')) { ?>
                <div class="pull-left btn-group margin-right">
                    <input class="btn search-market" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_MARKETS_SEARCH')?>" /> 
                    <input class="btn search-market-quantity" type="number" min="1" step="1" placeholder="<?php echo JText::_('COM_SECRETARY_QUANTITY')?>" value="1" /> 
                	<div class="btn btn-newentry add-market"><i class="fa fa-plus"></i></div>
                </div>
                <?php } ?>
                
                <div class="pull-left">
                <?php
                if($this->canDo->get('core.delete')) {
                    echo Secretary\Navigation::ToolbarItem('markets.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash');
                }
                ?> 
                <div class="btn custom-columns-btn"><i class="fa fa-columns"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_COLUMNS_ADAPT')?></div> 
                </div>  
            </div>

            <div class="pull-right">
                <div class="btn-group">
                	<div class="select-small select-arrow select-arrow-white">
                    <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                        <select id="filter_published" class="filter_category" onchange="this.form.submit()" name="filter_published">
                            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                        </select>
                    </div>
                </div>
            </div>
		</div>
	
		<div class="chk_items_container" style="display:none;">
            <div class="fullwidth">
            <?php 
            foreach($this->selectedColumns as $key => $value) {
                $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="chk_group[]" value="'.$key .'" ';
                if($value) $str .= " checked";
                $str .= ' /><label for="chk_'. ucfirst($key) . '">'.ucfirst($key).'</label></div>';
                echo $str;
            }
            ?></div>
            <button class="btn btn-newentry" onclick="Joomla.submitbutton('markets.applyColumns')"><?php echo JText::_("COM_SECRETARY_SAVE")?></button>
        </div>
        
		<table class="table table-hover" id="marketable">
			<?php echo $this->loadTemplate('marketlist'); ?>
		</table>
		
	</div>
    
	<?php } else { ?>
        <div class="alert alert-danger"><?php echo JText::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" id="watchlist_id" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

</div>

<script>
jQuery( document ).ready(function( $ ) {

	var columns =  <?php echo 6 + $this->columnsCount; ?>;
	var stock = {};
	
    $("input.search-market").live('focus', function() {
    	$(this).autocomplete({
    		source: 'index.php?option=com_secretary&task=markets.searchStock', 
    		minLength:3,
    		open: function(event, ui) {
    			$(".ui-autocomplete").css("z-index", 1000);
    		},
    		select: function( event, ui ) {
    			$(this).val( ui.item.name ); 
    			stock = ui.item;
    			return false;
    		}
    	})
    	.autocomplete( "instance" )._renderItem = function( ul, item ) {
    		return $( '<li class="fullwidth market-search-item">' )
    		.append( '<div class="pull-left"><span class="ui-menuitem-value">'+ item.name + '</span><span class="ui-menuitem-subvalue">'+item.exchDisp+' - '+item.symbol+'</span></div><div class="pull-right">'+item.type+'</div>' )
    		.appendTo( ul );
    	};
    });

    $('.add-market').click(function() {
		stock.catid = $('#watchlist_id').val();
		stock.quantity =$('.search-market-quantity').val();
		
        $('table#marketable').empty();
        
    	$.ajax({
			type: 'POST',
			url:  "index.php?option=com_secretary&task=markets.addStock",
			data: { 'data': JSON.stringify(stock) },
			dataType: "json",
			success: function (response) {
				$('table#marketable').load('index.php?option=com_secretary&view=markets&format=raw&layout=marketlist');
            }
		});
    });

    $('#market-scale').live('change',function(){
        var value = $(this).val();
        var symbol = $(this).data('symbol');
        document.getElementById("market-graph").src = "http://chart.finance.yahoo.com/z?s="+symbol+"&t="+value+"&a=vm&p=m50,m200"; 
    });

    $('.market-item-cancel').live('click', function() { $('tr#market-chart').remove(); });

    $('.market-item').live('click', function() {

		$('#market-chart').remove();
		
        var symbol = $(this).data('symbol');
        var item = $(this).data('item');
        
		var href = '<a href="http://finance.yahoo.com/chart/'+symbol+'" target="_blank">Chart in Yahoo Finance</a>';
		var details = $('.market-item-details:first').html();
		details = details.replace(/##ID##/g, item[0]);
		details = details.replace(/##QTY##/g, item[1]);
		details = details.replace(/##PRICE##/g, item[2]);
		details = details.replace(/##SYMBOL##/g,symbol);
        
        var contents = '<tr id="market-chart"><td class="chart" style="padding:50px;background:none" colspan='+columns+'><div class="pull-left"><img id="market-graph" src="http://chart.finance.yahoo.com/z?s='+symbol+'&t=6m&a=vm&p=m50,m200"/><br>'+href+'</div><div class="pull-right">'+details+'</div></td></tr>';

		$(contents).insertAfter(this);
    });

    $('.market-item-update').live('click',function() {
		var form = $('form:first'); 
    	$.ajax({
			type: 'POST',
			url:  "index.php?option=com_secretary&task=markets.updateStock",
			data:  { 'data':  form.serialize() },
			dataType: "json",
			success: function (response) {
        		$('table#marketable').empty();
				$('table#marketable').load('index.php?option=com_secretary&view=markets&format=raw&layout=marketlist');
            }
		});
	});

});

</script>