<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

$this->datafields   = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields); 
?>

<?php if( $this->tmpl == 'component') { ?>
<div class="secretary-modal-top">
    <button class="modal-close" data-dismiss="modal" type="button">x</button>
    <h3><?php echo JText::_('COM_SECRETARY_MESSAGE'); ?></h3>
</div>
<?php } ?>

<div class="secretary-main-container">
<div class="secretary-main-area">

<?php
$userId	= (int) Secretary\Database::getQuery('subjects', (int) \Secretary\Joomla::getUser()->id,'created_by','id','loadResult');
if($userId <= 0) {
	echo JText::_('COM_SECRETARY_MESSAGES_USER_NO_CONTACT');
} else {
?>

<?php echo Secretary\HTML::_('datafields.item'); ?>
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=message&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="message-form" enctype="multipart/form-data" class="form-validate">

	<div class="secretary-toolbar fullwidth">
		<?php $this->addToolbar(); ?>
	</div>
    
	<div class="message-head-contact fullwidth">
        <div class="message-head-contact-col-2 message-head-contact-col pull-left">
       		<h3 class="title"><?php echo JText::_('COM_SECRETARY_TALK') ?></h3>
            <div class="">
                <?php echo $this->form->getLabel('contact_to'); ?>
                <?php echo $this->form->getInput('contact_to'); ?>
            </div> 
            <div class="message-head-contact-sep">
            	<?php echo JText::_('COM_SECRETARY_OR'); ?>
    		</div> 
            <div class="">
                <?php echo $this->form->getLabel('contact_to_alias'); ?>
                <?php echo $this->form->getInput('contact_to_alias'); ?>
            </div> 
        </div>
        
        <div class="message-head-contact-col-1 message-head-contact-col pull-left">
       		<h3 class="title"><?php echo JText::_('COM_SECRETARY_CORRESPONDENCE') ?></h3>
            <div class="fullwidth">
        		<?php echo $this->form->getLabel('catid'); ?>
            	<div class="select-arrow select-arrow-white select-large"><?php echo $this->form->getInput('catid'); ?></div>
            </div>
		</div>
	</div>
    
    <hr />
	<div class="fullwidth margin-bottom">
        <div class="col-md-8">
            <div class="control-group">
            <?php echo $this->form->getLabel('subject'); ?>
            <?php echo $this->form->getInput('subject'); ?>
            </div>
        </div>
        <div class="col-md-2">
            <div class="control-group">
            <?php echo $this->form->getLabel('template'); ?>
            <div class="select-arrow select-arrow-white select-large"><?php echo $this->form->getInput('template'); ?></div>
            </div>
        </div>

        <?php if(intval($this->item->contact_to) === $userId) { ?>
        <div class="col-md-2">
            <div class="control-group">
            <?php echo $this->form->getLabel('state'); ?>
            <div class="select-arrow select-arrow-white select-large"><?php echo $this->form->getInput('state'); ?></div>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <div class="col-md-12">
        <div class="control-group">
            <?php echo $this->form->getInput('message'); ?>
        </div>
    </div>
    
	<div class="fullwidth">
    
        <div class="col-md-6">
            <div class="control-group">
            	<?php echo $this->form->getLabel('upload'); ?>
            	<?php echo $this->form->getInput('upload'); ?>
            </div>
        </div>
        
        <div class="col-md-4 pull-right">
            <div class="control-group">
            	<?php echo $this->form->getLabel('priority'); ?>
                <?php echo $this->form->getInput('priority'); ?>
            </div>
        </div>
        
    </div>
    
    <div class="fullwidth">
        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
        
        <div class="fields-items"></div>
        <div class="field-add-container clearfix">
            <?php echo Secretary\HTML::_('datafields.listOptions', 'messages' ); ?>
            <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
        </div>

    </div>
        
    <input type="hidden" name="task" value="" />
    <?php echo $this->form->getInput('id'); ?>
    <input type="hidden" value="<?php echo $this->item->catid; ?>" name="catid" id="catid" />
    <?php echo JHtml::_('form.token'); ?>
</form>
    
<?php } ?>

</div>
</div>

<?php if( $this->tmpl == 'component') { ?>
<div class="secretary-modal-bottom">
    <div class="btn modal-close" ><?php echo JText::_('COM_SECRETARY_TOOLBAR_CLOSE'); ?></div>
</div>
<?php } ?>


<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>