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
 
$user       = Secretary\Joomla::getUser();
$business	= Secretary\Application::company('currency,taxvalue');
$currency	= $business['currency'];

?>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('parent_id'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('parent_id'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('type'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('nr'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('nr'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
</div>
