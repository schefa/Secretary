<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

$user		= Secretary\Joomla::getUser();
$canEdit	= $user->authorise('core.edit', 'com_secretary');

$business	= Secretary\Application::company();
$currency	= $business['currency'];
?>
	<div class="alert alert-warning"><?php echo JText::_('COM_SECRETARY_ACCOUNTING_INFO'); ?></div>
<div class="secretary-accounts-table">
        
    <div class="secretary-accounts-thead clearfix">
        <div class="input-clean">
        <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
        </div>
        <?php /*?><div>
        <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_PID', 'a.kid', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
        </div><?php */?>
        <div>
        <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_NR', 'a.nr', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
        </div>
        <div>
        <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_ACCOUNTS_TITLE', 'a.title', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
        </div>
        <div>
        <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_SOLL', 'a.soll', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
        </div>
        <div>
        <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_HABEN', 'a.haben', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
        </div>
    </div>
    
    <div class="row-fluid">
    <?php foreach ($this->items as $kontotyp => $parents) { ?>
    	<div class="col-md-6">
        
    	<?php if (!empty($kontotyp)) { echo '<h3>'.JText::_('COM_SECRETARY_ACCOUNT_'.strtoupper($kontotyp)).'</h3>'; } ?>
        
    	<?php foreach ($parents as $parent_id => $konten) { ?>
        
        	<div class="secretary-accounts-table-account-parent clearfix">
            	<div class="secretary-accounts-table-account-parent-title">
				<?php $parent =  Secretary\Database::getQuery('accounts_system',$parent_id); echo $parent->nr .' - '. $parent->title; ?>
                </div>
                <div class="secretary-accounts-table-account-amount"><?php echo Secretary\Utilities\Number::getNumberFormat( abs($konten['soll'] - $konten['haben'])); ?></div>
            </div>
                    
            <?php foreach ($konten as $i => $konto) { ?>
            <?php if(is_numeric($i)){ ?>
                <div class="secretary-accounts-table-account clearfix">
                
                    <div class="secretary-accounts-table-account-check center hidden-phone input-clean">
                        <?php echo JHtml::_('grid.id', $i, $konto->id); ?>
                        <span class="lbl"></span>
                    </div>
                    <div class="secretary-accounts-table-account-title">
                    <?php if ($canEdit) : ?>
                        <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&view=accounting&layout=edit&id='.(int) $konto->id .'&extension='.$this->extension ); ?>">
                        <?php echo $konto->nr.' '.$konto->title; ?></a>
                    <?php else : echo $konto->nr.' '.$konto->title; endif; ?>
                    </div>
                    
                    <div class="secretary-accounts-table-account-amount">
                        <div class="secretary-accounts-table-account-soll"><?php echo Secretary\Utilities\Number::getNumberFormat($konto->soll); ?></div>
                        <div class="secretary-accounts-table-account-haben"><?php echo Secretary\Utilities\Number::getNumberFormat($konto->haben); ?></div>
                    </div>
                    
                </div>
            <?php } } } ?>
        </div>
    <?php } ?>
    </div>
    
    
    <div class="secretary-accounts-table-footer clearfix">
        <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
        <div class="pull-right clearfix">
        <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $this->state->get('list.ordering'));?></select>
        </div>
        <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
    </div>

</div>
