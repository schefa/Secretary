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
defined( '_JEXEC' ) or die( 'Restricted access' );

$stateTitle = Secretary\Database::getQuery('status', $this->item->state,'id','title','loadResult');
$taxTypesOptions = array( 0 => 'COM_SECRETARY_NONE', 1 => 'COM_SECRETARY_INKLUSIV', 2 => 'COM_SECRETARY_EXKLUSIV');
?>

<div class="secretary-main-container">
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=document&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="document-form" class="form-validate">

	<div class="secretary-main-area entry-default">
		
        <div class="margin-bottom">
        	<a class="btn btn-default" href="<?php echo Secretary\Route::create('documents'); ?>"><i class="fa fa-angle-double-left"></i> <?php echo JText::_('COM_SECRETARY_DOCUMENTS'); ?></a>
			<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('document',$this->item->id,$this->item->created_by))) { ?>
        	<a class="btn btn-default" href="index.php?option=com_secretary&task=document.edit&id=<?php echo $this->item->id; ?>"><i class="fa fa-edit"></i> <?php echo JText::_('COM_SECRETARY_EDIT'); ?></a>
            <?php }?>
        </div>

        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active">
                <a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a>
            </li>
			<?php if(true === $this->canDo->get('core.edit') && !empty($this->item->subject[5])) {  ?>
            <li>
                <a class="btn btn-link" href="#email" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_EMAIL', true); ?></a>
            </li>
            <?php }  ?>
            <li>
                <a class="btn btn-link" href="#more" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_TAB_ERWEITERT', true); ?></a>
            </li>
            <li>
                <a onclick="window.print();return false;" class="btn btn-link" href="#" role="tab" data-toggle="tab"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/document_print_preview-20.png" />&nbsp;<?php echo JText::_('COM_SECRETARY_PRINT_THIS_PREVIEW'); ?>
                </a>
            </li>
            <?php if(COM_SECRETARY_PDF) { ?>
            <li>
                <a class="btn btn-link modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" href="<?php echo Secretary\Route::create('document', array('format'=>'pdf', 'tmpl'=>'component', 'id'=> $this->item->id)); ?>" role="tab" data-toggle="tab"><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" />&nbsp;<?php echo JText::_('COM_SECRETARY_PDF_PREVIEW'); ?></a>
            </li>
            <?php }  ?>
        </ul>
        
        <div class="tab-content">   
            <div class="tab-pane active" id="home">


<div class="row-fluid">
    <div class="col-lg-9">
    
