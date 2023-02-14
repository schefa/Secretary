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

// Get Business Data
$entitySelect = $this->state->params->get('entitySelect');

$user		= Secretary\Joomla::getUser();
$canCheckin	= $user->authorise('core.manage', 'com_secretary');

$extension = (!empty($this->extension)) ? '&extension='. $this->extension : '';
?>

<div class="secretary-main-container">

<form action="<?php echo JRoute::_('index.php?option=com_secretary&view=items'. $extension); ?>" method="post" name="adminForm" id="adminForm">

<div class="secretary-main-area">

<?php if($entitySelect == 0 && ( $this->extension == 'entity')) { ?>
    <div class="alert alert-message"><?php echo JText::_('COM_SECRETARY_ENTITIES_NOENTITYSELECT'); ?></div>
<?php } ?>

    <div class="row-fluid fullwidth">
   
    <div class="pull-left secretary-toolbar">
            <div class="select-arrow-toolbar">
            <h2 class="documents-title">
            	<span class="documents-title-first"><?php echo $this->title; ?></span>
            </h2>
            </div>
            <div class="select-arrow-toolbar-next">
                &#10095; 
            </div>
        <?php $this->addToolbar(); ?>
    </div>
        <?php if( $this->extension == 'status' || $this->extension == 'fields') { ?>
            <div class="pull-right select-arrow">
                <select id="filter_section" onchange="this.form.submit()" name="filter_section"> 
                    <?php echo JHtml::_('select.options', $this->sections, 'id', 'title', $this->section, true);?>
                </select>
            </div>
        <?php } ?>
    </div>
       
    <?php if (empty($this->items) && $this->extension !== 'uploads') : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('COM_SECRETARY_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
        <?php echo $this->loadTemplate($this->extension)?>
    <?php endif;?>
   
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="extension" value="<?php echo $this->extension; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>

</div>