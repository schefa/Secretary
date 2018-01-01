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
defined( '_JEXEC' ) or die( 'Restricted access' );

$doc = JFactory::getDocument();

$margins = explode(";",$this->defaultTemplate->margins);
?>

<div id = "scoped-content">  
<style type="text/css" media="print">
@media print {
  body * {
    visibility: hidden;
  }
  #section-to-print, #section-to-print * {
    visibility: visible;
  }
  #section-to-print {
    position: absolute;
    left: 0;
    top: 0;
	width: 100%;
      all: initial;
      * {
        all: unset;
      }
  }
}
#section-to-print {
  all: initial;
  * {
    all: unset;
  }
}
<?php echo $this->defaultTemplate->css; ?>
</style>


</div>
<div class="secretary-modal-top">
    <button class="modal-close" data-dismiss="modal" type="button">x</button>
   <?php /*?> <button onclick="window.print()">Drucken</button><?php */?>
    <h3><?php echo JText::_('COM_SECRETARY_PREVIEW'); ?></h3>
</div>

<div class="secretary-modal-contentt">
<div id="section-to-print">

	<div style="padding-left:<?php echo $margins[0]?>mm;padding-right:<?php echo $margins[1]?>mm;">

    <?php if(isset($this->defaultTemplate)) { ?>
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
        
		<div style="padding-top:<?php echo $margins[2]?>mm;padding-bottom:<?php echo $margins[3]?>mm">
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
        </div>
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
    <?php } else { echo '<div class="alert alert-warning">'. JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE'). '</div>'; } ?>
    
    </div>
</div>
</div>
<div class="secretary-modal-bottom">
    <div class="btn modal-close" ><?php echo JText::_('COM_SECRETARY_TOOLBAR_CLOSE'); ?></div>
</div>