<div class="fullwidth">
    <div class="col-md-6"> 
        <div class="fullwidth">
			<h3 class="title title-edit"><?php echo JText::_('COM_SECRETARY_SUBJECT');?></h3>
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_ANREDE'); ?></label></div>
                <div class="fullwidth secretary-controls controls"><?php echo $this->item->subject[1]; ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_STREET'); ?></label></div>
                <div class="fullwidth secretary-controls controls"><?php echo $this->item->subject[2]; ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_LOCATION'); ?></label></div>
                <div class="fullwidth secretary-controls controls"><?php echo $this->item->subject[3] .' '.$this->item->subject[4]; ?></div>
            </div>
        </div>
        <div class="row">
        	<div class="col-md-6">
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PHONE'); ?></label></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->subject[5]; ?></div>
                </div>
        	</div>
        	<div class="col-md-6">
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_EMAIL'); ?></label></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->subject[6]; ?></div>
                </div>
        	</div>
        </div> 
    </div>
    <div class="col-md-5 pull-right">
        <div class="fullwidth">
			<h3 class="title title-edit">&nbsp;</h3>
        	<div class="col-md-6">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('created');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->created; ?></div>
                </div>
        	</div>
        	<div class="col-md-6">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('nr');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->nr; ?></div>
                </div>
        	</div>
        </div>
        <div class="col-md-12">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('title');?></div>
                <div class="fullwidth secretary-controls controls"><?php echo $this->item->title; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="fullwidth margin-bottom">
	<hr>
    <div class="col-md-12">
		<h3 class="title title-edit"><?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></h3>
		<?php $items = json_decode($this->item->items, true);?>
		<table class="table">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_SECRETARY_QUANTITY');?></th>
					<th><?php echo JText::_('COM_SECRETARY_ENTITY');?></th>
					<th><?php echo JText::_('COM_SECRETARY_PRODUCT_NO');?></th>
					<th><?php echo JText::_('COM_SECRETARY_PRODUCT');?></th>
					<th class="right"><?php echo JText::_('COM_SECRETARY_EINZELPREIS');?></th>
					<th class="right"><?php echo JText::_('COM_SECRETARY_STEUERSATZ');?></th>
					<th class="right"><?php echo JText::_('COM_SECRETARY_GESAMTPREIS');?></th>
				</tr>
			</thead>
			<?php if(!empty($items)) {?>
			<tbody>
        		<?php foreach($items as $item) { ?>
        		<tr>
        			<td><?php echo $item['quantity']; ?></td>
        			<td><?php echo $item['entity']; ?></td>
        			<td><?php if(isset($item['pno'])) echo $item['pno']; ?></td>
        			<td><?php echo $item['title']; ?><br><?php echo $item['description']; ?></td>
        			<td class="right"><?php echo Secretary\Utilities\Number::getNumberFormat($item['price'],$this->item->currencySymbol); ?></td>
        			<td class="right"><?php echo $item['taxRate'].' %'; ?></td>
        			<td class="right"><?php echo Secretary\Utilities\Number::getNumberFormat($item['total'],$this->item->currencySymbol); ?></td>
        		</tr>
        		<?php } ?>
			</tbody>
			<?php } else { ?>
				<tr><td colspan="7">&nbsp;</td></tr>
			<?php } ?>
		</table>
    </div>
    
    <div class="fullwidth"><hr></div>
    
    <div class="col-md-6">
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('text');?></div>
            <div class="fullwidth secretary-controls controls"><?php echo $this->item->text; ?></div>
        </div>
    </div>
    
    <div class="col-md-4 pull-right">
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('subtotal');?></div>
            <div class="fullwidth secretary-controls controls text-right"><?php echo Secretary\Utilities\Number::getNumberFormat($this->item->subtotal,$this->item->currencySymbol); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TAX');?> (<?php echo JText::_($taxTypesOptions[$this->item->taxtype]);?>)</label></div>
            <div class="fullwidth secretary-controls controls ">
            <?php
            if($taxes = json_decode( $this->item->taxtotal,true)) {
                foreach($taxes as $taxrate => $tax) {
                    echo '<div class="fullwidth"><div class="pull-left">'.$taxrate.'%</div><div class="pull-right">'. Secretary\Utilities\Number::getNumberFormat($tax,$this->item->currencySymbol).'</div></div>';
                }
            }
            ?>
            </div>
        </div>
    	<?php if($this->item->rabatt > 0) { ?>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('rabatt');?></div>
            <div class="fullwidth secretary-controls controls text-right">- <?php echo Secretary\Utilities\Number::getNumberFormat($this->item->rabatt,$this->item->currencySymbol); ?></div>
        </div>
        <?php } ?>
        <div class="control-group">
            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TOTAL');?></label></div>
            <div class="fullwidth secretary-controls controls text-right"><strong><?php echo Secretary\Utilities\Number::getNumberFormat($this->item->total,$this->item->currencySymbol); ?></strong></div>
        </div>
    </div>

</div>

    </div>
    <div class="col-lg-3">
        <div class="secretary-document-bottom">
            <div class="secretary-document-template">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('template');?></div>
                    <div class="fullwidth secretary-controls controls"><?php if(isset($this->defaultTemplate->title)) echo $this->defaultTemplate->title; else echo JText::_('COM_SECRETARY_NONE'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_EMAIL_TEMPLATE');?></label></div>
                    <div class="fullwidth secretary-controls controls">
                    <?php if(isset($this->emailTemplate->title)) echo $this->emailTemplate->title; else echo JText::_('COM_SECRETARY_NONE'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('office');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->office; ?></div>
                </div>
            </div>
            <div class="secretary-document-upload">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('upload');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->upload; ?></div>
                </div>
            </div>
            <div class="secretary-document-zahlung">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('state');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo JText::_( $stateTitle ); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('deadline');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo $this->item->deadline; ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('paid');?></div>
                    <div class="fullwidth secretary-controls controls"><?php echo round($this->item->paid,2).' '. $this->item->currencySymbol; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

		</div>
            
		<?php if(!empty($this->item->subject[5])) {  ?>
            <div class="tab-pane" id="email">
                <?php require(SECRETARY_ADMIN_PATH.'/views/document/tmpl/email.php');?>
            </div>
        <?php } ?>
        
            <div class="tab-pane" id="more"> 
            
            <div class="fullwidth">
                <h3 class="title"><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
                <div class="fields-items form-horizontal">
                    <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                        <?php foreach($fields as $field) { ?>
                                    
                            <div class="control-group">
                                <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                                <div class="controls"><?php echo Secretary\Utilities::cleaner($field[2],true); ?></div>
                            </div>
    
                        <?php } ?>
                    <?php } ?>
                </div>
                
            </div>
            </div>
        </div>
        
    </div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
    </div>
    