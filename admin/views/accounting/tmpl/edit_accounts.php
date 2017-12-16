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

$user       = Secretary\Joomla::getUser();
$business	= Secretary\Application::company('currency,taxvalue');
$currency	= $business['currency'];
$extension	= 'bookings';

$this->datafields		= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
$fields			= $this->datafields['fields'];

$accountTitle = Secretary\Database::getQuery('accounts_system',intval($this->item->kid),'id','title','loadResult');
?>

<div class="control-group">
    <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_ACCOUNTS_SYSTEM_PID'); ?></label></div>
    <div class="controls">
    <input class="search-accounts" type="text" value="<?php echo $accountTitle; ?>" />
	<?php echo $this->form->getInput('kid'); ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
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
    <div class="control-label"><?php echo $this->form->getLabel('year'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('year'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('budget'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('budget'); ?></div>
</div>

<div class="fullwidth">
    <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
    
    <div class="fields-items"></div>
    <div class="field-add-container clearfix">
        <?php echo Secretary\HTML::_('datafields.listOptions', $extension ); ?>
        <button id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></button>
    </div>

</div>

<script type="text/javascript">
jQuery.noConflict();
jQuery( document ).ready(function( $ ) {
<?php if(isset($fields)) :?>
	var secretary_fields = [<?php echo $fields;?>];
	Secretary.Fields( secretary_fields );
<?php else : ?>
	var secretary_fields = [];
<?php endif;?>
});
</script>
