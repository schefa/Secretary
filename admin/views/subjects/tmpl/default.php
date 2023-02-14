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

$user	= Secretary\Joomla::getUser();

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$category	= (!empty($this->catid)) ? '&catid='.$this->catid : '';

if(empty($this->contact_locations)) {
	$contentClass = array(12, 0);
} else {
	$contentClass = array(10, 2);
}

$foldersLink = $user->authorise('core.show','com_secretary.folder');

?>

<div class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=subjects'. $category); ?>" method="post" name="adminForm" id="adminForm">
    
<div class="fullwidth">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
    
    <div class="secretary-main-container-left">
        <div class="secretary-main-area">
    
        <div class="fullwidth">
            <div class="pull-left">
                <h2 class="documents-title">
                    <span class="documents-title-first">
                    <?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></span>
                    <span class="documents-title-second">
                    <?php if($foldersLink) { ?>
                    <a href="<?php echo Secretary\Route::create('folders', array('extension'=>'subjects')); ?>"><?php } ?>
                    <?php echo JText::_('COM_SECRETARY_CATEGORIES'); ?>
                    <?php if($foldersLink) { ?></a><?php } ?>
                    </span>
                </h2>
            </div>
            <div class="pull-right">
                <div class="secretary-search btn-group">
                    <input type="text" class="form-control filter_search_subject" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                    <button class="btn btn-default hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
                    <button class="btn btn-default hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
                </div>
            </div>
        </div>
        
        <hr class="fullwidth" />
        
        <div class="fullwidth secretary-toolbar">
            <div class="select-arrow select-arrow-toolbar">
                <select id="subjects_category">
                    <?php echo JHtml::_('select.options', $this->folders, 'id', 'title', $this->catid, true);?>
                </select>
            </div>
            <?php $this->addToolbar(); ?>
        </div>
        
        <?php
        $gmaps = $this->state->params->get('gMapsContacts', 0);
        if(!empty($this->items[0]) && isset($gmaps) && ($gmaps == 1)) {
            echo Secretary\HTML::_('google.maps', $this->items, 'contacts' );
        }
        ?>
        
        <div class="fullwidth btn-toolbar">
        <?php echo Secretary\Navigation::displayAlphabeticalToolbar($this->view);  ?>   
        </div>
          
        <?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items"><?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?></div>
        <?php else : ?>

		<div class="chk_items_container" style="display:none;">
            <div class="fullwidth">
            <?php 
            foreach($this->selectedColumns as $key => $value) {
                $str = '<div class="chk_item"><input id="chk_'.ucfirst($key).'" type="checkbox" name="chk_group[]" value="'.$key .'" ';
                if($value) $str .= " checked";
                $str .= ' /><label for="chk_'. ucfirst($key) . '">'. JText::_('COM_SECRETARY_'.ucfirst($key)).'</label></div>';
                echo $str;
            }
            ?></div>
            <button class="btn btn-newentry" onclick="Joomla.submitbutton('subjects.applyColumns')"><?php echo JText::_("COM_SECRETARY_SAVE")?></button>
        </div>
        
        <table class="table table-hover" id="documentsList">
            <thead>
                <tr>
                	<th colspan="<?php echo 4; ?>">
                	<span class="custom-columns-btn btn-link"><?php echo JText::_('COM_SECRETARY_COLUMNS_ADAPT')?></span>    
                	</th>
                	<th colspan="<?php echo count($this->acceptedColumns ?? []); ?>"></th>
                </tr>
                <tr>
                    <th width="1%" class="hidden-phone">
                    <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
                    </th>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort','JSTATUS','a.state',$listDirn,$listOrder); ?>
                    </th>
                    <th></th>
                    <th class='left'>
                        <?php echo JHtml::_('grid.sort','COM_SECRETARY_SUBJECT_NAME','a.lastname',$listDirn,$listOrder); ?>
                    </th>
                    <?php 
                    foreach($this->acceptedColumns as $key => $value) {
                        $css = (isset($this->items[0]->$key) && is_numeric($this->items[0]->$key)) ? 'right' : 'left';
                    ?>
                    	<th class='nowrap <?php echo  $css ?>' >
                    		<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_'.$key, 'a.'.$key, $listDirn, $listOrder); ?>
                    	</th>
                    <?php   
                    }
                    ?>
                </tr>
            </thead> 
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
            
                <tr class="row<?php echo $i % 2; ?>">
                
                    <td class="center hidden-phone">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        <span class="lbl"></span>
                    </td>
                    
                    <td class="center">
                        <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
                        <?php echo Secretary\HTML::_('status.state', $item, $i, 'subjects', $item->canChange, $state ); ?>
                    </td>
                    
                    <td>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=subject&id='.(int) $item->id); ?>"><i class="fa fa-newspaper-o"></i></a>
            
                    <?php if(COM_SECRETARY_PDF && $item->template > 0) { ?>
                        <?php $href = Secretary\Route::create('subject', array('id' => $item->id, 'format' => 'pdf')); ?>
                        <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                    <?php } ?>
                    </td>
                    
                    <td>
                    <?php if ($item->checked_out) : ?>
                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'subjects.', $item->canCheckin); ?>
                    <?php endif; ?>
                    
                    <?php if ($item->canEdit && !$item->checked_out) : ?>
                        <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>" title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&task=subject.edit&id='.(int) $item->id); ?>">
                        <?php echo $item->lastname; ?></a>
                    <?php else : echo $item->lastname; endif; ?>
                
                    <?php if(!empty($item->firstname)) echo ", ". $item->firstname; ?>
                    
                    <?php if(!empty($item->upload)) { ?>
                        <a class="hasTooltip modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" data-original-title="<?php echo JText::_('COM_SECRETARY_DOCUMENT_DESC'); ?>" href="<?php echo "index.php?option=com_secretary&task=item.openFile&id=".$item->upload; ?>"><i class="fa fa-paperclip"></i></a>
                    <?php } ?>
					</td>

                	<?php foreach($this->acceptedColumns as $key => $value) { ?>
                    	<td class="<?php echo (isset($this->items[0]->$key) && is_numeric($this->items[0]->$key)) ? 'right' : 'left' ?>">
                    	<?php
                    	if(isset($item->$key)) {
                    	    if(is_float($item->$key)) {
                    	        echo Secretary\Utilities\Number::transformAmount($item->$key, 'get', $currency, '&mdash;');
                    	    } else {
                                echo ($key === 'category') ? JText::_($item->$key) : $item->$key;
                    	    }
                    	}
                    	?>
                    	</td>
                    <?php } ?>

                </tr>
                <?php endforeach; ?>
                
            </tbody>
            
            <tfoot class="table-list-pagination">
            <tr>
                <td colspan="<?php echo count(get_object_vars($this->items[0]) ?? []) ?>">
                    <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
                </td>
            </tr>
            </tfoot>
            
        </table>
        <?php endif;?>
        </div>
    </div>
    
    <div class="secretary-main-container-right">
    
        <div class="row-fluid">
        	<div class="select-arrow">
                <select id="filter_published" class="filter_category" onchange="this.form.submit()" name="filter_published">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                </select>
        	</div>
        	<div class="select-arrow">
                <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()">
                <option value=""><?php echo JText::_('COM_SECRETARY_SORT_BY');?></option>
                <?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
                </select>
        	</div>
        	<div class="select-arrow">
        	
        		<select name="filter_order_Dir" onchange="this.form.submit()" >
                <option <?php if($listDirn === 'asc') { echo 'selected="selected"'; } ?> value="asc"><?php echo JText::_('COM_SECRETARY_ASCENDING') ?></option>
                <option <?php if($listDirn === 'desc') { echo 'selected="selected"'; } ?> value="desc"><?php echo JText::_('COM_SECRETARY_DESCENDING') ?></option>
                </select>
                
        	</div>
        	<div class="select-arrow">
        	<?php echo $this->pagination->getLimitBox(); ?>
        	</div>
    	</div>
    
        <hr class="secretary-main-area-right-hr" />
        
        <?php if(!empty($this->contact_locations)) { ?>
        <ul class="secretary-list-group">
            <?php if(!empty($this->zip)) { ?>
                <li class="list-group-item">
                    <i class="fa fa-remove"></i>&nbsp;
                    <a href="<?php echo Secretary\Route::create( 'subjects', array( 'catid'=> $this->catid )); ?>"><?php echo JText::_('COM_SECRETARY_RESET_FILTER'); ?></a>
                </li>
            <?php } ?>
            <?php foreach($this->contact_locations AS $contact_locations) { ?>
                
                <li class="fullwidth">
                <?php if(!empty($contact_locations->zip)) { ?>
                    <a href="<?php echo Secretary\Route::create( 'subjects', array( 'catid'=> $this->catid, 'zip'=>$contact_locations->zip )); ?>">
                    <?php echo $contact_locations->zip ?>
                    </a>
                <?php } ?>
                	<span class="text"> 
                <?php
                    if(!empty($contact_locations->location) && !empty($contact_locations->zip)) {
                        echo $contact_locations->location;
                    } else {
                        echo JText::_('diverse');
                    }
                    ?>
                	</span>
                    <span class="badge"><?php echo $contact_locations->total ?></span>
                </li>
                
            <?php } ?>
            </ul>
        <?php } ?>
    </div>
    
    <?php echo $this->loadTemplate('batch'); ?>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->catid; ?>" id="subjects_catID" />
    <input type="hidden" name="zip" value="<?php echo $this->zip; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
    <?php echo JHtml::_('form.token'); ?>
    
    </div>
    
</form>

<script type="text/javascript">
jQuery( document ).ready(function( $ ) {
	// Autocomplete
	$( ".filter_search_subject" ).autocomplete({
			source: 'index.php?option=com_secretary&task=ajax.search&section=subjects', 
			minLength:2,
			open: function(event, ui) {
				$(".ui-autocomplete").css("z-index", 1000);
			},
			select: function( event, ui ) {
				$( this ).val( ui.item.value );
				$('#adminForm').submit();
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
			.append( '<a><span class="ui-menuitem-value">'+ item.value + '</span><br><span class="ui-menuitem-sub">'+ item.street + ', '+ item.zip + ' ' + item.location + '</span></a>' )
			.appendTo( ul );
		};
});
</script>

</div>