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

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('currency'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('currency'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('symbol'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('symbol'); ?></div>
</div>
