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

$user		= \Secretary\Joomla::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$business	= Secretary\Application::company();

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = JFormHelper::loadFieldType('SecretarySections', false)->getIcons();

$areas = array('documents','subjects','messages','newsletters','products');
?>

<div class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=templates'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
    
    <?php if ($this->canDo->get('core.show')) { ?>
	<div class="secretary-main-area">
    
		<div class="row-fluid fullwidth">
			<div class="pull-left">
        		<h2 class="documents-title">
                    <span class="documents-title-first"><?php echo $this->title; ?></span>
            		<?php if($this->extension == 'newsletters') { ?>
                    <span class="documents-title-second">
                    	<a href="<?php echo JRoute::_('index.php?option=com_secretary&view=folders&extension=newsletters'); ?>"><?php echo JText::_('COM_SECRETARY_CATEGORIES_NEWSLETTERS');?></a>
                    </span>
                    <?php } else { ?>
                    <span class="documents-title-second">
                    	<a href="<?php echo JRoute::_('index.php?option=com_secretary&view=folders&extension=templates'); ?>"><?php echo JText::_('COM_SECRETARY_CATEGORIES');?></a>
                    </span>
                    <?php } ?>
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
        <div class="btn-toolbar">
            <div class="btn-group">
            
            	<?php foreach($areas AS $area ) { ?>
                <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=templates&extension='.$area); ?>" class="btn btn-large <?php if($this->extension === $area) echo 'active'; ?>"><?php echo JText::_('COM_SECRETARY_'.$area);?></a>
                <?php } ?>
            </div>
        </div>
      
        <hr />
         
        
		<div class="secretary-toolbar clearfix">
            <div class="select-arrow-bg select-arrow-toolbar">
                <div class="select-arrow">
                    <select id="select_category">
                        <?php echo JHtml::_('select.options', $this->categories, 'id', 'title', $this->categoryId, true);?>
                    </select>
                </div>
            </div>
			<?php $this->addToolbar(); ?>
            <div class="pull-right">
                <div class="btn-group">
                	<div class="select-small select-arrow select-arrow-white">
                    <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                        <select id="filter_published" onchange="this.form.submit()" name="filter_published">
                            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                            <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
                        </select>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                    <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?></select>
                    </div>
                </div>
            </div>
		</div>
        
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
        
		<table class="table table-hover" id="documentsList">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
                    	<?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
                    </th>
                    <th class='left'>
                    	<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_NAME', 'a.title', $listDirn, $listOrder); ?>
					</th>
                    <th class='left'>
                    	<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_DESCRIPTION', 'a.text', $listDirn, $listOrder); ?>
                    </th>
                    <?php if($this->extension == 'newsletter') { ?>
                    	<th class='left'><?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></th>
                    <?php } else { ?>
                    <th class='left'>
                    	<?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $listDirn, $listOrder); ?>
                    </th>
                    <?php } ?>
                    <th class='left'>
						<?php $categoryTitle = ($this->extension == 'newsletter') ? 'COM_SECRETARY_LIST' : 'COM_SECRETARY_CATEGORY'; ?>
                    	<?php echo JHtml::_('grid.sort',$categoryTitle, 'category', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                    	<?php echo JHtml::_('grid.sort',  '<i class="fa fa-globe"></i>', 'a.language', $listDirn, $listOrder); ?>
                    </th>
				</tr>
			</thead>
			<tbody>
			<?php
				$cntBusinessTemplate = true;
			
				foreach ($this->items as $i => $item) :
			
					$canChange = false; $canCheckin = false; $canEdit = false;
					if($user->authorise('core.edit', 'com_secretary.template')) {
						$canEdit = true; $canChange	= true; $canCheckin = true;
					}
					if(!$canCheckin) $canCheckin = $user->authorise('core.admin', 'com_secretary');
					if(!$canChange) $canChange = $user->authorise('core.edit.state', 'com_secretary.template');
					
					if($business['id'] != $item->business) {
						$businessTemplate = 'no-business-template';
						$cntBusinessTemplate = false;
					} else {
						$businessTemplate = '';
					}
				?>
				<tr class="row<?php echo $i % 2 . ' '. $businessTemplate; ?>">
                    
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        <span class="lbl"></span>
					</td>
                    
                    <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    
                    <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
					<td><?php echo Secretary\HTML::_('status.state', $item, $i, 'templates', $canChange, $state ); ?></td>
                    
                    <td width="1%">
						<?php if(COM_SECRETARY_PDF) { ?>
                            <?php $href = Secretary\Route::create('template', array('id' => $item->id, 'format' => 'pdf')); ?>
                            <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
                        <?php } ?>
                    </td>
                    <td>
                    	<?php $extension = (!empty($item->extension)) ? "&extension=".$item->extension : ""; ?>
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'templates.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit) : ?>
                            <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="index.php?option=com_secretary&task=template.edit&id=<?php echo (int) $item->id . $extension; ?>">
                            <?php echo JText::_($item->title); ?></a>
                        <?php else : ?>
                            <?php echo JText::_($item->title); ?>
                        <?php endif; ?>
                    </td>
                    
                    <td><?php echo substr(htmlentities( $item->text), 0, 150); ?>…</td>
                    
                    <?php if($this->extension == 'newsletter' OR $item->extension =='newsletter') { ?>
                    <td>
                    <?php 
					$catData = Secretary\Database::getQuery('folders',$item->catid,'id','fields','loadResult');
					if($fields = json_decode($catData)) {
						echo count($fields ?? []);	
					}
					?>
                    </td>
                    <?php } else { ?>
					<td>
						<?php echo $modules[$item->extension]; ?>
                    </td>
                    <?php } ?>
                    
					<td><?php echo $item->category; ?></td>
					<td><img src="<?php echo JURI::root(). '/media/mod_languages/images/'. substr($item->language, 0, 2) .'.gif'; ?>" /></td>
                    
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
		</table>
        
    <?php if(!$cntBusinessTemplate) { ?>
    	<div class="fullwidth template-not-assigned">
            <span class="msg no-business-template"></span>
            <span><?php echo JText::_('COM_SECRETARY_TEMPLATE_NOT_FOR_BUSINESS'); ?></span>
        </div>
    <?php } ?>
    
		<?php endif;?>

	</div>
    
	<?php } else { ?>
        <div class="alert alert-danger"><?php echo JText::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?>  
    
	<?php echo $this->loadTemplate('batch'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="catid" value="<?php echo $this->categoryId; ?>" />
    <input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>  
		
    </div>
