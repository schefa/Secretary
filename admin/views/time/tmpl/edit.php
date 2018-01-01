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