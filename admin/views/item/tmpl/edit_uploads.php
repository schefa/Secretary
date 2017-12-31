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

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');
$modules = JFormHelper::loadFieldType('SecretarySections', false)->getModulesArray();

?>

<?php if(!empty($this->item->id)) { ?>
	<div class="upload-file">
		<?php \Secretary\Helpers\Uploads::getUploadFile($this->item,'',480); ?>
        <input type="hidden" value="<?php echo $this->title; ?>" name="jform[upload_title]" id="jform_upload_title" />
	</div>
<?php } else { ?>
    <div class="upload-fileupload"><input type="file" name="jform[upload]" id="jform_upload" /></div>	
    <div class="upload-allowedsize"><?php echo JText::_('COM_SECRETARY_DOCUMENT_SIZE_ALLOWED') .' '. \Secretary\Utilities\Number::human_filesize($this->state->params->get('documentSize')) . 'B'; ?></div>
<?php } ?>

<hr />

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('business'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('business');  ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
    <div class="controls">
	<?php
	if(isset($this->item->extension)) {
		echo $modules[$this->item->extension];
	} else {
		echo $this->form->getInput('extension');
	}
	?>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('itemID'); ?></div>
    <div class="controls">
	<?php
	if($this->item->extension > 0) {
		echo $this->item->itemID;
	} else {
		echo $this->form->getInput('itemID');
	}
	?>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
</div>
