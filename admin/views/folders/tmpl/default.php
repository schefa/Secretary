<?php
/**
 * @version     3.2.0
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

$title = (isset($this->extension)) ? JText::_('COM_SECRETARY_CATEGORIES_'.strtoupper($this->extension)) : JText::_('COM_SECRETARY_CATEGORIES');
$title_items = (isset($this->extension)) ? JText::_('COM_SECRETARY_'.strtoupper($this->extension)) : JText::_('COM_SECRETARY_ITEMS');

$sections = array('documents','subjects','products','times','messages','markets','templates');
?>

<div class="secretary-main-container">
    
    <div class="secretary-main-area">
    <ul class="categories-tabs">
    <?php 
    foreach($sections as $section) {
        $active = ($section == $this->extension) ? 'active' : '';
        echo '<li class="'.$active.'"><a href="'.\Secretary\Route::create('folders',array('extension'=>$section)).'">'. JText::_('COM_SECRETARY_'.$section ) .'</a></li>';
    } 
    ?>
    </ul>
    </div>

    <form action="<?php echo JRoute::_('index.php?option=com_secretary&view=folders');?>" method="post" name="adminForm" id="adminForm">
    
		<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>
        
        <div class="secretary-main-area">
            
        <div class="fullwidth">
            <div class="pull-left">
                <h2 class="documents-title">
                    <span class="documents-title-first">
                    <?php echo $title; ?>
                    </span>
                    <span class="documents-title-second">
                <?php if($this->extension == 'newsletters') { ?>
                    <a href="index.php?option=com_secretary&view=templates&extension=newsletters&catid=0">
                        <?php echo JText::_('COM_SECRETARY_ALL') .'&nbsp;'. JText::_('COM_SECRETARY_NEWSLETTER');?>
                    </a>
                <?php } elseif(isset($this->extension)) { ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CATEGORIES_ELEMENTS_DESC'); ?>"  
                    href="<?php echo JRoute::_('index.php?option=com_secretary&view='.$this->extension.'&catid=0'); ?>">
                    <?php echo JText::_('COM_SECRETARY_ALL') .'&nbsp;'. $title_items;?></a>
                <?php } ?>
                    </span>
                </h2>
            </div>
            
            <div class="pull-right">
            <div class="secretary-search btn-group">
                <input type="text" name="filter_search" id="filter_search" class="form-control" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
                <button class="btn btn-default hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="fa fa-search"></i></button>
                <button class="btn btn-default hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
            </div>
            </div>

        </div>
        
        <hr />
        
        <div class="secretary-toolbar clearfix">
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
                            <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.published'), true);?>
                        </select>
                    </div>
                </div>
                <div class="btn-group">
                    <div class="select-arrow select-arrow-white">
                        <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('COM_SECRETARY_SORT_BY');?></option>
                            <?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?>
                        </select>
                    </div>
                </div>
            </div>
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
                        <th width="1%" class="nowrap center">
                            <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th>
                            <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th><?php echo JText::_('COM_SECRETARY_'.$this->extension); ?>
                        </th>
                        <?php if($this->extension == 'newsletters') { ?>
                        <th width="1%"><?php echo JText::_('COM_SECRETARY_NEWSLETTER_SUBS'); ?></th>
                        <?php } ?>
                        <th width="1%">
                            <?php echo JText::_('COM_SECRETARY_ORDERING'); ?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone">
                            <?php echo JText::_('JGRID_HEADING_ID'); ?>
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
                        <td class="center">
                            <?php echo Secretary\HTML::_('status.state', $item->state, $i, 'folders.', $item->canChange ); ?>
                        </td>
                        <td>
                            <?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1) ?>
                            <?php if ($item->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'folders.', $item->canCheckin); ?>
                            <?php endif; ?>
                            <?php if ($item->canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_secretary&task=folder.edit&id='.$item->id.'&extension='.$item->extension);?>">
                                    <?php echo $this->escape($item->title); ?></a>
                            <?php else : ?>
                                <?php echo $this->escape($item->title); ?>
                            <?php endif; ?>
                        </td>
                        
                        <?php if($this->extension == 'newsletters') { ?>
                        <td>
                            <a class="btn btn-small" href="index.php?option=com_secretary&view=templates&extension=newsletters&catid=<?php echo $item->id;?>">
                                <?php echo JText::_("COM_SECRETARY_NEWSLETTER_OF") ." ". $item->title;?>
                            </a>
                        </td>
                        <td>
                        <?php
						
						$contacts = Secretary\Database::getQuery('newsletter',$item->id,'listID','COUNT(*)','loadResult');
						echo (int) $contacts;
						?>
                        </td>
                        <?php } else { ?>
                        <td>
                            <a class="btn btn-small" href="<?php echo Secretary\Route::create($item->extension, array('catid'=>$item->id));?>">
                                <?php echo JText::_("COM_SECRETARY_ALL") ." ". $item->title;?>&nbsp;<span class="fa fa-angle-double-right"></span>
                            </a>
                        </td>
                        <?php }  ?>
                        
                        <td><?php echo ($item->ordering); ?></td>
                        
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php endforeach; ?>
                
                </tbody>
                <tfoot class="table-list-pagination">
                    <tr>
                        <td colspan="15">
                        <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        
        <?php endif;?>
        </div>
        
        <?php echo $this->loadTemplate('batch'); ?>
        <input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
    
</div>
