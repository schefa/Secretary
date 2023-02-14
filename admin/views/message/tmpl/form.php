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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

$catid = isset($this->item->messagesCategory) ? (int) $this->item->messagesCategory->id : 0;
?>

<div class="secretary-message <?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" itemscope itemtype="http://schema.org/Person">

    <?php
    if(!empty($this->item->template->id))
        $this->defaultTemplate	= \Secretary\Helpers\Templates::getTemplate($this->item->template->id); 
    
    if(isset($this->defaultTemplate)) { 
        echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text,array('subject'=>$this->item->id),array('fields'=>$this->item->template->fields,'messagesCategory'=>$this->item->messagesCategory,'tid'=>$this->item->template->id), 'form');
    } else { 
        echo '<div class="alert alert-warning">'. JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE'). '</div>';
    } 
    ?>
    
	<?php if ($this->params->get('allow_vcard')) :	?>
		<?php echo JText::_('COM_SECRETARY_DOWNLOAD_INFORMATION_AS');?>
		<a href="<?php echo JRoute::_('index.php?option=com_secretary&amp;view=message&amp;id=' . $this->item->id . '&amp;bid=' . $this->item->business . '&amp;cid=' . $catid . '&amp;format=vcf'); ?>">
		<?php echo JText::_('COM_SECRETARY_VCARD');?></a>
	<?php endif; ?>
    
</div>
