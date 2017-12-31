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
$user	= Secretary\Joomla::getUser();
$userId	= $user->get('id');
$business	= Secretary\Application::company();
$currency 	= $business['currency'];

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$category = (!empty($this->accountId)) ? '&catid='.$this->accountId : '&catid=0';
?>

<div class="secretary-main-container">

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=accountings'.$category); ?>" method="post" name="adminForm" id="adminForm">

    <?php if ($this->canDo->get('core.show')) { ?>
    
    <div class="secretary-main-area">
    
    <div class="row-fluid clearfix">
        <div class="pull-left">
            <h2 class="documents-title">
                <span class="documents-title-first"><?php echo $this->title; ?></span>
                <?php if( $this->extension !== 'accounts') { ?>
                <span class="documents-title-second">
                    <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=accountings&extension=accounts'); ?>"><?php echo JText::_('COM_SECRETARY_ACCOUNTS');?></a>
                </span>
                <?php  } if( $this->extension !== 'accounts_system') { ?>
                <span class="documents-title-second">
                    <a href="<?php echo JRoute::_('index.php?option=com_secretary&view=accountings&extension=accounts_system'); ?>"><?php echo JText::_('COM_SECRETARY_ACCOUNTS_SYSTEM');?></a>
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
    
    <div class="row-fluid secretary-toolbar clearfix">
    
    	<?php if ($this->extension != 'accounts_system') { ?>
        <div class="select-arrow-bg select-arrow-toolbar">
            <div class="select-arrow">
            	<select id="documents_category">
                    <option value="0"><?php echo JText::_('COM_SECRETARY_ACCOUNTS_ALL'); ?></option>
                    <?php echo JHtml::_('select.options', $this->accounts, 'value', 'text', $this->accountId, true);?>
                </select>
            </div>
        </div>
        <?php } ?>
        <?php $this->addToolbar(); ?>
    </div>
    
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
        <?php echo $this->loadTemplate($this->extension)?>
    <?php endif;?>
    
	<?php echo $this->loadTemplate('batch'); ?>
    
	<?php } else { ?>
        <div class="alert alert-danger"><?php echo JText::_('JERROR_ALERTNOAUTHOR'); ?></div>
	<?php } ?> 
     
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
    <input type="hidden" name="account" value="<?php echo $this->accountId; ?>" id="documents_catID" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
    
    </div>
</form>

</div>
</div>