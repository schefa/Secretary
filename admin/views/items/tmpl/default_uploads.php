<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');
$modules = JFormHelper::loadFieldType('SecretarySections', false)->getIcons();

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage', 'com_secretary');

?>

<?php if (empty($this->items)) : ?>
    <div class="alert alert-no-items">
        <?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
    </div>
<?php else : ?>
<table class="table table-hover">
    <thead>
        <tr>
        
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            
            <?php if (isset($this->items[0]->id)): ?>
            <th width="1%" class="nowrap center hidden-phone">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <?php endif; ?>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ENTRY', 'a.itemID', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SECTION', 'a.extension', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_BUSINESS', 'a.business', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
            </th>
            <th class='left'></th>
        
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
        <tr class="row<?php echo $i % 2; ?>">
            
            <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <span class="lbl"></span>
            </td>
            
            <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
            
            <td>
                <?php if ($canCheckin) : ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=item&layout=edit&id='.(int) $item->id .'&extension='.$this->extension ); ?>">
                    <?php echo $item->title; ?></a>
                <?php else : ?>
                    <?php echo $item->title; ?>
                <?php endif; ?>
            </td>
            
            <?php
				if($item->extension == 'system') $item->extension = 'documents';
				$ext = Secretary\Application::getSingularSection($item->extension);
				if($item->itemID > 0) 	{
					$link = JRoute::_('index.php?option=com_secretary&view='.$ext.'&id='.$item->itemID);
					$title = JText::_('COM_SECRETARY_'.strtoupper($ext)) .' ('. $item->itemID.')';
				} else {
					$link = JRoute::_('index.php?option=com_secretary&task='.$ext.'.edit&secf='.$item->id) .'" class="btn btn-default';
					$addEntryText = JText::_('COM_SECRETARY_'.strtoupper($ext));
					$title = JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR', $addEntryText);
				}
			?>
            <td>
            	<a href="<?php echo $link; ?>"><?php echo $title; ?></a>
            </td>
            
            <td><?php echo $modules[$item->extension]; ?></td>
            <td><?php echo Secretary\Database::getQuery('businesses',$item->business,'id','title','loadResult'); ?></td>
            
            <td><?php \Secretary\Helpers\Uploads::getUploadFile($item, '', 40); ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-list-pagination">
    <tr>
        <td colspan="7">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFieldsUploads(), 'value', 'text', $this->state->get('list.ordering'));?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
</table>
<?php endif; ?>


<?php if (!empty($this->extraItems)) : ?>

<div class="pull-left secretary-toolbar">
    <div class="select-arrow-toolbar">
    <h2 class="documents-title">
    	<span class="documents-title-first"><?php echo JText::_('COM_SECRETARY_FILES_AUTOMATIC_GENERATED'); ?></span>
    </h2>
    </div>
    <div class="select-arrow-toolbar-next">
        &#10095; 
    </div>
	<?php echo Secretary\Navigation::ToolbarItem('items.deleteFiles', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default', 'fa-trash'); ?>
</div>

<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            <th><?php echo JText::_('COM_SECRETARY_TITLE') ?></th>
    	</tr>
	</thead>
	<tbody>
    <?php foreach ($this->extraItems as $i => $item) : ?>
    <tr>
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->file); ?>
            <span class="lbl"></span>
        </td>
    	<td>
    	<?php if(COM_SECRETARY_PDF) { ?>
        <?php $href = (isset($item->document->id)) ? Secretary\Route::create('document', array('id' => $item->document->id, 'format' => 'pdf')) : ''; ?>
        <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo JURI::root(); ?>/media/secretary/images/pdf-20.png" /></a>
        <?php } ?><?php echo $item->title; ?></td>
    </tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
