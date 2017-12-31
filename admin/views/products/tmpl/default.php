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

// Import CSS 
$business	= Secretary\Application::company('currency');
$currency 	= $business['currencySymbol'];

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$category = (!empty($this->categoryId)) ? '&catid='.$this->categoryId : '&catid=0';

$acceptedColumnsKeys = array_keys($this->acceptedColumns);

$eingang = array_flip( array('priceCost','quantityBought','totalBought') );
$ausgang = array_flip( array('priceSale','quantity','total') );
$thPos = array('startEingang' => false, 'endEingang' => false, 'startAusgang' => false, 'endAusgang' => false,'endOther'=>false);
foreach($acceptedColumnsKeys as $pos=>$col) {
    if(isset($eingang[$col])) {
        $thPos['endEingang'] = $pos + 1; 
        if(false === $thPos['startEingang']) {
            $thPos['startEingang'] = $pos ;
        }
    } elseif(isset($ausgang[$col])) {
        $thPos['endAusgang'] = $pos + 1; 
        if(false === $thPos['startAusgang']) {
            $thPos['startAusgang'] = $pos ; 
        } 
    } else {
        $thPos['endOther'] = $pos + 1; 
    }
}
 
?>

<div class="secretary-main-container">
    
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=products'.$category); ?>" method="post" name="adminForm" id="adminForm">
      
	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
        
    <?php if ($this->canDo->get('core.show')) { ?>
    
        <div class="secretary-main-area">
        
		<div class="row-fluid clearfix">
			<div class="pull-left">
        		<h2 class="documents-title">
                    <span class="documents-title-first"><?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></span>
                    <span class="documents-title-second">
                    <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=folders&extension=products'); ?>"><?php echo JText::_('COM_SECRETARY_CATEGORIES');?></a>
                    </span>
                    <span class="documents-title-second">
                    <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=locations&extension=products'); ?>"><?php echo JText::_('COM_SECRETARY_LOCATIONS_PRODUCTS');?></a>
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
			<div class="pull-right margin-right">
            	<div class="select-arrow select-arrow-white select-small">
                    <select id="filter_year" onchange="this.form.submit()" name="filter_year"> 
                        <?php echo JHtml::_('select.options', $this->years, 'value', 'text', $this->state->get('filter.year'), true);?>
                    </select>
                </div>
			</div>
        </div>
        
        <hr />
        
		<div class="row-fluid secretary-toolbar clearfix">
            <div class="select-arrow-bg select-arrow-toolbar">
                <div class="select-arrow">
                    <select id="products_category">
                        <?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                </div>
            </div>
			<?php $this->addToolbar(); ?>
                
            <?php if(!empty($this->documents)) { ?>
            <div class="pull-right select-arrow-control">
                <?php $document = JText::_('COM_SECRETARY_DOCUMENT'); ?>
                <span class="select-label"><?php echo JText::sprintf('COM_SECRETARY_CREATE_THIS', $document); ?></span>
                
                <?php 
                $documents	= array();
                foreach($this->documents as $document) {
                    $documents[] = JHtml::_('select.option', $document->id, JText::_($document->title)); }
                ?>
                <div class="select-arrow select-arrow-white">
                <select id="add_new_document">
                    <?php echo JHtml::_('select.options', $documents, 'value', 'text');?>
                </select>
                </div>
                <div id="add_document" class="btn btn-newentry"><?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                <script>
                (function($){
                    $('#add_document').click(function(){
                        var documentId = $('#add_new_document').val();
                        var url = 'index.php?option=com_secretary&view=document&layout=edit&catid='+documentId;
                        window.location.href = url;
                    })
                })(jQuery);
                </script>
            </div>
            <?php } ?>
		</div>
          
		<div class="btn-toolbar">
        <?php echo Secretary\Navigation::displayAlphabeticalToolbar($this->view);  ?>   
		</div>
        
        <hr />
        
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
		
	
		<div class="secretary-products-stockwatch">
		<?php
		foreach ($this->items as $i => $item) :
    		if($item->quantityMin > 0) {
        		$need = (  $item->quantity - $item->quantityBought ) + $item->quantityMin;
        		if($need > 0) {
        		    echo '<div class="products-needed">'.JText::sprintf('COM_SECRETARY_PRODUCTS_WATCH_ARE_NEEDED','<strong>'.$need .' '.$item->entity.'</strong>', '<strong>'.$item->title.'</strong>').'</div>';
        		}
    		}
    		if($item->quantityMax > 0) {
        		$seed = ( $item->quantityBought - $item->quantity) - $item->quantityMax;
        		if($seed > 0) {
        		    echo '<div class="products-toomuch">'.JText::sprintf('COM_SECRETARY_PRODUCTS_WATCH_TOO_MUCH','<strong>'.$seed .' '.$item->entity.' '.$item->title.'</strong>', number_format($item->quantityMax)).'</div>';
        		}
    		}
		endforeach;
		?>
		</div>
    
		<div class="chk_items_container" style="display:none;">
            <div class="fullwidth">
            <?php 
            foreach($this->selectedColumns as $key => $value) {
                $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="chk_group[]" value="'.$key .'" ';
                if($value) $str .= " checked";
                $str .= ' /><label for="chk_'. ucfirst($key) . '">'. JText::_('COM_SECRETARY_PRODUCT_'.ucfirst($key)).'</label></div>';
                echo $str;
            }
            ?></div>
            <button class="btn btn-newentry" onclick="Joomla.submitbutton('products.applyColumns')"><?php echo JText::_("COM_SECRETARY_SAVE")?></button>
        </div>

		<div class="secretary-responsive">

		<table class="table table-hover" id="documentsList">
			<thead>

				<tr>
                	<th colspan="<?php echo (3 + $thPos['endOther'])?>">
                		<a href="#" class="custom-columns-btn"><?php echo JText::_('COM_SECRETARY_COLUMNS_ADAPT')?></a>
                	</th>
                	<?php if(false !== ($thPos['startEingang'])) { ?>
                	<th colspan="<?php echo $thPos['endEingang'] - $thPos['startEingang'] ?>" class='center products-list-summary'><?php echo JText::_('COM_SECRETARY_EINGANG');?></th>
                	<?php } ?>
                	<?php if(false !== ($thPos['startAusgang'])) { ?>
                	<th colspan="<?php echo $thPos['endAusgang'] - $thPos['startAusgang'] ?>" class='center products-list-summary'><?php echo JText::_('COM_SECRETARY_AUSGANG');?></th>
                	<?php } ?>
                	<th colspan="2" class='center products-list-summary'><?php echo JText::_('COM_SECRETARY_VALUE_OF_GOODS');?></th>
				</tr>

				<tr>
				     
					<th width="1%" class="hidden-phone">
            		<?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
					</th>
            				
                    <th width="1%"></th>
            				
                    <th class='left'>
                    <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_PRODUCT_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    	      
                    <?php 
                    $z = 0;
                    foreach($this->acceptedColumns as $key => $value) {
                        
                        $css = (isset($this->items[0]->$key) && is_numeric($this->items[0]->$key)) ? 'right' : 'left';

                        if($thPos['startEingang'] === $z || $thPos['startAusgang'] === $z )
                            $css .= ' products-list-summary';
                        
                    ?>
                    	<th class='nowrap <?php echo  $css ?>' >
                    		<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_PRODUCT_'.$key, 'a.'.$key, $listDirn, $listOrder); ?>
                    	</th>
                    <?php   
                    $z++;
                    }
                    ?>

                    <th class='right products-list-summary'>
                    <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_QUANTITY', 'a.quantity', $listDirn, $listOrder); ?>
                    </th>
                    <th class='right products-list-total-rest'>
                    <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_VALUE', 'a.total', $listDirn, $listOrder); ?>
                    </th>
                    
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
                       
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        <span class="lbl"></span>
					</td>
					
                    <td>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=product&id='.(int) $item->id); ?>"><i class="fa fa-newspaper-o"></i></a>
                    
                    <?php if(COM_SECRETARY_PDF && $item->template > 0) { ?>
                        <?php $href = Secretary\Route::create('product', array('id' => $item->id, 'format' => 'pdf')); ?>
                        <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                    <?php } ?>
                    </td>
                           
                    <td>
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'products.', $item->canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($item->canEdit) : ?>
                            <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&task=product.edit&id='.(int) $item->id); ?>">
                            <?php echo Secretary\Utilities::cleaner($item->title,true); ?></a>
                        <?php else : ?>
                            <?php echo Secretary\Utilities::cleaner($item->title,true); ?>
                        <?php endif; ?>
                        
                        <?php if(!empty($item->upload)) { ?>
                            <a class="hasTooltip modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" data-original-title="<?php echo JText::_('COM_SECRETARY_DOCUMENT_DESC'); ?>" href="<?php echo "index.php?option=com_secretary&task=item.openFile&id=".$item->upload; ?>"><i class="fa fa-paperclip"></i></a>
                        <?php } ?>
                    </td>  
                                   
                    <?php 
                    $x = 0;
                    foreach($this->acceptedColumns as $key => $value) {
                        
                        $css = (isset($this->items[0]->$key) && is_numeric($this->items[0]->$key)) ? 'right' : 'left';
                        
                        if($thPos['startEingang'] === $x || $thPos['startAusgang'] === $x )
                            $css .= ' products-list-summary';
                        
                    ?>
                    	<td class="<?php echo $css?>">
                    	<?php
                    	if(isset($item->$key)) {
                	        if(in_array($key,array('priceSale','totalBought','priceCost','total'))) {
                	           echo Secretary\Utilities\Number::getNumberFormat($item->$key, $currency);
                	        } elseif(in_array($key,array('quantityBought','quantity','quantityMax','quantityMin'))) {
                	           echo Secretary\Utilities\Number::transformAmount($item->$key,'get','', '&mdash;');
                	        } else {
                	           echo $item->$key; 
                	        }
                    	}
                    	?>
                    	</td>
                    <?php $x++; } ?>  

                   	<?php $bestandsMenge = $item->quantityBought - $item->quantity; ?>
                   	<?php $bestandsWert = $item->priceSale * $bestandsMenge; ?>
                   	<td class="text-right products-list-summary"><?php  echo $bestandsMenge; ?></td>
                    
                    <td class="text-right products-list-total-rest nowrap">
                    <?php echo Secretary\Utilities\Number::transformAmount($bestandsWert,'get', $currency, JText::_('COM_SECRETARY_VALUE_WHAT_VK')); ?>
                    </td>
                    
                </tr>
				<?php endforeach; ?>
			</tbody>

		</table>
		</div>
		
		<?php endif;?>
    
			<div class="table-list-pagination">
			<div class="margin-top">

                	<div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
           	 		<div class="pull-left limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
           	 		<div class="pull-left clearfix">
                    <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('COM_SECRETARY_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?></select>
                    </div> 
                	<div class="pull-left">
                	
                		<select name="filter_order_Dir" onchange="this.form.submit()" >
                        <option <?php if($listDirn === 'asc') { echo 'selected="selected"'; } ?> value="asc"><?php echo JText::_('COM_SECRETARY_ASCENDING') ?></option>
                        <option <?php if($listDirn === 'desc') { echo 'selected="selected"'; } ?> value="desc"><?php echo JText::_('COM_SECRETARY_DESCENDING') ?></option>
                        </select>
                        
                	</div>
                	<div class="pull-left">
                    <select id="filter_published" class="filter_category" onchange="this.form.submit()" name="filter_published">
                        <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                        <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                    </select>
                	</div>


			</div>
			</div>
    
		</div>
	<?php } else { ?>
        <div class="alert alert-danger"><?php echo JText::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?>  
    
	<?php echo $this->loadTemplate('batch'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" id="products_catID" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
	<?php echo JHtml::_('form.token'); ?>
</form>   

</div>
