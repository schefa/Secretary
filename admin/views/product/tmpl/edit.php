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

$user               = JFactory::getUser();
$this->datafields   = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
$fields             = (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
?>

<div class="secretary-main-container">
    
<?php echo Secretary\HTML::_('datafields.item'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=product&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

	<div class="secretary-main-area">

        <div class="secretary-toolbar fullwidth">
            <div class="pull-left secretary-title">
            <div class="select-arrow-toolbar select-arrow"><?php echo $this->form->getInput('catid');?></div>
            <span>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_PRODUCT'); ?></span>
            <?php $this->addToolbar(); ?>
            </div>
        </div>
        
        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
            <li><a href="#history" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_DOCUMENTS', true); ?></a></li>
            <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
            <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
            <?php endif; ?>
            
            <?php if($this->item->id > 0 && COM_SECRETARY_PDF && !empty($this->defaultTemplate)) { ?>
            <li class="pull-right secretary-document-pdf-print">
                <a class="btn btn-link btn-pdf modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" href="<?php echo Secretary\Route::create('product', array('format'=>'pdf', 'id'=> $this->item->id )); ?>" role="tab" data-toggle="tab"><img src="<?php echo JURI::root(); ?>/media/secretary/images/pdf-20.png" />&nbsp;<?php echo 'PDF'; ?></a>
            </li>
            <?php }  ?>
        </ul>
         
        <div class="tab-content">
        
            <div class="tab-pane active" id="home">
            
                <div class="fullwidth">
                    <div class="col-xs-8 control-group">
                        <div class="row">
                            <div class="col-md-9">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('title'); ?></div>
                            </div>
                            </div>
                            <div class="col-md-3">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('nr'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('nr'); ?></div>
                            </div>
                            </div>
                        </div>
                        <div class="fullwidth control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                            <div class="secretary-controls"><?php echo $this->form->getInput('description'); ?></div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-2">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('taxRate'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('taxRate'); ?></div>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('entity'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('entity'); ?></div>
                            </div>
                            </div>
                            <div class="col-md-3">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('template'); ?></div>
                                <div class="secretary-controls select-large select-arrow select-arrow-white">
                                <?php echo $this->itemtemplates; ?>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-3">
                            <div class="control-group">
                                <div class="control-label"><label>&nbsp;</label></div>
                                <div class="secretary-controls select-large select-arrow select-arrow-white">
                                <?php echo $this->form->getInput('business'); ?>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-1">
                            <div class="control-group">
                                <div class="control-label"><label>&nbsp;</label></div>
                                <div class="secretary-controls" style="width:50px;"><?php echo $this->form->getInput('year'); ?></div>
                            </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5"><div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                                <div class="secretary-controls select-arrow select-arrow-white">
                                    <?php echo $this->form->getInput('location'); ?>
                                </div>
                            </div></div>
                            <div class="col-md-3">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('quantityMin'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('quantityMin'); ?></div>
                            </div></div>
                            <div class="col-md-3">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('quantityMax'); ?></div>
                                <div class="secretary-controls"><?php echo $this->form->getInput('quantityMax'); ?></div>
                            </div></div>
                        </div>
                    </div>
                    <div class="col-xs-4">
						<div class="control-group"> 
                            <div class="control-group">
                            	<div class="control-label"><?php echo JText::_('COM_SECRETARY_ATTACHMENT'); ?></div>
                            	<div class="secretary-controls"><?php echo $this->form->getInput('upload'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr />
                
               	<div class="fullwidth">
                    
                	<div class="form-horizontal row-fluid">
                        <div class="col-md-6">
                            <h3 class="title title-edit">
							<?php echo JText::_("COM_SECRETARY_EINGANG"); ?> 
                            <a class="btn btn-sm" href="<?php echo JRoute::_('index.php?option=com_secretary&view=document&layout=edit&pusage=2&pid='.$this->item->id . '&subject='.json_encode($this->suppliers_ids));?>"><?php echo JText::_("COM_SECRETARY_BUY"); ?></a>
                            </h3>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('priceCost'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('priceCost'); ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PRODUCT_QUANTITYBOUGHT'); ?></label></div>
                                <div class="controls">
                                <input value="<?php echo Secretary\Utilities\Number::getNumberFormat($this->item->quantityBought); ?>" class="form-control" readonly="true" type="text">
                                <?php echo $this->form->getInput('quantityBought'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PRODUCT_TOTALBOUGHT'); ?></label></div>
                                <div class="controls">
                                <input value="<?php echo Secretary\Utilities\Number::getNumberFormat($this->item->totalBought); ?>" class="form-control" readonly="true" type="text">
                                <?php echo $this->form->getInput('totalBought'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_SUPPLIERS'); ?>&nbsp;<a href="index.php?option=com_secretary&view=subjects" target="_blank"><i class="fa fa-external-link"></i></a></label></div>
                                <div class="controls">
                            <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo $this->contactsCounts; ?>">
                                <div>
                                <input class="search-features uk-form-blank" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
                                </div>
                            </div>
                                
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h3 class="title title-edit"><?php echo JText::_("COM_SECRETARY_AUSGANG"); ?>
                            <a class="btn btn-sm" href="<?php echo JRoute::_('index.php?option=com_secretary&view=document&layout=edit&pusage=1&pid='.$this->item->id);?>"><?php echo JText::_("COM_SECRETARY_SELL"); ?></a>
                            </h3>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('priceSale'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('priceSale'); ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PRODUCT_QUANTITY'); ?></label></div>
                                <div class="controls">
                                <input value="<?php echo Secretary\Utilities\Number::getNumberFormat($this->item->quantity); ?>" class="form-control" readonly="true" type="text">
                                <?php echo $this->form->getInput('quantity'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_PRODUCT_TOTAL'); ?></label></div>
                                <div class="controls">
                                <input value="<?php echo Secretary\Utilities\Number::getNumberFormat($this->item->total); ?>" class="form-control" readonly="true" type="text">
                                <?php echo $this->form->getInput('total'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
				
                </div>
                
            </div>
                
            <div class="tab-pane" id="history">
            <?php
                if(!empty($this->item->history)) {
    			    include_once(JPATH_COMPONENT_ADMINISTRATOR .'/views/product/tmpl/default_documents.php');
    			} else {
    				echo '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_NONE').'</div>';	
    			}
    		?>
            </div>
        
            <div class="tab-pane" id="fields">
                <div class="fullwidth">
                    <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                    
                    <div class="fields-items"></div>
                    <div class="field-add-container clearfix">
            			<?php echo Secretary\HTML::_('datafields.listOptions', 'products' ); ?>
                        <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                    </div>

                </div>
            </div>
        
        	<?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <div class="tab-pane" id="permission">
            	<?php echo $this->form->getInput('rules'); ?>
            </div>
        	<?php endif; ?>
            
		</div>
        
	</div>

    <?php echo $this->form->getInput('id'); ?>
    <input type="hidden" name="catid" value="<?php echo $this->item->catid ?>" id="catid" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>

</div>