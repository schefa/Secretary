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

$user = Secretary\Joomla::getUser();
 
$this->datafields   = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
?>

<div class="secretary-main-container">
<?php echo Secretary\HTML::_('datafields.item'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=location&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">

    <div class="secretary-main-area form-horizontal">
    
        <div class="secretary-toolbar fullwidth">
            <div class="secretary-title">
            <div class="select-arrow-toolbar select-arrow"><?php echo $this->form->getInput('catid');?></div>
            <span>&nbsp;<i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->title; ?></span>
            <?php $this->addToolbar(); ?>
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
                            <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                        </div>
                        
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('currency'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('currency'); ?></div>
                        </div>
                        
                        <?php if(!isset( $this->extension )) { ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('extension'); ?></div>
                                <div class="controls">
                                    <div class="select-arrow select-large select-arrow-white">
                                        <select name="jform[extension]" id="jform_extension">
                                            <?php echo JHtml::_('select.options', \Secretary\Helpers\Locations::$options, 'id', 'title', $this->item->extension, true);?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="jform[extension]" value="<?php echo $this->extension; ?>" />
                        <?php } ?>
                        
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
                            <div class="controls">
                            	<div class="select-arrow select-large select-arrow-white"><?php echo $this->form->getInput('state'); ?></div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-6 pull-right">
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
                    </div>
                </div>
    
                <div class="col-md-12">
            
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                    </div>
                    
                    <div class="control-group margin-bottom">
                        <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
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
                    
                    <div class="fields-items"></div>
                    <div class="field-add-container clearfix">
            			<?php echo Secretary\HTML::_('datafields.listOptions', 'locations' ); ?>
                        <div id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></div>
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

<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>


<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>

</div>