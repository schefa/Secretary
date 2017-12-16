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

$user = Secretary\Joomla::getUser();

$extension	= 'locations';
$this->areas = array(
		  'businesses' => JText::_('Standorte (Unternehmen)'),
		  'documents' => JText::_('Abteilungen (Dokumente)'),
		  'products' => JText::_('Lager (Produkte)'),
		  'times' => JText::_('Veranstaltungsorte (Zeiten)')
		);
?>

<div class="secretary-main-container">
<?php echo Secretary\HTML::_('datafields.item'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=location&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="location-form" class="form-validate">

    <div class="secretary-main-area entry-default">
    
        <div class="form-horizontal">
            
            <div class="secretary-toolbar fullwidth">
                <div class="secretary-title">
                    <span><a href="<?php echo Secretary\Route::create('locations'); ?>"> <?php echo JText::_('COM_SECRETARY_LOCATIONS'); ?></a>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;</span>
                    <span><?php echo $this->title; ?></span>
					<?php if($this->checkedOut == false && (\Secretary\Helpers\Access::edit('location',$this->item->id,$this->item->created_by))) { ?>
                    <a class="btn btn-saveentry" href="index.php?option=com_secretary&task=location.edit&id=<?php echo $this->item->id;?>"><?php echo JText::_('COM_SECRETARY_TOOLBAR_EDIT'); ?></a>
                    <?php } ?>
                </div>
            </div>
        
            <ul class="nav nav-tabs margin-bottom fullwidth" id="myTab" role="tablist">
                <li class="active">
                    <a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a>
                </li>
                
                <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
                
                <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
                <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
                <?php endif; ?>
                
            </ul>
            
            <div class="tab-content">   
                <div class="tab-pane form-horizontal active" id="home">
                
                    <div class="fullwidth margin-bottom">
                    
                        <div class="col-md-5">
                            
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                                <div class="controls"><?php echo $this->item->title; ?></div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
                                <div class="controls"><?php echo \Secretary\Helpers\Locations::$options[$this->item->extension]; ?></div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                                <div class="controls"><?php echo $this->item->catid;?></div>
                            </div>
                            
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                                <div class="controls"><?php echo $this->item->state;?></div>
                            </div>
                            
                        </div>
                        
                        <div class="col-md-6 pull-right">
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('street'); ?></div>
                                <div class="controls"><?php echo $this->item->street; ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('zip'); ?></div>
                                <div class="controls"><?php echo $this->item->zip; ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
                                <div class="controls"><?php echo $this->item->location; ?></div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('country'); ?></div>
                                <div class="controls"><?php echo $this->item->country; ?></div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-12">
                
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                            <div class="controls"><?php echo $this->item->upload; ?></div>
                        </div>
                        
                        <div class="control-group margin-bottom">
                            <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                            <div class="controls"><?php echo $this->item->description; ?></div>
                        </div>
                    </div>
                        
                    <?php 
					if(!empty($this->item->contacts))
					{
						echo "<ul>";
						foreach($this->item->contacts as $contact) {
							echo "<li>". $contact->firstname .' '. $contact->lastname ."</li>";
						}
						echo "</ul>";
					}
					?>
				</div>
			 
                <div class="tab-pane" id="fields">
                    <div class="fullwidth">
                        <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                                    
                        <div class="fields-items form-horizontal">
                            <?php if(!empty($this->item->fields) && ($fields = json_decode($this->item->fields, true))) { ?>
                                <?php foreach($fields as $field) { ?>
                                            
                                    <div class="control-group">
                                        <div class="control-label"><label><?php echo $field[1]; ?></label></div>
                                        <div class="controls"><?php echo $field[2]; ?></div>
                                    </div>
            
                                <?php } ?>
                            <?php } ?>
                        </div>
                    
                    </div>
                </div>
                
                <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
                <div class="tab-pane" id="permission">
                    <div class="control-group">
                        <?php echo $this->form->getLabel('access'); ?>
                        <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
                    </div>
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
                <?php endif; ?>
                
            </div>
            
		</div>
    </div>
      

<?php echo $this->form->getInput('id'); ?>

<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
  
</div>