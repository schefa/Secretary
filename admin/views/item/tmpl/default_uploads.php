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

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');

$modules = JFormHelper::loadFieldType('SecretarySections', false)->getModulesArray();
?>
        
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    <div class="controls"><a href="<?php echo $this->item->link;  ?>" target="_new"><?php echo $this->item->title;  ?></a></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('folder'); ?></div>
    <div class="controls"><?php echo $this->item->link;  ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('business'); ?></div>
    <div class="controls"><?php echo Secretary\Database::getQuery('businesses',$this->item->business,'id','title','loadResult'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('itemID'); ?></div>
    <div class="controls"><?php echo $this->item->itemID; ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
    <div class="controls"><?php echo $this->item->created;  ?></div>
</div>
<?php if(!empty($this->item->extension)) { ?>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
    <div class="controls"><?php echo $modules[$this->item->extension];  ?></div>
</div>
<input name="jform[extension]" type="hidden" default="<?php echo $this->item->extension;  ?>" />
<?php } ?>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('parameter'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('parameter'); ?></div>
</div>

<input name="jform[itemID]" type="hidden" default="<?php echo $this->item->itemID;  ?>" /> 
<input name="jform[title]" type="hidden" default="<?php echo $this->item->title;  ?>" /> 
<input name="jform[folder]" type="hidden" default="<?php echo $this->item->folder;  ?>" /> 
<input name="jform[business]" type="hidden" default="<?php echo $this->item->business;  ?>"  />
<input name="jform[created]" type="hidden" default="<?php echo $this->item->created;  ?>" /> 

