<?php
 
defined('_JEXEC') or die;

$user = Secretary\Joomla::getUser();
 
$this->item->extension = (isset($this->item->extension)) ? $this->item->extension : $this->extension; 

echo Secretary\HTML::_('datafields.item');

?>

<div class="secretary-main-container">

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_secretary&view=template&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

	<div class="row-fluid">
    <div class="secretary-main-area secretary-main-area-blank">
    
        <div class="form-horizontal">
                
            <div class="secretary-toolbar fullwidth">
                <div class="secretary-title">
                    <span><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE'); ?>:&nbsp;<?php echo $this->title; ?>&nbsp;<i class="fa fa-angle-right"></i></span>
                    <?php $this->addToolbar(); ?>
                </div>
            </div>
                
            <ul class="nav nav-tabs margin-bottom fullwidth" id="myTab" role="tablist">
                <li class="nav-item">
                    <a href="#home" role="tab" data-bs-toggle="tab" class="nav-link active"><?php echo \Joomla\CMS\Language\Text::_('JDETAILS', true); ?></a>
                </li>
                
                <li class="nav-item"><a href="#fields" role="tab" data-bs-toggle="tab" class="nav-link"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELDS', true); ?></a></li>

                <?php if ($user->authorise('core.admin', 'com_secretary'))
                {
                    ?>
                <li class="nav-item"><a href="#permission" role="tab" data-bs-toggle="tab" class="nav-link"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
                <?php } ?>
                
			<?php if ($this->item->id)
			{
                ?>
                <?php if (COM_SECRETARY_PDF)
                {
                    ?>
            	<li class="nav-item pull-right secretary-document-pdf-print">
                    <a class="btn btn-danger" data-joomla-dialog='{"popupType": "iframe", "src": "<?php echo Secretary\Route::create('template', array('format'=>'pdf', 'id'=> $this->item->id )); ?>"}'><img src="<?php echo SECRETARY_MEDIA_PATH; ?>/images/pdf-20.png" />&nbsp;<?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PREVIEW'); ?></a>
                </li>
                <?php }  ?>
            <?php } ?>
            </ul>
            
            <div class="tab-content">   
                <div class="tab-pane active" id="home">
                
                    <div class="row row-multi">
                        <div class="col-md-3">
                            <?php echo $this->form->getLabel('title'); ?>
                            <?php echo $this->form->getInput('title'); ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->form->getLabel('catid'); ?>
                            <?php echo $this->form->getInput('catid'); ?>
							<input type="hidden" name="catid" value="<?php echo $this->item->catid;?>" />
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->form->getLabel('extension'); ?>
                            <?php echo $this->form->getInput('extension'); ?>
                        <?php 
                        if ( isset( $this->extension ))
                        {
                            ?>
                        	<input type="hidden" name="jform[extension]" value="<?php echo $this->extension; ?>" />
                        <?php } ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->form->getLabel('state'); ?>
                            <?php echo $this->form->getInput('state'); ?>
                        </div>
                        
                        <div class="col-md-2">
                            <?php echo $this->form->getLabel('business'); ?>
                            <?php echo $this->form->getInput('business'); ?>
                        </div>
                        
                        <div class="col-md-1">
                            <?php echo $this->form->getLabel('language'); ?>
                            <?php echo $this->form->getInput('language'); ?>
                        </div>
                    </div>
                    <div class="secretary-designer-hr"></div>
                    <div class="row-fluid">
                        
                        <div class="col-md-2">
                    		<?php echo $this->loadTemplate('sidebar');?>
                        </div>
        
                        <div class="col-md-10">
                                    
                            <div class="secretary-designer-css">
                            	<h4>CSS</h4>
                                <?php echo $this->form->getInput('css'); ?>
                            </div>
                            <div class="secretary-designer-hr"></div>
                            
                            <div class="margin-bottom">
                                <div class="btn-group secretary-template-designer">
                                    <div id="secretary-template-designer-toggle" class="btn designer">Designer</div>
                                    <div class="btn html active">HTML Code</div>
                                </div>
                            </div>
                                
                            <div class="margin-bottom"><?php echo $this->form->getInput('header'); ?></div>

                            <div style="display:none;" id="secretary-canvas" class="secretary-canvas-shadow"><?php echo $this->item->text; ?></div>
                            <textarea style="display:block;" name="jform[text]" placeholder="Body" id="secretary-canvas-input"><?php echo $this->item->text; ?></textarea>

                            <div class="margin-top"><?php echo $this->form->getInput('footer'); ?></div>
                            
                        </div>  

                    </div>
                    
				</div>
			 
                <div class="tab-pane" id="fields">
                    <div class="fullwidth">
                        <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELDS'); ?></h3>
                        
                        <div class="fields-items"></div>
                        <div class="field-add-container clearfix">
                			<?php echo Secretary\HTML::_('datafields.listOptions', "templates" ); ?>
                            <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_NEW'); ?></div>
                        </div>

                    </div>
                </div>
                
                <?php if ($user->authorise('core.admin', 'com_secretary'))
                {
                    ?>
                <div class="tab-pane" id="permission">
                    <div class="control-group">
                        <?php echo $this->form->getLabel('access'); ?>
                        <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
                    </div>
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
                <?php } ?>
                
            </div>
            
        </div>
    
    </div>
        
	</div>

<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="jform[dpi]" value="<?php echo $this->item->dpi; ?>" id="secretary_dpi" /> 
<input type="hidden" name="task" value="" />
<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>

</div>


