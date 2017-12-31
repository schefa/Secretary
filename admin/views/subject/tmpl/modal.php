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

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "subject.cancel" || document.formvalidator.isValid(document.getElementById("subject-form")))
		{

			if (window.opener && (task == "subject.save" || task == "subject.cancel"))
			{
				window.opener.document.closeEditWindow = self;
				window.opener.setTimeout("window.document.closeEditWindow.close()", 1000);
			}

			Joomla.submitform(task, document.getElementById("subject-form"));
		}
	};
');

// Fieldsets to not automatically render by /layouts/joomla/edit/params.php
$this->ignore_fieldsets = array('details', 'display', 'email', 'item_associations');
?>
<div class="container-popup">

<div class="pull-right">
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('subject.apply');"><?php echo JText::_('COM_SECRETARY_TOOLBAR_APPLY') ?></button>
	<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('subject.save');"><?php echo JText::_('COM_SECRETARY_TOOLBAR_SAVE') ?></button>
	<button class="btn" type="button" onclick="Joomla.submitbutton('subject.cancel');"><?php echo JText::_('JCANCEL') ?></button>
</div>

<div class="clearfix"> </div>
<hr class="hr-condensed" />

<form action="<?php echo JRoute::_('index.php?option=com_secretary&layout=modal&tmpl=component&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="subject-form" class="form-validate">


    <div class="fullwidth form-horizontal">

        <div class="col-md-6 form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo JText::_('COM_SECRETARY_GROUP'); ?></div>
                <div class="controls select-arrow select-arrow-white"><?php echo $this->form->getInput('catid'); ?></div>
            </div>
            <hr />
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('gender'); ?></div>
                <div class="controls select-arrow select-arrow-white"><?php echo $this->form->getInput('gender'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('firstname'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('firstname'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('lastname'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('lastname'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('street'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('street'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('zip'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('zip'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('location'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('email'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
            </div>
            
        </div>
        
        <div class="col-md-6">
        
            <div class="control-group">
            <div class="control-label"><?php echo JText::_('COM_SECRETARY_IMAGE'); ?></div>
            <?php if(!empty($this->item->upload)) {
                $logoImage = Secretary\Database::getQuery('uploads', $this->item->upload,'id','business,title,folder');?>
                <div class="upload-file fullwidth">
                <?php \Secretary\Helpers\Uploads::getUploadFile($logoImage, '', 200); ?>
                </div>
				<?php if($canUpload) { ?>
                <div class="upload-file-delete">
                    <input type="checkbox" name="deleteDocument" >&nbsp;<?php echo JText::_('COM_SECRETARY_DELETE');?>
					<input id="jform_upload_title" type="hidden" value="<?php echo $this->item->upload; ?>" name="jform[upload_title]">
                </div>
                <?php } ?>
            <?php } ?>
			<?php if($canUpload) { ?>
            <div class="upload-file"><?php echo $this->form->getInput('upload');?></div>	
            <p class="secretary-desc fullwidth"><?php echo JText::_('COM_SECRETARY_DOCUMENT_SIZE_ALLOWED') .' '. \Secretary\Utilities\Number::human_filesize($documentSize) . 'B'; ?></p>
            <?php } ?>
            </div>
            
            <?php if(!empty($this->item->id)) {?>
            <div class="control-group">
            
                <?php $document = JText::_('COM_SECRETARY_DOCUMENT'); ?>
                <h3 class="title"><?php echo JText::sprintf('COM_SECRETARY_CREATE_THIS', $document); ?></h3>
                
                <?php 
                $documents	= array();
                foreach($this->documents as $document) {
                    $documents[] = JHtml::_('select.option', $document->id, JText::_($document->title)); }
                ?>
                <select id="add_new_document" data-subject="<?php echo $this->item->id;?>" class="form-control inputbox">
                    <?php echo JHtml::_('select.options', $documents, 'value', 'text');?>
                </select>
                <div id="add_document" class="btn btn-primary"><?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                <script>
                (function($){
                    $('#add_document').click(function(){
                        var subjectId = $('#add_new_document').data('subject');
                        var documentId = $('#add_new_document').val();
                        var url = 'index.php?option=com_secretary&view=document&layout=edit&catid='+documentId+'&subject='+subjectId;
                        window.location.href = url;
                    })
                })(jQuery);
                </script>
            </div>
            <?php } ?>
            
            <hr />
            <div class="control-group">
                <h3 class="title title-edit"><?php echo JText::_('COM_SECRETARY_CONNECTIONS'); ?></h3>
                <div class="posts multiple-input-selection clearfix" data-source="subjects" data-counter="<?php echo $this->contactsCounts; ?>">
                    <div>
                    <input class="search-features uk-form-blank" type="text" placeholder="<?php echo JText::_('COM_SECRETARY_SEARCH'); ?>" >
                    </div>
                </div>
            </div>
            <hr />
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                <div class="controls select-arrow select-arrow-white"><?php echo $this->form->getInput('state'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
            </div>
        </div>
    
    </div>
    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
