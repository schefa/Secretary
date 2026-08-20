<?php
 
defined('_JEXEC') or die;
 
// Get Business Data
$user			= \Secretary\Joomla::getUser();
$currency		= $this->business['currencySymbol'];
$listOrder		= $this->state->get('list.ordering');
$listDirn		= $this->state->get('list.direction');
$filterZeitraum = $this->state->get('filter.zeitraum');
$filterZeitraum = (empty($filterZeitraum)) ? 1 : $filterZeitraum;

$this->document->addStylesheet(SECRETARY_MEDIA_PATH.'/css/secretary.print.css?'.time());
$this->document->addScript(SECRETARY_MEDIA_PATH.'/js/secretary.charts.js?'.time());
?>


<div id="section-to-print">
    
    <div class="print-toolbar">
        <div id="pop-print" class="btn hidden-print">
        	<a onclick="window.print();return false;" href="#"><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRINT'); ?></a>
        </div>
    </div>

    <h2><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENTS')?></h2>
    <?php echo $this->loadTemplate('documents'); ?>
    <div class="page-break"></div>
    
    <h2><?php echo \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECTS')?></h2>
    <?php echo $this->loadTemplate('contacts'); ?>

</div>
