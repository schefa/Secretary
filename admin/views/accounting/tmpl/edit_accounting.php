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

$user       = Secretary\Joomla::getUser();
$business	= Secretary\Application::company();
$currency	= $business['currency'];
$extension	= 'accountings';

$this->datafields		= \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields); 
?>

<ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
    <li class="active"><a href="#home" role="tab" data-toggle="tab"><?php echo JText::_('JDETAILS', true); ?></a></li>
    <li><a href="#fields" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_FIELDS', true); ?></a></li>
</ul>
 
<div class="tab-content">

    <div class="tab-pane active" id="home">

        <div class="fullwidth">
        
            <div class="row-fluid">
                <div class="col-md-4 control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
                <div class="col-md-4 control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('entry_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('entry_id'); ?></div>
                </div>
                <div class="col-md-4 control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('upload'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('upload'); ?></div>
                </div>
            </div>
            
            <hr />
            
            <div class="row-fluid">   
                <div class="col-md-6 secretary-document-accounts-soll">
                    <h3><?php echo JText::_('COM_SECRETARY_SOLL'); ?></h3>
                    <div class="secretary-acc-rowlist-s"></div>
                    <div class="secretary-acc-add add-s btn" counter="0" data-type="s">+</div>
                </div>
                <div class="col-md-6 secretary-document-accounts-haben">
                    <h3><?php echo JText::_('COM_SECRETARY_HABEN'); ?></h3>
                    <div class="secretary-acc-rowlist-h"></div>
                    <div class="secretary-acc-add add-h btn" counter="0" data-type="h">+</div>
                </div>					
            </div>
            
        </div>
        
    </div>

    <div class="tab-pane" id="fields">
        <div class="fullwidth">
            <h3><?php echo JText::_('COM_SECRETARY_FIELDS'); ?></h3>
            
            <div class="fields-items"></div>
            <div class="field-add-container clearfix">
                <?php echo Secretary\HTML::_('datafields.listOptions', $extension ); ?>
                <button id="field-add" counter="<?php echo 0 + $this->datafields['count']; ?>"><span class="fa fa-plus"></span> <?php echo JText::_('COM_SECRETARY_NEW'); ?></button>
            </div>

        </div>
    </div>
    
</div>

<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>