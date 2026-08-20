<?php
 
defined('_JEXEC') or die;

?>

<hr>

<div class="fullwidth margin-bottom">
    <div class="col-md-4">
        <label><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE');?></label>
        <div class="control-group">
        <?php echo $this->itemtemplates; ?>
        </div>
    </div>
    <div class="col-md-4">
        <label><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_TEMPLATE');?></label>
        <div class="control-group">
        <?php echo $this->emailtemplates; ?>
        </div>
    </div>
    <div class="col-md-4">
        <label><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_USAGE');?></label>
        <div class="control-group">
         <?php echo $this->productUsageOption ;?>  
        </div>
    </div>
</div>
