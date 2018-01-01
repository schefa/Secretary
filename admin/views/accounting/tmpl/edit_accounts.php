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
$business	= Secretary\Application::company('currency,taxvalue');
$currency	= $business['currency'];
$extension	= 'bookings';

$this->datafields = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);

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

<?php
$fields	= (isset($this->datafields['fields'])) ? $this->datafields['fields'] : '';
$javaScript = 'Secretary.printFields( ['. $fields .'] );';
$this->document->addScriptDeclaration($javaScript);
?>
