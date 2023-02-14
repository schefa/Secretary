<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */
 
// No direct access
defined('_JEXEC') or die; 

$user               = \Secretary\Joomla::getUser();
$this->datafields	= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
$owner              = Secretary\Database::getJDataResult('users',(int) $this->item->owner, 'name'); 
?>

<div class="secretary-main-container form-horizontal">
<?php echo Secretary\HTML::_('datafields.item'); ?>
                     
<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=business&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

	<div class="secretary-main-area">
    
    	<div class="secretary-toolbar fullwidth">
            <?php $this->addToolbar(); ?>
        </div>
     
        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
            <?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <li><a href="#permission" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PERMISSIONS', true); ?></a></li>
            <?php endif; ?>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane active" id="home">
    
                <fieldset class="adminform">
                
                    <div class="row-fluid">
                        <div class="col-md-9">
                            <div class="control-group">
							<?php echo $this->form->getLabel('title'); ?>
                            <?php echo $this->form->getInput('title'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><?php echo JText::_('COM_SECRETARY_BUSINESS_OWNER'); ?></label>
                            <?php echo $owner; ?>
                        </div>
                    </div>
                    
                    
                    <hr />
                    
                    <div class="row-fluid">
                    
                        <div class="col-md-6">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('address'); ?>
                            <?php echo $this->form->getInput('address'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('slogan'); ?>
                            <?php echo $this->form->getInput('slogan'); ?>
                            </div>
                            <div class="control-group">
                            <?php echo $this->form->getLabel('upload'); ?>
                            <?php echo $this->form->getInput('upload'); ?>
                            </div>
                        </div>
                        
                    </div>
                    
                    <hr />
                    
                    <div class="row-fluid">
                        <div class="col-md-12">
                        <h3><?php echo JText::_('COM_SECRETARY_BUSINESS_PREFERENCE'); ?></h3>
                        <p class="secretary-desc fullwidth"><?php echo JText::_('COM_SECRETARY_BUSINESS_PREFERENCE_DESC'); ?></p>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="col-md-3">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('currency'); ?>
                            <?php echo $this->form->getInput('currency'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('taxvalue'); ?>
                            <?php echo $this->form->getInput('taxvalue'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('taxPrepo'); ?>
                            <?php echo $this->form->getInput('taxPrepo'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="control-group">
                            <?php echo $this->form->getLabel('defaultNote'); ?>
                            <?php echo $this->form->getInput('defaultNote'); ?> 
                            </div>
                        </div>
                    </div>
                    <hr />
                    
                    <div class="row-fluid">
                        <div class="col-md-12">
                            <h3><?php echo JText::_('COM_SECRETARY_BUSINESS_DOCUMENTS'); ?></h3>
                            <p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_BUSINESS_DOCUMENTS_DESC'); ?></p>
                        </div>
                        <div class="col-md-3">
                        <h4><?php echo JText::_('COM_SECRETARY_DOCUMENTS'); ?></h4>
                        <?php echo JFormHelper::loadFieldType('CategoriesMultiselect', false)->getInput('documents', 'jform[selectedFolders][documents]', $this->item->selectedFolders['documents'] ); ?> 
                        </div>
                        <div class="col-md-3">
                        <h4><?php echo JText::_('COM_SECRETARY_SUBJECTS'); ?></h4>
                        <?php echo JFormHelper::loadFieldType('CategoriesMultiselect', false)->getInput('subjects', 'jform[selectedFolders][subjects]',  $this->item->selectedFolders['subjects']); ?> 
                        </div>
                        <div class="col-md-3">
                            <h4><?php echo JText::_('COM_SECRETARY_PRODUCTS'); ?></h4>
                            <?php echo JFormHelper::loadFieldType('CategoriesMultiselect', false)->getInput('products', 'jform[selectedFolders][products]',  $this->item->selectedFolders['products']); ?> 
                        </div>
                        <div class="col-md-3">
                            <h4><?php echo JText::_('COM_SECRETARY_MESSAGES'); ?></h4>
                            <?php echo JFormHelper::loadFieldType('CategoriesMultiselect', false)->getInput('messages', 'jform[selectedFolders][messages]',  $this->item->selectedFolders['messages']); ?> 
                        </div>
                    </div>
                    <hr />
                    
                    <div class="row-fluid">
                        <div class="col-md-4">
                            <h4><?php echo JText::_('COM_SECRETARY_REPORTS'); ?></h4>
                            <p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_BUSINESS_REPORTS_DESC'); ?></p>
                            <div class="control-group">
                            <label><?php echo JText::_('COM_SECRETARY_REPORTS_WINNING'); ?></label>
                            <?php echo $this->form->getInput('guv1'); ?> 
                            <p><?php echo JText::_('COM_SECRETARY_REPORTS_WINNING_DESC'); ?></p>
                            </div>
                            
                            <div class="control-group">
                            <label><?php echo JText::_('COM_SECRETARY_REPORTS_LOSING'); ?></label>
                            <?php echo $this->form->getInput('guv2'); ?> 
                            <p><?php echo JText::_('COM_SECRETARY_REPORTS_LOSING_DESC'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
                            <p class="secretary-desc"><?php echo JText::_('COM_SECRETARY_BUSINESS_FIELDS_DESC');?></p>
                            
                            <div class="fields-items"></div>
                            <div class="field-add-container clearfix">
                            	<?php echo Secretary\HTML::_('datafields.listOptions', 'businesses' ); ?>  
                                <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
                            </div>
                        </div>
                    </div> 
                    
                    <div class="alert alert-succes"><?php echo JText::_('COM_SECRETARY_BUSINESS_FORM_INTRO');?></div>
                    
                </fieldset>
                
            </div>
        
        	<?php if ($user->authorise('core.admin', 'com_secretary')) : ?>
            <div class="tab-pane" id="permission">
            	<fieldset>
                	<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
            </div>
        	<?php endif; ?>
        
        </div>
        
    </div>
    
	<?php echo $this->form->getInput('id'); ?>
	<?php echo $this->form->getInput('home'); ?>
	<?php echo $this->form->getInput('created_by'); ?>
    <input class="form-control inputbox" type="hidden" value="<?php echo $this->item->createdEntry;;?>" name="jform[createdEntry]" id="jform_createdEntry" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>

<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>