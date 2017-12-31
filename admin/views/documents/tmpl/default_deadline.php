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
?>

<div class="secretary-main-area deadline-module">

    <h3><?php echo JText::_('COM_SECRETARY_DEADLINE_EXCEEDED'); ?></h3>

    <table class="table">
    	<thead>
            <tr>
                <th class='center'>
                <?php echo JText::_('COM_SECRETARY_NR'); ?>
                </th>
                <th class='left'>
                </th>
                <th class='left'>
                <?php echo JText::_('COM_SECRETARY_DEADLINE'); ?>
                </th>
                <th class='left'>
                <?php echo JText::_('COM_SECRETARY_DATE') ." / ". $this->report['alias'] ; ?>
                </th> 
                <th class='left'>
                <?php echo JText::_('COM_SECRETARY_SUBJECT'); ?>
                </th>
                <th class='right'>
                <?php echo JText::_('COM_SECRETARY_PAIDAMOUNT'); ?>
                </th>
                <th class='right'>
                <?php echo JText::_('COM_SECRETARY_TOTAL'); ?>
                </th>
                <th width="1%" class="nowrap center">
                <?php echo JText::_('JSTATUS'); ?>
                </th>
                <th class="secretary-deadline-td"></th>
            </tr>
        </thead>
        <tbody>
    	
    	<?php foreach ($this->itemsExpired as $i => $item) : ?>
    
		<?php $item->category_title = empty($item->category_title) ? JText::_('COM_SECRETARY_DOCUMENT') : JText::_($item->category_title); ?>
        <tr class="row<?php echo $i % 2 ; ?>">
            
            <td class="center">
                <?php if ( !empty($item->nr)) : ?>
                    <?php echo $item->nr; ?>
                <?php elseif ( empty($item->nr)) : ?>
                    <?php echo " - "; ?>
                <?php endif; ?>
            </td>
            
            <td class="left">
            
            <?php if(COM_SECRETARY_PDF && $item->template > 0) { ?>
                <?php $href = Secretary\Route::create('document', array('id' => $item->id, 'format' => 'pdf')); ?>
                <a class="hasTooltip printpdf modal" href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW') ; ?>" rel="{size: {x: 900, y: 500}, handler:'iframe'}"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" /></a>
            <?php } ?>
            
                <?php $href = Secretary\Route::create('document', array('id' => $item->id, 'layout' => 'template', 'tmpl' => 'component')); ?>
                <a href="<?php echo $href; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PREVIEW'); ?>" class="hasTooltip printpdf modal" rel="{size: {x: 800, y: 500}, handler:'iframe'}" id="modalLink1"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/document_print_preview-20.png" /></a>
                
                <?php /*if(!empty($item->email)) { ?>
                <?php $email = 'index.php?option=com_secretary&view=document&task=email&id='.$item->id ; ?>
                <a class="hasTooltip printpdf" href="<?php echo $email; ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_EMAIL'); ?>"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/email-25.png" /></a>
                <?php }*/ ?>
                
            </td>
            
            <td class="left"><?php echo $item->deadline; ?>
            </td>
            
            <td class="left">
            
            <?php $created = '<br/>'.  JText::_('COM_SECRETARY_CREATED') .' '. date('H:i:s d.m.Y', $item->createdEntry); ?>
            
            <?php if ($item->canEdit) : ?>
                <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'). $created; ?>"  href="<?php echo Secretary\Route::create(false, array('task'=> 'document.edit', 'id' => (int) $item->id, 'catid' => (int) $item->catid)); ?>">
                <?php echo $item->created. ' / '. $item->category_title; ?></a>
            <?php else : ?>
                <?php echo $item->created. ' / '. $item->category_title; ?>
            <?php endif; ?>
            
            </td>
            
            <td><?php echo $item->contact_name; ?></td>
            
            <td class="right documents-list-total">
                <span class="paid-amount"><?php echo Secretary\Utilities\Number::getNumberFormat($item->paid,$item->currencySymbol); ?></span>
            </td>
            <td class="right documents-list-total">
                <span class="total-amount"><?php echo Secretary\Utilities\Number::getNumberFormat($item->total,$item->currencySymbol); ?></span>
            </td>

            <td class="center">
                <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
                <?php echo Secretary\HTML::_('status.state', $item->state, $i, 'documents.', $item->canChange, $state ); ?>
            </td>

            <td class="center secretary-deadline-td">
                <?php if ($item->canChange) {
                    echo '<a href="index.php?option=com_secretary&task=documents.acquit&cid='.$item->id.'&catid='.$item->catid.'" class="hasTooltip secretary-deadline-status " data-original-title="'.JText::_('COM_SECRETARY_PAIDUP_DESC').'">'. JText::_('COM_SECRETARY_PAIDUP') .'</a>';
                }
                ?>	
            </td>
            
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
</div>           