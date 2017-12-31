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

$extension = 'times';

$user = \Secretary\Joomla::getUser(); 

$locationTitle = Secretary\Database::getQuery('locations',$this->item->location_id,'id','title','loadResult');
?>
               
<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
    <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
    <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
    <?php endif; ?>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="home">
    
        <div class="row">
            <div class="col-md-12">
            
            <fieldset class="adminform">
            
                <div class="row-fluid">
                
                    <div class="col-md-3">
                        <div class="control-group">
                        <div class="control-label">
                        	<label><?php echo JText::_('COM_SECRETARY_LOCATION_TIMES').Secretary\HTML::_('search.locations','','times');?></label>
                        </div>
                        <div class="controls"> 
                            <?php echo $this->form->getInput('location_id'); ?>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                        <div class="controls select-arrow select-arrow-white select-large"><?php echo $this->form->getInput('catid'); ?></div>
                    </div>
                    <div class="col-md-2">
                        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                        <div class="controls select-arrow select-arrow-white select-large"><?php echo $this->form->getInput('state'); ?></div>
                    </div>
                    
					<hr class="col-md-12" />
                    
                    <div class="col-md-4">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('startDate'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('startDate'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('endDate'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('endDate'); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="control-group">
                            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></label></div>
                            <div class="posts multiple-input-selection clearfix" data-counter="<?php echo $this->contactsCounts; ?>">
                                <div>
                                <input class="search-features uk-form-blank" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                    <hr />
                        <div class="control-label"><?php echo $this->form->getLabel('text'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('text'); ?></div>
                    </div>
                    
                </div>
                
                <div class="row-fluid">
                
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                    </div>
                
                    <hr class="col-md-12" />
                    
                    
                </div>
                
            </fieldset>
                
            </div>
            
        </div>
    
    </div>
    
    <div class="tab-pane" id="fields">
        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
        <div class="fields-items"></div>
        <div class="field-add-container clearfix">
            <?php echo Secretary\HTML::_('datafields.listOptions', $extension ); ?>
            <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
        </div>
    </div>
    
    <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
        <div class="tab-pane" id="permission">
            <fieldset>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
        </div>
	<?php endif; ?>
    
</div>	
         