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
?>

<div class="secretary-main-container">
<div class="secretary-main-area entry-default">

    <div class="secretary-toolbar fullwidth">
        <div class="secretary-title">
            <span><a href="<?php echo Secretary\Route::create('businesses'); ?>"> <?php echo JText::_('COM_SECRETARY_BUSINESSES'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <span><?php echo JText::_('COM_SECRETARY_BUSINESS'); ?></span>
			<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('business',$this->item->id,$this->item->created_by))) { ?>
            <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=business.edit&id=<?php echo $this->item->id;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
            <?php } ?>
        </div>
    </div>
            
    <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
        <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    </ul>
    
    <div class="tab-content">
        <div class="tab-pane active" id="home">
        
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->item->title; ?></div>
                    </div>
                </div>
            </div>
            
            <hr />
            
            <div class="row-fluid">
            
                <div class="col-md-6">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
                        <div class="controls"><?php echo $this->item->address; ?></div>
                    </div>
                </div>
                
                <div class="col-md-6">
                
                    <div class="control-group">
                    <?php echo $this->form->getLabel('upload'); ?>
                    <?php if(!empty($this->item->upload)) {
                        $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload,'id','business,title,folder');?>
                        <div class="upload-file">
                    	<?php \Secretary\Helpers\Uploads::getUploadFile( $logoImage, NULL, 200); ?>
                        </div>
					<?php } ?>
                    </div>
                    
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('slogan'); ?></div>
                        <div class="controls"><?php echo $this->item->slogan; ?></div>
                    </div>
                    
                </div>
                
            </div>
            
            <hr />
            
            <div class="row-fluid">
                <div class="col-md-12">
                <h3><?php echo JText::_('COM_SECRETARY_BUSINESS_PREFERENCE'); ?></h3>
                <p><?php echo JText::_('COM_SECRETARY_BUSINESS_PREFERENCE_DESC'); ?></p>
                </div>
            </div>
            <div class="row-fluid">
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_CURRENCY'); ?></label></div>
                        <div class="controls"><?php echo $this->item->currency; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_BUSINESS_TAXVALUE'); ?></label></div>
                        <div class="controls"><?php echo $this->item->taxvalue; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TAX'); ?></label></div>
                        <div class="controls"><?php echo $this->item->taxPrepo; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_BUSINESS_DEFAULTNOTE'); ?></label></div>
                        <div class="controls"><?php echo $this->item->defaultNote; ?></div>
                    </div>
                </div>
            </div>
            <hr />
        
            <div class="row-fluid">
            
                <div class="col-md-12">
                    <h3><?php echo JText::_('COM_SECRETARY_BUSINESS_DOCUMENTS'); ?></h3>
                    <p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_BUSINESS_DOCUMENTS_DESC'); ?></p>
                </div>
                
                <div class="col-md-3">
                    <div class="controls">
                    <?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->selectedFolders['documents']);?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="controls">
                    <?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->selectedFolders['subjects']);?>
                    </div>
				</div>
                
                <div class="col-md-3">
                    <div class="controls">
                    <?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->selectedFolders['products']);?>
                    </div>
				</div>
                
                <div class="col-md-3">
                    <div class="controls">
                    <?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->selectedFolders['messages']);?>
                    </div>
				</div>
                    
            </div>
            
            <hr />
            
            <div class="row-fluid">
            
                <div class="col-md-4">
                    <h4><?php echo JText::_('COM_SECRETARY_REPORTS'); ?></h4>
                    
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_REPORTS_WINNING'); ?></label></div>
                    	<p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_REPORTS_WINNING_DESC'); ?></p>
                        <div class="controls">
                    	<?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->guv1);?> 
                        </div>
                    </div>
                    
                    
                    <div class="control-group">
                        <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_REPORTS_LOSING'); ?></label></div>
                    	<p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_REPORTS_LOSING_DESC'); ?></p>
                        <div class="controls">
                    	<?php echo \Secretary\HTML::_('business.selectedCategories',$this->item->guv2);?>
                    	</div>
                    </div>
                    
                </div>
                
                <div class="col-md-8">
                    <div class="fullwidth">
                        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
    
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

    <div class="row-fluid">
        <div class="col-md-4">
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_CREATED'); ?></label></div>
                <div class="controls"><?php echo date('H:i:s d.m.Y', $this->item->createdEntry);?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('ID'); ?></label></div>
                <div class="controls"><?php echo $this->item->id;?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="control-group">
                <div class="control-label"><label><?php echo JText::_('JDEFAULT'); ?></label></div>
                <div class="controls"><?php echo $this->item->home;?></div>
            </div>
        </div>
    </div> 
    
</div>
</div>

