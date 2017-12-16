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

$user = JFactory::getUser(); 
  
$app	= JFactory::getApplication();
$toggleTaxRateColumn= (int) $app->getUserState('filter.toggleTaxRateColumn', 1);
$taxSelection= ($toggleTaxRateColumn== 0) ? '' : ' taxSelection';
?>

<div class="secretary-main-container">
    
<?php echo Secretary\HTML::_('datafields.item'); ?>
<?php echo $this->loadTemplate('item');?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=document&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

<div class="secretary-main-area secretary-document">

    <div class="secretary-document-padding">
        <div class="secretary-toolbar fullwidth">
            <div class="select-arrow-toolbar "><?php echo $this->form->getInput('catid');?></div>
            <div class="select-arrow-toolbar-next">&#10095;</div>
            <?php $this->addToolbar(); ?>
        </div>
        
    	<ul class="nav nav-tabs fullwidth margin-bottom" id="myTab" role="tablist">
        <li class="active">
            <a class="btn btn-link" href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a>
        </li>
        <li>
            <a class="btn btn-link" href="#more" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_TAB_ERWEITERT', true); ?></a>
        </li>
        
        <?php if(($this->item->id > 0) && $user->authorise('core.show', 'com_secretary.accounting')) { ?>
            <li>
                <a class="btn btn-link" href="#accounting" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_ACCOUNTING', true); ?></a>
            </li>
		<?php } ?>
        
        <?php if ($user->authorise('core.admin', 'com_secretary')) { ?>
            <li><a class="btn btn-link" href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
        <?php } ?>
        
        <?php if($this->item->id > 0) { ?>  
        
            <?php if(COM_SECRETARY_PDF && !empty($this->defaultTemplate)) { ?>
            <li class="pull-right secretary-document-pdf-print">
                <a class="btn btn-link btn-pdf modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" href="<?php echo Secretary\Route::create('document', array('format'=>'pdf', 'id'=> $this->item->id )); ?>" role="tab" data-toggle="tab"><img src="<?php echo JURI::root(); ?>/media/secretary/images/pdf-20.png" />&nbsp;<?php echo JText::_('PDF'); ?></a>
            </li>
            <?php }  ?>
            
        <?php if(\Secretary\Helpers\Access::checkAdmin() && !empty($this->item->subject[6])) {  ?>
            <li class="pull-right">
                <a class="btn btn-link open-modal" data-url="<?php echo Secretary\Route::create('document', array('layout'=>'email','format'=>'raw','tmpl'=>'component', 'id'=> $this->item->id )); ?>"><img src="<?php echo JURI::root(); ?>/media/secretary/images/email-25.png" />&nbsp;<?php echo JText::_('COM_SECRETARY_EMAIL'); ?></a> 
            </li>
        <?php } ?>
        
            <li class="pull-right">
            	<a class="btn btn-link open-modal" data-url="<?php echo Secretary\Route::create('document', array('layout'=>'preview','format'=>'raw','tmpl'=>'component', 'id'=> $this->item->id )); ?>"><img src="<?php echo JURI::root(); ?>/media/secretary/images/document_print_preview-20.png" />&nbsp;<?php echo JText::_('COM_SECRETARY_PREVIEW'); ?></a> 
            </li>
            
        <?php } ?>
        
    </ul>
    
    </div>
        
    <div class="tab-content">   
        <div class="tab-pane active form" id="home">
        
            <div class="row-fluid">
            <div class="col-lg-9">
            <div class="secretary-document-main">
            
            <div class="table-item-header fullwidth">
                
                <div class="pull-right secretary-documents-datetitle">
                    <div class="row-fluid">
            			<h4 class="title"><?php echo JText::_('&nbsp;'); ?></h4>
                        <div class="secretary-control-group row">
                            <div class="pull-left col-md-6">
                            <?php echo $this->form->getLabel('created');?>
                            <div class="controls"><?php echo $this->form->getInput('created');?></div>		
                            </div>
                            <div id="ajaxNumber" class="col-md-6" <?php if($this->item->id) echo 'data-id="'.$this->item->id.'" data-catid="'.$this->item->catid.'"'; ?>>
                            <?php echo $this->form->getLabel('nr');?>
                            <div class="controls">
       							<?php echo $this->form->getInput('nr'); ?>
                                <div id="ajaxNumberResult"></div>
                            </div>		
                            </div>
                        </div>
                        
                        <div class="secretary-control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('title');?></div>
                            <div class="controls"><?php echo $this->form->getInput('title');?></div>								
                        </div>
                    </div>	
                </div>	
                
                <div class="pull-left secretary-documents-contact">
                
                <?php if(!$this->multiple_subjects || count($this->jsonSubjects) <= 1) { ?>
                    <h4 class="title"><?php echo JText::_('COM_SECRETARY_SUBJECT') . Secretary\HTML::_('search.contacts' ); ?></h4>
                
                    <div class="fullwidth secretary-control-group">
                        <div class="secretary-control-group-gender">
                            <label class="control-label"><?php echo JText::_('COM_SECRETARY_ANREDE'); ?></label>
                            <?php echo $this->genderoptions; ?>
                        </div>
                        
                        <div class="secretary-control-group-name ui-widget">
                            <label class="control-label"><?php echo JText::_('COM_SECRETARY_NAME'); ?></label>
                            <div id="display_contact_name" <?php if(empty($this->item->subject[1])) { echo 'style="display:none"'; } ?>><span id="contact_name"><?php echo $this->item->subject[1]; ?></span><span class="clean-contact">x</span></div>
                            <input id="jform_subject_name" <?php if(!empty($this->item->subject[1])) { echo 'style="display:none"'; } ?> type="text" name="jform[subject][1]"	value="<?php echo $this->item->subject[1]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_NAME'); ?>" class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_NAME'); ?>" />
                        </div>
                        
                        <?php echo $this->subjectConnections; ?>
                        
                    </div>

                    <div class="secretary-control-group">
                        <div class="secretary-input-prepend clearfix">
                            <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_STREET'); ?></div>
                            <input id="jform_subject_street" type="text" name="jform[subject][2]"	value="<?php echo $this->item->subject[2]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_STREET'); ?>" class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_STREET'); ?>" />
                        </div>

                    <table border="0" >
                        <tr>
                            <td width="36%">
                            <div class="secretary-input-prepend clearfix">
                                <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_POSTCODE'); ?></div>
                                <input id="jform_subject_zip" type="text" name="jform[subject][3]"	value="<?php echo $this->item->subject[3]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_POSTCODE'); ?>" class="search-subject-zip hasTooltip input-border-radius-left" data-original-title="<?php echo JText::_('COM_SECRETARY_POSTCODE'); ?>" />
                            </div>
                            </td>
                            <td width="4%">
                            </td>
                            <td width="60%">
                            <div class="secretary-input-prepend clearfix">
                                <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_LOCATION'); ?></div>
                                <input id="jform_subject_location" type="text" name="jform[subject][4]"	value="<?php echo $this->item->subject[4]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_LOCATION'); ?>" class="search-subject-location hasTooltip input-border-radius-left" data-original-title="<?php echo JText::_('COM_SECRETARY_LOCATION'); ?>" />
                            </div>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="clearfix">             
                        <div class="secretary-control-group-left">         
                            <div class="secretary-input-prepend clearfix">
                                <div class="secretary-add-on"><span class="fa fa-phone"></span></div>
                                <input class="hasTooltip" id="jform_subject_phone" type="text" name="jform[subject][5]" value="<?php echo $this->item->subject[5]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_PHONE'); ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_PHONE'); ?>">
                            </div>
                        </div>
                        <div class="secretary-control-group-right">
                            <div class="secretary-input-prepend clearfix">
                                <div class="secretary-add-on"><span class="fa fa-envelope-o"></span></div>
                                <input class="hasTooltip" id="jform_subject_email" type="text" name="jform[subject][6]" value="<?php if(isset($this->item->subject[6])) echo $this->item->subject[6]; ?>" placeholder="<?php echo JText::_('COM_SECRETARY_EMAIL'); ?>" data-original-title="<?php echo JText::_('COM_SECRETARY_EMAIL'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    </div>
                    
                    <?php echo $this->form->getInput('subjectid');?>
                    
            	<?php } else { ?>
            		<h4 class="title"><?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></h4>
            		<?php 
            		
            		foreach($this->jsonSubjects as $x => $id) {   
            		    $contact = Secretary\Database::getQuery('subjects',$id,'id','firstname,lastname','loadAssoc');
            		    if($x > 0)
            		        echo ', ';
            		    echo $contact['firstname'] .' '. $contact['lastname'];
            		}
            		?>
            		<input name="subject" type="hidden" value="<?php echo $this->subjects; ?>" />
            	<?php } ?>
                    
                </div>
            </div>
            
            <div class="secretary-documents-table-items <?php echo $taxSelection ?>">
            
            	<h4 class="title"><?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></h4>
                    
                <div class="table-item-th">
                    <div class="row-fluid table-items clearfix">
                    	<div class="table-item-col-0">&nbsp;</div>
                        <div class="table-item-col-1">
                            <div class="row">
                                <div class="col-md-6 text-right"><?php echo JText::_('COM_SECRETARY_QUANTITY');?></div>
                                <div class=""><?php echo JText::_('COM_SECRETARY_ENTITY');?></div>
                            </div>
                        </div>
                        <div class="table-item-col-pno"><?php echo JText::_('COM_SECRETARY_PRODUCT_NO');?></div>
                        <div class="table-item-col-2"><?php echo JText::_('COM_SECRETARY_PRODUCT');?></div>
                        <div class="table-item-col-3 text-center"><?php echo JText::_('COM_SECRETARY_EINZELPREIS');?></div>	
                        <div class="table-item-col-4 text-center" style="display: <?php echo (!empty($taxSelection) ? 'block' : 'none') ?>"><?php echo JText::_('COM_SECRETARY_STEUERSATZ');?></div>
                        <div class="table-item-col-5 text-center"><?php echo JText::_('COM_SECRETARY_GESAMTPREIS');?></div>
                    </div>
                </div>
                
                <div class="row-fluid table-items-list dd">
                	<ol class="dd-list"></ol>
                </div>
                
                <div class="secretary-documents-table-bottom">
                    <span class="btn btn-default item-counter" id="item-add" counter="<?php echo $this->countParameters; ?>">
                        <i class="fa fa-plus"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_ENTRY_ADD_NEW_ROW'); ?>
                    </span>
                    <span class="btn btn-no-bg <?php echo (!empty($taxSelection) ? 'active' : ' ') ?>" id="item-toggle-tax"><?php echo JText::_('COM_SECRETARY_TAXRATE_EDIT'); ?></span>
                    <span class="btn btn-no-bg" id="item-toggle-pno"><?php echo JText::_('COM_SECRETARY_PRODUCT_NO_TOGGLE'); ?></span>
                    
                    <span class="btn btn-no-bg item-counter" id="item-add-document" counter="<?php echo $this->countParameters; ?>"><?php echo JText::_('COM_SECRETARY_ENTRY_ADD_NEW_ROW_DOCUMENT'); ?></span>
                </div>
            
            </div>
            
            <?php echo $this->loadTemplate('details_footer');?>
            </div>
            </div>
            
            <div class="col-lg-3">
                <div class="secretary-document-bottom">

                    <div class="secretary-document-template">
                        
        				<?php if($this->state->params->get('documents_frontend') == 1 && !empty($this->item->id) && $this->item->template > 0) { 
							$key = md5($this->item->id . $this->item->createdEntry . $this->item->subjectid . $this->item->total);
							$fontendlink = JURI::root(). 'index.php?option=com_secretary&view=document&id='.$this->item->id.'&key='.$key;
						?>
                        <div class="control-group">
                        	<p class="secretary-desc"><a href="<?php echo $fontendlink ?>" target="_blank"><?php echo $fontendlink; ?></a></p>
                        </div>
                        <hr />
            			<?php } ?>
                        
                        <div class="control-group">
                        <h4 class="title"><?php echo JText::_('COM_SECRETARY_TEMPLATE');?>&nbsp;<a href="index.php?option=com_secretary&view=templates&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                        <?php echo $this->itemtemplates; ?>
                        </div>
                                            
                        <?php if(isset($this->item->message['template'])) { ?>
                        <div class="control-group">
                        	<h4 class="title"><?php echo JText::_('COM_SECRETARY_EMAIL');?>&nbsp;<?php echo JText::_('COM_SECRETARY_TEMPLATE');?></h4>
                                    
                            <?php echo $this->emailtemplates; ?>
                            
                            <?php if(isset($this->item->message['subject'])) { ?>
                            <input type="hidden" name="jform[fields][message][subject]" value="<?php echo $this->escape($this->item->message['subject']); ?>" /><?php } ?>
                            
                            <?php if(isset($this->item->message['id'])) { ?>
                            <input type="hidden" name="jform[fields][message][id]" value="<?php echo $this->item->message['id']; ?>" /><?php } ?>
                        
                            <?php if(isset($this->item->message['text'])) { ?>
                            <input type="hidden" name="jform[fields][message][text]" value="<?php echo $this->escape($this->item->message['text']); ?>" /><?php } ?>
                        
                            <?php if(isset($this->item->message['emailed'])) { ?>
                            <input type="hidden" name="jform[fields][message][emailed]" value="<?php echo $this->item->message['emailed']; ?>" /><?php } ?>
                            
                        </div>
                        <?php } ?>
                        <hr />
                        <div class="control-group">
                            <h4 class="title"><?php echo JText::_('COM_SECRETARY_LOCATION_DOCUMENTS'); ?>&nbsp;<a href="index.php?option=com_secretary&view=locations&extension=documents" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                            <div class="controls"><?php echo $this->form->getInput('office');?></div>								
                        </div>
                             
                    </div>
                    
                    <div class="secretary-document-upload">
                        <h4 class="title"><?php echo JText::_('COM_SECRETARY_DOCUMENT_DESC');?>&nbsp;<a href="index.php?option=com_secretary&view=items&extension=uploads" target="_blank"><i class="fa fa-external-link"></i></a></h4>
                        <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                    </div>
                    
                    <div class="secretary-document-zahlung">
                        <h4 class="title"><?php echo JText::_('COM_SECRETARY_ENTRY_PAYMENTINFORMATIONEN');?></h4>
                        <div class="control-group">
                            <?php echo $this->form->getLabel('state');?>
                            <div class="controls"><?php echo $this->form->getInput('state');?></div>								
                        </div>
                        
                        <div class="control-group">
                            <?php echo $this->form->getLabel('deadline');?>
                            <div class="controls"><?php echo $this->form->getInput('deadline');?></div>								
                        </div>
                        
                        <div class="control-group">
                            <?php echo $this->form->getLabel('paid');?>
                            <div class="controls">
                                <div class="secretary-input-group clearfix pull-left">
                                    <div class="secretary-input-group-left"><?php echo $this->form->getInput('paid');?></div>
                                    <div class="secretary-input-group-right currency-control"><?php echo $this->item->currencySymbol; ?></div>
                                </div>
                            </div>	
                        </div>	
                        	
                    </div>
            
                </div>
            </div>
            </div>
            
        </div>
    
        <div class="tab-pane secretary-document-main" id="more">
            <?php echo $this->loadTemplate('extended');?>				
        </div>
        
        <?php if($this->item->id > 0 && $user->authorise('core.show', 'com_secretary.accounting')) {  ?>
        <div class="tab-pane secretary-document-main" id="accounting">
            <?php echo $this->loadTemplate('accounting');?>				
        </div>
        <?php } ?>
        
        <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
        <div class="tab-pane secretary-document-main" id="permission">
        	<?php echo $this->form->getInput('rules'); ?>
        </div>
        <?php endif; ?>
        
    </div>
        
    <?php echo $this->form->getInput('id'); ?>
    <input type="hidden" name="jform[createdEntry]" value="<?php echo $this->item->createdEntry;?>" />
    <input type="hidden" name="catid" value="<?php echo $this->item->catid;?>"  id="catid" />
    <input type="hidden" name="task" value="" id="formtask" />
    <?php echo JHtml::_('form.token'); ?>
    
</div>
    
</form>

</div>
<?php echo Secretary\HTML::modal(); ?>

<script>
jQuery(document).ready(function($){
	$('.clean-contact').click(function(){
		$('#display_contact_name').hide();
		$('#contact_name').text('');
		$('#jform_subjectid').val('');
		$('#jform_subject_name').val('');
		$('#jform_subject_street').val('');
		$('#jform_subject_zip').val('');
		$('#jform_subject_location').val('');
		$('#jform_subject_phone').val('');
		$('#jform_subject_email').val(''); 
		$('#jform_subject_name').show();
	});
	$('.table-items-list').nestable({ dragClass : "table-item dd-dragel" , maxDepth : 1 });
});
</script>
    