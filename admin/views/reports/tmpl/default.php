<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

// Get Business Data
$user			= Secretary\Joomla::getUser();
$currency		= $this->business['currencySymbol'];
$listOrder		= $this->state->get('list.ordering');
$listDirn		= $this->state->get('list.direction');
$filterZeitraum = $this->state->get('filter.zeitraum');
$filterZeitraum = (empty($filterZeitraum)) ? 1 : $filterZeitraum;

if ($this->canDo->get('core.show')) { ?>
<div class="secretary-main-container dashboard">
		
<form action="<?php echo Secretary\Route::create('reports'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo \Secretary\Helpers\Access::getAccessMissingMsg($this->view); ?>

	<div class="secretary-main-area">

    	<div class="fullwidth">
        
            <div class="pull-left">
            <h2 class="documents-title">
            <span class="documents-title-first"><?php echo JText::_('COM_SECRETARY_REPORTS', true); ?></span>
            </h2>
            </div>
        
            <div class="pull-right">
            <div class="charts-toolbar fullwidth">
        
                <fieldset id="filter-bar">
                    <div class="row-fluid">
                                
                        <div class="pull-left btn-group select-arrow-control margin-right">
                        
                            <div class="secretary-input-prepend">
                                <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_PERIOD'); ?></div>
                                <div class="select-arrow select-arrow-white">
                                <select id="filter_zeitraum" class="form-control" onchange="this.form.submit()" name="filter_zeitraum"><?php echo JHtml::_('select.options', $this->zeitraumoptions, 'value', 'text', $filterZeitraum, true);?></select> 
                                </div>
                            </div>
                             
                        </div> 
                    
                        <div class="pull-left select-arrow-control margin-right">
                            
                            <div class="secretary-input-prepend">
                                <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_FILTER_DATE_START'); ?></div>
                                <?php echo JHTML::_('calendar', $this->state->get('reports_start_date'), 'reports_start_date', 'reports_start_date', "%Y-%m-%d", array('class'=>'form-control input-date')); ?>
                            </div>
                            
                        </div>
                        <div class="pull-left select-arrow-control">
                        
                            <div class="secretary-input-prepend">
                                <div class="secretary-add-on"><?php echo JText::_('COM_SECRETARY_FILTER_DATE_END'); ?></div>
                            	<?php echo JHTML::_('calendar', $this->state->get('reports_end_date'), 'reports_end_date', 'reports_end_date', "%Y-%m-%d", array('class'=>'form-control input-date')); ?>
                            </div>
                            
                        </div>
                        <div class="pull-left">
                            <button class="btn btn-primary btn-default btn-date-filter" type="submit"><?php echo JText::_('COM_SECRETARY_FILTER_DATE_GO'); ?></button>
						</div>  
                        
                        <div class="pull-right">
                            <?php // echo $this->pagination->getLimitBox(); ?>
                        </div>   
                        
                    </div> 
                </fieldset>
                
            </div>
           	</div>
            
        </div>

        <ul class="nav nav-tabs fullwidth" id="myTab" role="tablist">
            <li class="active"><a href="#documents" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_DOCUMENTS', true); ?></a></li>
            <li><a href="#contacts" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_SUBJECTS', true); ?></a></li>
            <li><a href="#products" role="tab" data-toggle="tab"><?php echo JText::_('COM_SECRETARY_PRODUCTS', true); ?></a></li>
            <li class="pull-right"><a rel="nofollow" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=900,height=480,directories=no,location=no'); return false;" title="Print" href="index.php?option=com_secretary&view=reports&layout=modal&tmpl=component&print=1&page=" class="btn btn-link"><span class="icon-print"></span><?php echo JText::_('COM_SECRETARY_PRINT'); ?></a></li>
        </ul>
        
        <div class="tab-content">
        
            <div class="tab-pane active" id="documents">
                <?php echo $this->loadTemplate('documents'); ?>
    		</div>
    
            <div class="tab-pane" id="contacts">
                <?php echo $this->loadTemplate('contacts'); ?>
            </div>
            
            <div class="tab-pane" id="products">
                <?php echo $this->loadTemplate('products'); ?>
            </div>
            
        </div>
		
	</div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
    
</form>
 

</div>

<?php } else { ?>
    <div class="alert alert-danger"><?php echo JText::_('JERROR_ALERTNOAUTHOR'); ?></div>
<?php } ?> 
 