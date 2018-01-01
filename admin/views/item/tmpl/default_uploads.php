<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die;

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

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

