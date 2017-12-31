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

$user		= \Secretary\Joomla::getUser(); 

$currency	= $this->state->get('currency');
$filterList	= $this->state->params->get('filterList');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$category       = (!empty($this->categoryId)) ? '&catid='.$this->categoryId : '';
$foldersLink    = $user->authorise('core.show','com_secretary.folder');
?>

<div class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=documents'.$category); ?>" method="post" name="adminForm" id="adminForm">

	<div class="fullwidth">
        
		<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
        
        <div class="secretary-main-container-left">
        
			<?php if(COM_SECRETARY_PDF === false) { ?>
            <div class="secretary-main-area">
				<div class="alert alert-warning"><?php echo JText::_('COM_SECRETARY_PDF_MISSING_PLUGIN');?></div>
            </div>
			<?php } ?>
        
			<?php if(!empty($this->itemsExpired)) { echo $this->loadTemplate('deadline'); } ?>
            
            <div class="secretary-main-area">
                
                <div class="fullwidth clearfix">
                    <div class="pull-left">
                        <h2 class="documents-title">
                            <span class="documents-title-first">
                                <?php echo $this->report['title']; ?>
                            </span>
                            <span class="documents-title-second">
                            <?php if($foldersLink) { ?>
                            <a href="<?php echo Secretary\Route::create('folders',array('extension'=> 'documents'));?>">
                            <?php } ?>
                            <?php echo JText::_('COM_SECRETARY_CATEGORIES'); ?>
                         	<?php if($foldersLink) { ?></a><?php } ?>
                            </span>
                        </h2>
                    </div>
                    <div class="pull-right">
                    <div class="secretary-search btn-group">
                        <input type="text" class="form-control" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH_DOCUMENTS_VIEW'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_SECRETARY_SEARCH_DOCUMENTS_VIEW'); ?>" />
                        <button class="btn btn-default hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
                        <button class="btn btn-default hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
                    </div>
                    </div>
                </div>
        		<hr class="fullwidth" />
                <div class="fullwidth secretary-toolbar">
                    <div class="select-arrow-bg select-arrow-toolbar"> 
                        <select id="documents_category" class="reloadSite">
                            <?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                        </select> 
                    </div>
                    <?php $this->addToolbar(); ?>
                </div>
                
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-no-items"><?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?></div>
                <?php else : ?>
                
                <table class="table table-hover" id="documentsList">
                    <thead>
                        <tr>
                            <th width="1%" class="hidden-phone">
                            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
                            </th>
                            <th width="1%" class='center'>
                            <?php echo JHtml::_('grid.sort','COM_SECRETARY_NR', 'a.nr', $listDirn, $listOrder); ?>
                            </th>
                            <th width="1%" class='center'>
                            </th>
                            <th width="1%">
                            </th>
                            <th class='left'>
                            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_DATE', 'a.created', $listDirn, $listOrder) ." / ". JHtml::_('grid.sort','COM_SECRETARY_FOLDER', 'a.catid', $listDirn, $listOrder) ; ?>
                            </th> 
                            <th class='left'>
                            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SUBJECT', 'contact_name', $listDirn, $listOrder); ?>
                            </th>
                            <th class='right'>
                            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TOTAL', 'a.total', $listDirn, $listOrder); ?>
                            </th>
                        <?php if (isset($this->items[0]->state)): ?>
                            <th width="1%" class="nowrap center">
                                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                        <?php endif; ?>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($this->items as $i => $item) : 
						
						if(empty($item->email)){ $subject = json_decode($item->subject); $item->email = $subject[5]; }						
                        ?>
                        <tr class="row<?php echo $i % 2 ; ?>">
                            
                            <td class="center hidden-phone">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                <span class="lbl"></span>
                            </td>
                            
                            <td class="center">
                            	<?php echo !empty($item->nr) ? $item->nr : ' - '; ?>
                            </td>
                            
                            <td class="left nowrap">
                                        
                            <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>" title="<?php echo JText::_('COM_SECRETARY_SHOW'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=document&id='.(int) $item->id); ?>"><i class="fa fa-newspaper-o"></i></a>
                                
                            <?php if($item->template > 0) { ?>
                            	<a class="printpdf hasTooltip open-modal" data-url="<?php echo Secretary\Route::create('document', array('layout'=>'preview','format'=>'raw','tmpl'=>'component', 'id'=> $item->id )); ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PREVIEW'); ?>"><i class="fa fa-print"></i></a> 
                            <?php } ?>
                            	
                            <?php if($item->canEdit && (\Secretary\Helpers\Access::checkAdmin()) && !empty($item->email)) { ?>
                                <?php $email = 'index.php?option=com_secretary&view=document&layout=email&format=raw&tmpl=component&id='.$item->id ; ?>
                                <a class="hasTooltip open-modal" data-url="<?php echo $email; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_EMAIL'); ?>"><i class="fa fa-envelope-o"></i></a>
                            <?php }  ?>
                                
                            </td>
                            
                            <td class="left" >
                            <?php if($item->template > 0 && COM_SECRETARY_PDF) { ?>
                                <?php $href = Secretary\Route::create('document', array('id' => $item->id, 'format' => 'pdf')); ?>
                                <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                            <?php } ?> 
                            </td>

                            <td class="left">
                                     
                            <?php $created = '<br/>'.  JText::_('COM_SECRETARY_CREATED') .' '. date('H:i:s d.m.Y', $item->createdEntry); ?>
                            
                            <?php if ($item->canEdit && !$item->checked_out) : ?>
                                <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'). $created; ?>"  href="<?php echo Secretary\Route::create(false, array('task'=> 'document.edit', 'id' => (int) $item->id, 'catid' => (int) $item->catid)); ?>">
                                <?php echo $item->created. ' / '. $item->category_title; ?></a>
                                
                            <?php else : ?>
                                <?php echo $item->created. ' / '. $item->category_title; ?>
                            <?php endif; ?>
                        
                            <?php if(!empty($item->upload)) { ?>
                                <a class="hasTooltip modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" data-original-title="<?php echo JText::_('COM_SECRETARY_DOCUMENT_DESC'); ?>" href="<?php echo "index.php?option=com_secretary&task=item.openFile&id=".$item->upload; ?>"><i class="fa fa-paperclip"></i></a>
                            <?php } ?>
                                
                            <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'documents.', $item->canCheckin); ?>
                            <?php endif; ?>
                            
                            </td>
                            
                            <td><a style="color:#333" href="<?php echo Secretary\Route::create('subject', array('id' =>  $item->subjectid)); ?>"><?php echo $item->contact_name; ?></a></td>
                            
                            <td class="right documents-list-total">
                                <span class="total-amount"><?php echo  Secretary\Utilities\Number::getNumberFormat($item->total,$item->currencySymbol); ?></span>
                                <div style="width:<?php echo (100 * $item->total / $this->maxValue); ?>%;"></div>
                            </td>
        
                            <td class="center">
                                <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
                                <?php echo Secretary\HTML::_('status.state', $item->state, $i, 'documents.', $item->canChange, $state ); ?>
                            </td>
                            
                        </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                    <tfoot class="table-list-pagination">
                        <?php 
                        if(isset($this->items[0])){
                            $colspan = count(get_object_vars($this->items[0]));
                        }
                        else{
                            $colspan = 10;
                        }
                    ?>
                    <tr>
                        <td colspan="<?php echo $colspan ?>">
                            <div class="row-fluid">
                                <div class="pull-left">
                                <?php  
                                echo $this->pagination->getListFooter(); 
                                ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
        
                <?php endif;?>
                    
            </div>
        
        </div>
        
        <div class="secretary-main-container-right">
             
            <div class="row-fluid documents-sidebar-stats">
                <h3 class="documents-sidebar-title"><?php echo JText::_('COM_SECRETARY_ERGEBNIS'); ?></h3>
                <div class="documents-sidebar-stats-status">
                <?php echo Secretary\HTML::_('documents.summary', $this->report['data'], $this->report['totat_amount']); ?>
                </div>
            </div>
  
            <?php if($filterList == 1) { ?>
            <hr class="secretary-main-area-right-hr" />
            <div class="row-fluid">
                <h3 class="documents-sidebar-title"><?php echo JText::_('COM_SECRETARY_FILTER'); ?><button class="btn headline-sidebar-button" type="submit"><?php echo JText::_('COM_SECRETARY_FILTER_DATE_GO'); ?></button></h3>
                <div class="filter-date clearfix">
                    <label><?php echo JText::_('COM_SECRETARY_TIMES_STARTDATE'); ?></label>
                    <?php echo JHTML::_('calendar', $this->state->get('start_date'), 'start_date', 'start_date', "%Y-%m-%d", array('class'=>'form-control input-date')); ?>
                </div>
                <div class="filter-date clearfix">
                    <label><?php echo JText::_('COM_SECRETARY_TIMES_ENDDATE'); ?></label>
                    <?php echo JHTML::_('calendar', $this->state->get('end_date'), 'end_date', 'end_date', "%Y-%m-%d", array('class'=>'form-control input-date')); ?>
                </div>
            </div>   
            <?php } ?>
            
            <hr class="secretary-main-area-right-hr" />
            <div class="row-fluid">
            	<div class="select-arrow">
                    <select id="documents_office" class="reloadSite">
                        <?php echo JHtml::_('select.options', $this->locations, 'id', 'title', $this->locationId, true);?>
                    </select>
				</div>   
				<?php 
				$currencyTitle = JText::_('COM_SECRETARY_CURRENCIES');
				$currOptions[] = JHtml::_('select.option', 0, JText::sprintf('COM_SECRETARY_FILTER_SELECT_LABEL_ALL', $currencyTitle) );
				foreach($this->report['currencies'] as $cur) {
				    $currOptions[] = JHtml::_('select.option', $cur, $cur );
				}
				?>
            	<div class="select-arrow">
                    <select id="documents_currency" class="reloadSite">
                        <?php echo JHtml::_('select.options', $currOptions, 'value', 'text', $this->currencyId );?>
                    </select>
				</div>
            	<div class="select-arrow">
                    <select id="filter_published" onchange="this.form.submit()" name="filter_published">
                        <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                        <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                    </select>
                </div>
                
            	<div class="select-arrow"> 
            		<select name="filter_order" onchange="this.form.submit()" >
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
                	<?php /* ?><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php */ ?>
    				<?php echo $this->pagination->getLimitBox(); ?>
                </div>
                                
			</div>   
            
            
            <?php if ( !empty($this->items) ) { ?>
             
            <?php if ($this->canDo->get('core.edit')) { ?> 
            
            <hr class="secretary-main-area-right-hr" />
            <div class="row-fluid">
                <h3 class="documents-sidebar-title"><?php echo JText::_('COM_SECRETARY_REPETITIONS'); ?></h3>
                <div class="documents-sidebar-repetitions">
                <?php if(!empty($this->itemsRepeat)) { ?>
                   <?php echo JText::sprintf('COM_SECRETARY_REPETITION_WAIT_FOR_CREATION', count($this->itemsRepeat)); ?>
                   <div class="margin-top"></div>
                   <a class="open-modal btn headline-sidebar-button" 
                   data-url="<?php echo Secretary\Route::create('documents', array('layout'=>'repetition','tmpl'=>'component')); ?>" >
                   <i class="fa fa-share-square-o"></i> <?php echo JText::_('COM_SECRETARY_SHOW'); ?>
                   </a>
                   </div>
                <?php } else { ?>
                    <?php echo JText::_('COM_SECRETARY_REPETITION_NON'); ?>
                <?php }?>
            </div>
            <?php } ?>
            
            <?php } ?>
              
        </div>
         
    	<?php echo $this->loadTemplate('batch'); ?>
    
	</div>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" id="documents_catID" />
    <input type="hidden" name="location" value="<?php echo $this->locationId; ?>" id="documents_locationID" />
    <input type="hidden" name="currency" value="<?php echo $this->currencyId; ?>" id="documents_currencyID" /> 
    <?php echo JHtml::_('form.token'); ?>

</form>
</div>

<?php echo Secretary\HTML::modal(); ?>
