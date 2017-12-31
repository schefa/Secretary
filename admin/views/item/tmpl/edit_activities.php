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
?>
        
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
</div>  
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('fields'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('fields'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('extension'); ?></div>
</div>
