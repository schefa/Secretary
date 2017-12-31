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

$user = \Secretary\Joomla::getUser();
$business	= \Secretary\Application::company('currency,taxvalue');
$currency	= $business['currency'];

$categoryTitle = Secretary\Database::getQuery('folders',$this->item->catid,'id','title','loadResult');
$businessTitle = Secretary\Database::getQuery('businesses',$this->item->business,'id','title','loadResult');
$locationTitle = Secretary\Database::getQuery('locations',$this->item->location,'id','title','loadResult');
?>

<div class="secretary-main-container">
<div class="secretary-main-area entry-default">

    <div class="secretary-toolbar fullwidth">
        <div class="secretary-title">
            <span><a href="<?php echo Secretary\Route::create('products'); ?>"> <?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <span><?php echo JText::_('COM_SECRETARY_PRODUCT'); ?></span>
            
			<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('product',$this->item->id,$this->item->created_by))) { ?>
            <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=product.edit&id=<?php echo $this->item->id;?>&catid=<?php echo $this->item->catid;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
			<?php } ?>
            
            <div class="btn-group pull-right">
                <div class="btn"><?php echo $businessTitle; ?></div>
                <div class="btn" style="width:50px;"><?php echo $this->item->year; ?></div>
            </div>
        </div>
    </div>
    
    <hr />
    
    <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
        <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
        <li><a href="#history" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_DOCUMENTS', true); ?></a></li>
        <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
    </ul>
     
    <div class="tab-content">
    
        <div class="tab-pane active" id="home">
    
            <div class="fullwidth">
                <div class="col-xs-9 control-group">
                    <div class="fullwidth control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="fullwidth secretary-controls controls"><?php echo $this->item->title; ?></div>
                    </div>
                    <div class="fullwidth control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                        <div class="fullwidth secretary-controls controls"><?php echo $this->item->description; ?></div>
                    </div>
                </div>
                <div class="col-xs-3 control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                    <div class="fullwidth secretary-controls controls">
                    <?php
                        if(!empty($this->item->upload)) { 
                            $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload, 'id');
                            echo '<div class="upload-file fullwidth">'. \Secretary\Helpers\Uploads::getUploadFile($logoImage, '',200) .'</div>';
                        } 
                    ?>
                    </div>
                </div>
            </div>
            
            <div class="fullwidth">
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('taxRate'); ?></div>
                        <div class="fullwidth secretary-controls controls"><?php echo $this->item->taxRate; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                        <div class="fullwidth secretary-controls controls">
                        <?php if(!empty($categoryTitle)) { echo $categoryTitle; } else { echo JText::_('COM_SECRETARY_NONE'); } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('entity'); ?></div>
                        <div class="fullwidth secretary-controls controls"><?php echo $this->item->entity; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                        <div class="fullwidth secretary-controls controls">
                        <?php if(!empty($locationTitle)) { echo $locationTitle; } else { echo JText::_('COM_SECRETARY_NONE'); } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr />
            
            <div class="fullwidth">
            
            <div class="row-fluid">
                <div class="col-md-6">
                    <h3 class="title title-edit"><?php echo JText::_("COM_SECRETARY_EINGANG"); ?></h3>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('priceCost'); ?></div>
                        <div class="controls"><?php echo $this->item->priceCost; ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('totalBought'); ?></div>
                        <div class="controls"><?php echo $this->item->totalBought; ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('quantityBought'); ?></div>
                        <div class="controls"><?php echo $this->item->quantityBought; ?></div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h3 class="title title-edit"><?php echo JText::_("COM_SECRETARY_AUSGANG"); ?></h3>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('priceSale'); ?></div>
                        <div class="controls"><?php echo $this->item->priceSale; ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('total'); ?></div>
                        <div class="controls"><?php echo $this->item->total; ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('quantity'); ?></div>
                        <div class="controls"><?php echo $this->item->quantity; ?></div>
                    </div>
                </div>
            </div>
                
            </div>
            <hr />
            
        </div>

        <div class="tab-pane" id="history">
        <?php
            if(!empty($this->item->history)) {
                include_once(SECRETARY_ADMIN_PATH .'/views/product/tmpl/default_documents.php');
			} else {
				echo '<div class="alert alert-warning">'.JText::_('COM_SECRETARY_NONE').'</div>';	
			}
		?>
        </div>
             
    
        <div class="tab-pane" id="fields">
        
            <div class="fullwidth">
                <h3 class="title"><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
                <div class="fields-items form-horizontal">
                    <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                        <?php foreach($fields as $field) { ?>
                                    
                            <div class="control-group">
                                <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                                <div class="controls"><?php echo $field[2]; ?></div>
                            </div>
    
                        <?php } ?>
                    <?php } ?>
                </div>
                
            </div>
            
        </div>
        
    </div>
    
</div>
</div>
 
<?php echo $this->form->getInput('id'); ?>
