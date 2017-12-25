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
 
$template = (strpos($this->extension,"locations") === true) ? 'locations' : $this->extension;
$this->datafields	= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
$fields				= $this->datafields['fields'];
?>

<div class="secretary-main-container">

<?php echo Secretary\HTML::_('datafields.item'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=time&layout=edit&id=' . (int) $this->item->id .'&extension='.$this->item->extension); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">

<div class="secretary-main-area">
    <div class="secretary-toolbar fullwidth">
        <div class="secretary-title">
            <span><?php echo JText::_('COM_SECRETARY_TIMES'); ?>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
			<?php if($this->item->extension === 'tasks') { ?>  
            <span><?php echo JText::_('COM_SECRETARY_PROJECT'); ?>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <?php } ?>
            <span><?php echo $this->title; ?>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
            <?php $this->addToolbar(); ?>
        </div>
    </div>
    
    <?php echo $this->loadTemplate($template); ?>
    
    <?php echo $this->form->getInput('id'); ?>
    <?php echo $this->form->getInput('business'); ?>
	<?php echo $this->form->getInput('extension'); ?>
    <input type="hidden" value="<?php echo $this->item->catid; ?>" name="catid" id="catid" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</div>

</form>
</div>

        
<script type="text/javascript">
<?php if(isset($fields)) :?>
	var secretary_fields = [<?php echo $fields;?>];
<?php else : ?>
	var secretary_fields = [];
<?php endif;?>
Secretary.printFields( secretary_fields );
</script>