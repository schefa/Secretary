<?php
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$doc = \Joomla\CMS\Factory::getDocument();

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
   <?php /*?> <button onclick="window.print()">Drucken</button><?php */?>
    <h3><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PREVIEW'); ?></h3>
</div>

<div class="secretary-modal-contentt">
<div id="section-to-print">

	<div style="padding-left:<?php echo $margins[0]?>mm;padding-right:<?php echo $margins[1]?>mm;">

    <?php if (isset($this->defaultTemplate))
    {
        ?>
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
        
		<div style="padding-top:<?php echo $margins[2]?>mm;padding-bottom:<?php echo $margins[3]?>mm">
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
        </div>
        <?php echo \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer, array('subject'=>$this->item->subjectid), $this->item->templateInfoFields ); ?>
    <?php }
    else
    {
        echo '<div class="alert alert-warning">'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_NOTEMPLATE'). '</div>'; } ?>
    
    </div>
</div>
</div>