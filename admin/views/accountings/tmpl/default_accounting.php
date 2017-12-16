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

// Import CSS
$user	    = Secretary\Joomla::getUser();
$userId	    = $user->get('id');
$business	= Secretary\Application::company();
$currency 	= $business['currency'];

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

?>

	<div class="alert alert-warning"><?php echo JText::_('COM_SECRETARY_ACCOUNTING_INFO'); ?></div>
<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%" class="hidden-phone">
            <?php echo Secretary\HTML::_('status.checkall'); ?><span class="lbl"></span>
            </th>
            <th class='nowrap'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_CREATED', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JText::_('COM_SECRETARY_BOOKING'); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_TOTAL', 'a.total', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_DOCUMENT', 'a.entry_id', $listDirn, $listOrder); ?>
            </th>
            <th class='center'>
            <?php echo JHtml::_('grid.sort',  'COM_SECRETARY_STATUS', 'a.state', $listDirn, $listOrder); ?>
            </th>
        </tr>
    </thead>
    <tfoot class="table-list-pagination">
    <?php 
        if(isset($this->items[0]))
            $colspan = count(get_object_vars($this->items[0]));
        else
            $colspan = 10;
    ?>
    <tr>
        <td colspan="<?php echo $colspan ?>">
            <div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
            <div class="pull-right">
            <select id="filter_published" class="filter_category" onchange="this.form.submit()" name="filter_published">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', $this->states, 'value', 'text', $this->state->get('filter.state'), true);?>
            </select>
            </div>
            <div class="pull-right clearfix">
            <select name="sortTable" id="sortTable" class="" onchange="Joomla.orderTable()"><option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option><?php echo JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);?></select>
            </div>
            <div class="pull-right limit-box clearfix"><span class="pagination-filter-text"><?php echo JText::_('COM_SECRETARY_LIMIT');?></span><?php echo $this->pagination->getLimitBox(); ?></div>
        </td>
    </tr>
    </tfoot>
    <tbody>
    <?php foreach ($this->items as $i => $item) : 
            
            $canChange = false; $canCheckin = false; $canEdit = false;
            if($user->authorise('core.edit', 'com_secretary.accounting')) {
                $canEdit = true; $canChange	= true; $canCheckin = true;
            }
            if(!$canCheckin) $canCheckin = $user->authorise('core.admin', 'com_secretary');
            if(!$canChange) $canChange = $user->authorise('core.edit.state', 'com_secretary.accounting');
            
            ?>
        <tr class="row<?php echo $i % 2; ?>">
            
            <td class="center hidden-phone">
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <span class="lbl"></span>
            </td>
            
            <td><?php echo $item->created; ?></td>
            
            <?php /*?><td>
                <?php if ($canEdit) : ?>
                    <a class="hasTooltip" data-original-title="<?php echo JText::_('COM_SECRETARY_CLICK_TO_EDIT'); ?>"  href="<?php echo JRoute::_('index.php?option=com_secretary&task=accounting.edit&id='.(int) $item->id); ?>">
                    <?php echo $item->entry_id; ?></a>
                <?php else : ?>
                    <?php echo $item->entry_id; ?>
                <?php endif; ?>
            </td><?php */?>
            
            <td class="table-left-border">
                <div class="accountings-record">
                    <div class="accountings-record-soll">
                        <?php
                            $soll =  json_decode($item->soll,true);
                            foreach( $soll as $key => $val) {
                                echo '<div class="accountings-record-item">';
                                $konto = Secretary\Database::getQuery('accounts_system',intval($val[0]));
                                $kontoTitle = (isset($konto->title)) ? $konto->title : JText::_('COM_SECRETARY_UNKNOWN');
                                echo $kontoTitle . '&nbsp;&nbsp;<span class="secretary-acc-sum">'. $val[1].'</span>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    <div class="accountings-record-an">
                    an
                    </div>
                    <div class="accountings-record-haben">
                        <?php
                            $haben =  json_decode($item->haben,true);
                            foreach( $haben as $key => $val) {
                                echo '<div class="accountings-record-item">';
                                $konto = Secretary\Database::getQuery('accounts_system',intval($val[0]));
                                $kontoTitle = (isset($konto->title)) ? $konto->title : JText::_('COM_SECRETARY_UNKNOWN');
                                echo $kontoTitle . '&nbsp;&nbsp;<span class="secretary-acc-sum">'. $val[1].'</span>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
                
            </td>
            
            <td class="vbottom table-left-border"><?php echo Secretary\Utilities\Number::getNumberFormat($item->total,$currency); ?></td>
            <td class="table-left-border"><?php echo $item->entry_id; ?></td>
            
            <?php $state = array('title' => $item->status_title,'class' => $item->class,'description' => $item->tooltip,'icon' => $item->icon ); ?>
            <td class="center"><?php echo Secretary\HTML::_('status.state', $item->state, $i, 'accountings.', false, $state); ?></td>
            
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

