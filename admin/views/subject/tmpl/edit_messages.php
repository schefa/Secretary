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
<div class="container-fluid">

<table class="table table-hover">
    <thead>
        <tr>
            <td width="1%" class="nowrap"><?php echo JText::_('COM_SECRETARY_CREATED'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_TITLE'); ?></td>
            <td><?php echo JText::_('COM_SECRETARY_MESSAGE'); ?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($this->item->messages as $message) { ?>
        <tr>
        <td><?php echo $message->created ?></td>
        <td><?php echo $message->subject; ?></td>
        <td><a href="index.php?option=com_secretary&view=message&id=<?php echo $message->id; ?>"><?php echo substr(strip_tags(Secretary\Utilities::cleaner($message->message,true)), 0, 150); ?>…</a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</div>
