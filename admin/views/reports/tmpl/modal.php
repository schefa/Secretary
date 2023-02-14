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
        	<a onclick="window.print();return false;" href="#"><?php echo JText::_('COM_SECRETARY_PRINT'); ?></a>
        </div>
    </div>

    <h2><?php echo JText::_('COM_SECRETARY_DOCUMENTS')?></h2>
    <?php echo $this->loadTemplate('documents'); ?>
    <div class="page-break"></div>
    
    <h2><?php echo JText::_('COM_SECRETARY_SUBJECTS')?></h2>
    <?php echo $this->loadTemplate('contacts'); ?>

</div>
