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
 
$this->datafields   = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);

$user = JFactory::getUser(); 
?>

<div class="secretary-main-container">
<?php echo Secretary\HTML::_('datafields.item'); ?>
 
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=subject&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
    
	<div class="secretary-main-area">
    
        <div class="secretary-toolbar fullwidth">
            <div class="secretary-title">
            <div class="select-arrow-toolbar select-arrow"><?php echo $this->form->getInput('catid');?></div>
            <span>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<?php echo JText::_('COM_SECRETARY_SUBJECT'); ?></span>
            <?php $this->addToolbar(); ?>
            </div>
        </div>
    
        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
            <li><a href="#connections" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_CONNECTIONS', true); ?></a></li>
            <li><a href="#documents" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_DOCUMENTS', true); ?></a></li>
            <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
            <?php endif; ?>
            
            <?php if($this->item->id > 0 && COM_SECRETARY_PDF && !empty($this->defaultTemplate)) { ?>
            <li class="pull-right secretary-document-pdf-print">
                <a class="btn btn-link btn-pdf modal" rel="{size: {x: 900, y: 500}, handler:'iframe'}" href="<?php echo Secretary\Route::create('subject', array('format'=>'pdf', 'id'=> $this->item->id )); ?>" role="tab" data-toggle="tab"><img src="<?php echo JURI::root(); ?>/media/secretary/images/pdf-20.png" />&nbsp;<?php echo 'PDF'; ?></a>
            </li>
            <?php }  ?>
            
        </ul>
                
        <div class="tab-content">
        
            <div class="tab-pane active" id="home">
             
                <div class="fullwidth">
    
                    <div class="col-md-6 form-horizontal"> 
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('gender'); ?></div>
                            <div class="controls select-arrow select-arrow-white"><?php echo $this->form->getInput('gender'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('firstname'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('firstname'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('lastname'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('lastname'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('street'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('street'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('zip'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('zip'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('location'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('country'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('country'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('email'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('phone'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('phone'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('number'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('number'); ?></div>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-6">
                    
                        <div class="control-group">
                            <div class="control-label"><?php echo JText::_('COM_SECRETARY_ATTACHMENT'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                        </div>
                        <hr />
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                            <div class="controls select-arrow select-arrow-white"><?php echo $this->form->getInput('state'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
                            <input name="sync" type="checkbox" value="1" /><?php echo (' synchronization SECRETARY - Joomla user data'); ?>
                        </div>
                        <hr />
                        <div class="control-group"> 
                            <div class="control-label"><label><?php echo JText::_('COM_SECRETARY_TEMPLATE');?>&nbsp;<a href="index.php?option=com_secretary&view=templates&extension=subjects" target="_blank"><i class="fa fa-external-link"></i></a></label></div>
                            <div class="controls"><?php echo $this->itemtemplates; ?></div>
                        </div>
                        
                        <?php if(!empty($this->item->id) && $user->authorise('core.create','com_secretary.document')) {?>
                        <hr />
                        <div class="control-group">
                        
                            <?php $document = JText::_('COM_SECRETARY_DOCUMENT'); ?>
                            <h3 class="title"><?php echo JText::sprintf('COM_SECRETARY_CREATE_THIS', $document); ?></h3>
                            
                            <?php 
                            $documents	= array();
                            foreach($this->document_categories as $document) {
                                $documents[] = JHtml::_('select.option', $document->id, JText::_($document->title)); }
                            ?>
                            <select id="add_new_document" data-subject="<?php echo $this->item->id;?>">
                                <?php echo JHtml::_('select.options', $documents, 'value', 'text');?>
                            </select>
                            <div id="add_document" class="btn btn-primary"><?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                            <script>
                            (function($){
                                $('#add_document').click(function(){
                                    var subjectId = $('#add_new_document').data('subject');
                                    var documentId = $('#add_new_document').val();
                                    var url = 'index.php?option=com_secretary&view=document&layout=edit&catid='+documentId+'&subject='+subjectId;
                                    window.location.href = url;
                                })
                            })(jQuery);
                            </script>
                        </div>
                        <?php } ?>
                        
                    </div>
                        <hr />
                        
                        <div class="fullwidth">
                            <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                            
                            <div class="fields-items"></div>
                            <div class="field-add-container clearfix">
                                <?php echo Secretary\HTML::_('datafields.listOptions', 'subjects' ); ?>
                                <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                            </div>
                            
                        </div>
                        
                </div>
                
                <hr />
                
            </div>
            
            <div class="tab-pane" id="connections">
                <?php echo $this->loadTemplate('connections'); ?>
			</div>
            <div class="tab-pane" id="documents">
                <?php if(!empty($this->item->documents)) echo $this->loadTemplate('documents'); ?>
			</div>
                
            <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
                <div class="tab-pane" id="permission">
                    <div class="container-fluid">
                        <?php echo $this->form->getInput('rules'); ?>
                    </div>
                </div>
            <?php endif; ?>
        
        </div>
        
	</div>

    <input type="hidden" value="<?php echo $this->item->catid; ?>" name="catid" id="catid" />
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>

<script type="text/javascript">
jQuery.noConflict();
jQuery( document ).ready(function( $ ) {
<?php if(isset($this->datafields['fields'])) :?>
	var secretary_fields = [<?php echo $this->datafields['fields'];?>];
<?php else : ?>
	var secretary_fields = [];
<?php endif;?>
	Secretary.Fields( secretary_fields );
});
</script>

</div>
