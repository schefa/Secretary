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

<div class="container-fluid">

<table class="table table-hover">
    <thead>
        <tr>
            <td><?php echo JText::_('COM_SECRETARY_TITLE'); ?></td>
            <td width="20%"><?php echo JText::_('COM_SECRETARY_TIMES_STARTDATE'); ?></td>
            <td width="20%"><?php echo JText::_('COM_SECRETARY_TIMES_ENDDATE'); ?></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($this->item->projects as $project) { ?>
        <tr>
        <td><a href="index.php?option=com_secretary&view=time&extension=projects&id=<?php echo $project->id; ?>"><?php echo $project->title; ?></a></td>
        <td><?php echo $project->startDate; ?></td>
        <td><?php echo $project->endDate; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

</div>