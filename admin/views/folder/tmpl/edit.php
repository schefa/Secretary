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

$user = JFactory::getUser();

// Unset data fields in the selection
$fields = json_decode($this->datafields['fields']);
$unsets = array();
 
if($this->item->extension=='documents') {
    $unsets = array_merge($unsets, array('pUsage','template','emailtemplate','docsSoll','docsSollTax','docsHaben','docsHabenTax'));
}
       
?>

<div class="secretary-main-container">
<?php echo Secretary\HTML::_('datafields.item'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=folder&extension=' . $this->extension . '&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

<div class="secretary-main-area">

    <div class="secretary-toolbar fullwidth">
        <div class="secretary-title"> 
        <span><?php echo JText::_($this->title); ?>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
        <?php $this->addToolbar(); ?>
        </div>
    </div>
    
    <ul class="nav nav-tabs margin-bottom fullwidth" id="myTab" role="tablist">
        <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
			<?php if($this->extension == 'newsletters') {  ?>
                <li><a href="#contacts" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_SUBJECTS', true); ?></a></li>
			<?php } ?>
        <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
        <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
        <?php endif; ?>
    </ul>
 
    <div class="tab-content">
        <div class="tab-pane active" id="home">
        
            <div class="row">
                <div class="col-md-9">
                
                    <div class="fullwidth">
                        <div class="col-md-6">
                            <?php echo $this->form->getLabel('title'); ?>
                            <div class="control-group">
                                <?php echo $this->form->getInput('title'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('alias'); ?>
                            <?php echo $this->form->getInput('alias'); ?>
                            </div>
                        </div>
                    </div>
            
                    <div class="fullwidth">
                        <div class="col-md-12">
                            <div class="control-group">
                                <?php echo $this->form->getLabel('description'); ?>
                                <div class="controls">
                                    <?php echo $this->form->getInput('description'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
					<?php if($this->item->extension == 'documents') echo $this->loadTemplate('documents'); ?>
					
					<?php if($this->item->extension == 'subjects' || $this->item->extension == 'products' || $this->item->extension == 'messages') { ?>
						<hr>
                        <div class="col-md-4">
                            <label><?php echo JText::_('COM_SECRETARY_TEMPLATE');?></label>
                            <div class="control-group">
                            <?php echo $this->itemtemplates; ?>
                            </div>
                        </div>
					<?php } ?>
			
                </div>
                
                <div class="col-md-3">
                    <div class="control-group">
                        <?php echo $this->form->getLabel('parent_id'); ?>
                        <div class="select-arrow select-arrow-white margin-bottom">
                            <select class="jform_parent_id" name="jform[parent_id]">
                                <?php echo JHtml::_('select.options', $this->folders, 'id', 'title', $this->item->parent_id, true);?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <?php echo $this->form->getLabel('number'); ?>
                        <?php echo $this->form->getInput('number'); ?>
                        <div class="secretary-desc margin-bottom"><?php echo JText::_('COM_SECRETARY_FOLDER_NUMBER_DESC');?></div>
                    </div>
                    
                    <div class="control-group">
                        <?php echo $this->form->getLabel('state'); ?>
                        <div class="controls select-arrow select-arrow-white margin-bottom">
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr />
            
            <div class="fullwidth">
                <div class="col-md-12">
                    <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                    
                    <div class="fields-items"></div>
                    <div class="field-add-container clearfix">
                        <?php echo Secretary\HTML::_('datafields.listOptions', $this->extension, $unsets ); ?>
                        <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                    </div>
                </div>
            </div>

        </div>

		<?php if($this->extension == 'newsletters') {  ?>
        <div class="tab-pane" id="contacts">
        	<?php echo $this->loadTemplate('subs'); ?>
        </div>
		<?php } ?>
                
        <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <div class="tab-pane" id="permission">
                <?php echo $this->form->getInput('rules'); ?>
            </div>
        <?php endif; ?>
    </div>
        
</div>
    
<?php echo $this->form->getInput('id'); ?>
<?php echo $this->form->getInput('extension'); ?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
</div>
